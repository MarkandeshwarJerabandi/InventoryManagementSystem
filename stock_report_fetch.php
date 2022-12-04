<?php

//stock_report_fetch.php

date_default_timezone_set("Asia/Calcutta");
include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	select * from
	stock_details, product
	where stock_details.product_id = product.product_id 
";

$today = date("Y-m-d H:i:s");


if(isset($_POST["search"]["value"]))
{
	   $query .= 'and (product.product_name LIKE "%'.$_POST["search"]["value"].'%" )';
}
   
//$query .= 'group by(purchase_invoice.purchase_id)';



 if(isset($_POST["stock"]))
 {
	 $query .= 'ORDER BY '.$_POST['stock']['0']['column'].' '.$_POST['stock']['0']['dir'].' ';
 }
 else
 {
	 $query .= 'order by(stock_details.product_id)';
 }

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
$i=0;


foreach($result as $row)
{
	
	$unit_display = 0;
	$total_display_amount = 0;
	
	$i++;
	$sub_array = array();
	$sub_array[] = $i;
	$sub_array[] = $row['product_name'];
	
//	$opening_balance=0;

	$opening_balance = $row['init_stock_quantity'];		// line updated on 05-04-2019 for initial_stock_quantity
	$opurchase=0;
	$purchase=0;
	$purchase_amount=0;
	$opurchase_amount=0;
	$osales=0;
	$sales=0;
	$sales_amount=0;
	$osales_amount=0;
	$closing_balance=0;
	$closing_balance_amount=0;
	if(isset($_POST['from_date'],$_POST['to_date']) && $_POST['from_date']!='' && $_POST['to_date']!='')
	{
		$from_date = $_POST['from_date'];// . ' ' .date('H:i:s');
		$to_date = $_POST['to_date'];// . ' ' .date('H:i:s');
		//echo $from_date;
		
		$opurchase_query = 'select * from stock_entry_details,stock_entry_history,product 
						  where stock_entry_details.stock_entry_id=stock_entry_history.stock_entry_id and stock_entry_history.product_id = product.product_id
						  and stock_entry_history.product_id="'.$row['product_id'].'" 
						  and stock_entry_details.date_of_entry < "'.$from_date.'"';
		$ostatement1 = $connect->prepare($opurchase_query);
		$ostatement1->execute() or die("Error in Query" . print_r($ostatement1->errorInfo()));
		$oresult1 = $ostatement1->fetchAll();
		$ofiltered_rows1 = $ostatement1->rowCount();
		//echo $ofiltered_rows1;
		if($ofiltered_rows1>0)
		{
			foreach($oresult1 as $orow1)
			{
				$opurchase += $orow1['quantity'];
				//echo $opurchase;
				$GST = $orow1['SGST'] + $orow1['CGST'];
				//$opurchase_amount += round((($orow1['quantity'] * $orow1['unit_cost'])+($orow1['quantity'] * $orow1['unit_cost']*$GST)/100),2);
			}
		}
		
		$osales_query = 'select * from inventory_order_product, inventory_order 
						  where inventory_order_product.inventory_order_id=inventory_order.inventory_order_id
						  and inventory_order_product.product_id="'.$row['product_id'].'" 
						  and inventory_order.inventory_order_date < "'.$_POST['from_date'].'"';
		$ostatement2 = $connect->prepare($osales_query);
		$ostatement2->execute();
		$oresult2 = $ostatement2->fetchAll();
		$ofiltered_rows2 = $ostatement2->rowCount();
		if($ofiltered_rows2>0)
		{
			foreach($oresult2 as $orow2)
			{
				$osales += $orow2['quantity'];
				if($orow2['sale_uom']=="Box")
					$size = 18;
				else
					$size = 1;
				$osales_amount += round(($size*$orow2['quantity'] * ($orow2['price']+$orow2['tax'])),2);
			}
		}
		
		$purchase_query = 'select * from stock_entry_details,stock_entry_history,product 
						  where stock_entry_details.purchase_id=stock_entry_history.purchase_id and stock_entry_history.product_id = product.product_id
						  and stock_entry_history.product_id="'.$row['product_id'].'" 
						  and stock_entry_details.date_of_entry between "'.$from_date.'" and "'.$to_date.'"';
		$statement1 = $connect->prepare($purchase_query);
		$statement1->execute();
		$result1 = $statement1->fetchAll();
		$filtered_rows1 = $statement1->rowCount();
		if($filtered_rows1>0)
		{
			foreach($result1 as $row1)
			{
				$purchase += $row1['quantity'];
				$GST = $row1['SGST'] + $row1['CGST'];
				//$purchase_amount += round((($row1['quantity'] * $row1['unit_cost'])+($row1['quantity'] * $row1['unit_cost']*$GST)/100),2);
			}
		}
		$sales_query = 'select * from inventory_order_product, inventory_order 
						  where inventory_order_product.inventory_order_id=inventory_order.inventory_order_id
						  and inventory_order_product.product_id="'.$row['product_id'].'" 
						  and inventory_order.inventory_order_date between "'.$_POST['from_date'].'" and "'.$_POST['to_date'].'"';
		$statement2 = $connect->prepare($sales_query);
		$statement2->execute();
		$result2 = $statement2->fetchAll();
		$filtered_rows2 = $statement2->rowCount();
		if($filtered_rows2>0)
		{
			foreach($result2 as $row2)
			{
				if($row2['sale_uom']=="Box")
					$size = 18;
				else
					$size = 1;
			//	$sales += $row2['quantity'];
				if($row2['sale_uom']=="Box")
					$sales += ($size * $row2['quantity']);
				else
				{
					$p_query = 'select * from product
								where product_id="'.$row['product_id'].'"
								';
					$statement3 = $connect->prepare($p_query);
					$statement3->execute();
					$result3 = $statement3->fetchAll();
					$filtered_rows3 = $statement3->rowCount();
					if($filtered_rows3>0)
					{
						foreach ($result3 as $row3)
						{
							$sales += round(($size * $row2['quantity']),2);
						}	
					}
				}
				
				$sales_amount += round(($size*$row2['quantity'] * ($row2['price']+$row2['tax'])),2);
			}
		}
	}
	else
	{
		$purchase_query = 'select * from stock_entry_details,stock_entry_history,product 
						  where stock_entry_details.stock_entry_id=stock_entry_history.stock_entry_id and stock_entry_history.product_id = product.product_id
						  and stock_entry_history.product_id="'.$row['product_id'].'" 
						  and stock_entry_details.date_of_entry <= "'.$today.'"';
		$statement1 = $connect->prepare($purchase_query);
		$statement1->execute();
		$result1 = $statement1->fetchAll();
		$filtered_rows1 = $statement1->rowCount();
		if($filtered_rows1>0)
		{
			foreach($result1 as $row1)
			{
				$purchase += $row1['quantity'];
				//echo $row1['quantity'];
				$GST = $row1['SGST'] + $row1['CGST'];
				//$purchase_amount += round((($row1['quantity'] * $row1['unit_cost'])+($row1['quantity'] * $row1['unit_cost']*$GST)/100),2);
			}
		}
		$sales_query = 'select * from inventory_order_product, inventory_order
						  where inventory_order_product.inventory_order_id=inventory_order.inventory_order_id
						  and inventory_order_product.product_id="'.$row['product_id'].'"
						  and inventory_order.inventory_order_date <= "'.$today.'"';
		$statement2 = $connect->prepare($sales_query);
		$statement2->execute();
		$result2 = $statement2->fetchAll();
		$filtered_rows2 = $statement2->rowCount();
		if($filtered_rows2>0)
		{
			foreach($result2 as $row2)
			{
				
				if($row2['sale_uom']=="Box")
					$size = 18;
				else
					$size = 1;
			//	$sales += $row2['quantity'];
				if($row2['sale_uom']=="Box")
					$sales += ($size * $row2['quantity']);
				else
				{
					$p_query = 'select * from product
								where product_id="'.$row['product_id'].'"
								';
					$statement3 = $connect->prepare($p_query);
					$statement3->execute();
					$result3 = $statement3->fetchAll();
					$filtered_rows3 = $statement3->rowCount();
					if($filtered_rows3>0)
					{
						foreach ($result3 as $row3)
						{
							$sales += round(($size * $row2['quantity']),2);
						}	
					}
				}
				$sales_amount += round(($size * $row2['quantity'] * ($row2['price']+$row2['tax'])),2);
			}
		}
		
	}
	$opening_balance = $opening_balance + $opurchase - $osales;
	$sub_array[] = $opening_balance;// + $opurchase - $osales;
	$sub_array[] = $purchase;
	$sub_array[] = $sales;
	$sub_array[] = $sales_amount;
	
	$sub_array[] = round(($opening_balance+$purchase)-$sales,2);
	$sub_array[] = abs(($sales_amount-$purchase_amount));

	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("select * from
	stock_details, product
	where stock_details.product_id = product.product_id");
	$statement->execute();
	//print $statement->rowCount();
	return $statement->rowCount();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);	

echo json_encode($output);

?>