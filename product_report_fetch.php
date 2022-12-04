<?php

//stock_report_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();
$product_id=$_POST['product_id'];

$today = date("Y-m-d");
$query .= "
	select * from product
";

if(isset($_POST['product_id']) && $_POST['product_id'] != '')
{
	$query .= "
		WHERE product.product_id = '".$product_id."'
	";
}



if(isset($_POST["stock"]))
{
	 $query .= ' ORDER BY '.$_POST['stock']['0']['column'].' '.$_POST['stock']['0']['dir'].' ';
}
else
{
	 $query .= ' ORDER BY product_id DESC';
}

if($_POST["length"] != -1)
{
	$query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}


$statement = $connect->prepare($query);
$statement->execute();
$filtered_rows = $statement->rowCount();
$result = $statement->fetchAll();
$data = array();
$i=0;

foreach($result as $row)
{
	
	$i++;
	$sub_array = array();
	$sub_array[] = $i;
	$sub_array[] = $row['product_name'];
	$sub_array[] = $row['as_on_date'];
	$sub_array[] = 'Brought';
	$sub_array[] = 'Forward';
	$sub_array[] = $row['init_stock_quantity'];
	
	$data[] = $sub_array;
	
	// to retrieve data from the both the views
	
	$query1 = "select * 
	from (
      select product_id,date_of_entry,stock_in,purchase_uom,'p' as type
      from productwise_stock_in_details
	  where product_id = '".$product_id."'
      UNION ALL
      select product_id,inventory_order_date,stock_out,sale_uom,'s' as type
      from productwise_stock_out_details
	  where product_id = '".$product_id."'
     ) derived
	order by product_id,date_of_entry ASC";
	$statement1 = $connect->prepare($query1);
	$statement1->execute();
	$filtered_rows1 = $statement1->rowCount();
	$result1 = $statement1->fetchAll();
	$quantity = $row['init_stock_quantity'];
	foreach($result1 as $row1)
	{
		$i++;
		$sub_array = array();
		$sub_array[] = $i;
		$sub_array[] = $row['product_name'];
		$sub_array[] = $row1['date_of_entry'];
		$ps_uom = $row1['purchase_uom'];
		if($ps_uom=="Box")
			$size = 18;
		else
			$size = 1;
		if($row1["type"]=="p")
		{
		/*	if($ps_uom=="Box")
				$sub_array[] = $row1['stock_in'] . " Box";
			else	*/
			$sub_array[] = ($size * $row1['stock_in']);
			$sub_array[] = '--';
			$quantity +=($size*$row1['stock_in']);
		}
		else
		{
			$sub_array[] = '--';
			$sub_array[] = ($size * $row1['stock_in']);
			$quantity -=($size*$row1['stock_in']);
		}
		$sub_array[] = $quantity;
		$data[] = $sub_array;
	}
	
/*	// display of stock in details from here
	
	$query1 = "SELECT *
			FROM productwise_stock_in_details 
			where product_id = '".$product_id."'
			order by date_of_purchase asc";
	$statement1 = $connect->prepare($query1);
	$statement1->execute();
	$filtered_rows1 = $statement1->rowCount();
	$result1 = $statement1->fetchAll();
	$quantity = $row['init_stock_quantity'];
	foreach($result1 as $row1)
	{
		$i++;
		$sub_array = array();
		$sub_array[] = $i;
		$sub_array[] = $row1['supplier_name'];
		$sub_array[] = $row1['date_of_purchase'];
		$sub_array[] = $row1['stock_in'];
		$sub_array[] = '--';
		
		$sale_uom = $row1['purchase_uom'];
	//	$product_unit = $row1['product_unit'];
	//	$pc = $row1['pc'];
		
	//	if($sale_uom == $product_unit)
			$quantity +=$row1['stock_in'];
	//	else
	//		$quantity -= round(($row1['quantity']/$pc),2);
		$sub_array[] = $quantity;
		$data[] = $sub_array;
	}
	
	
	
	// display of stock out details from here
	
	$query1 = "SELECT *
			FROM productwise_stock_out_details 
			where product_id = '".$product_id."'
			order by inventory_order_date asc";
	$statement1 = $connect->prepare($query1);
	$statement1->execute();
	$filtered_rows1 = $statement1->rowCount();
	$result1 = $statement1->fetchAll();
	$quantity = $row['init_stock_quantity'];
	foreach($result1 as $row1)
	{
		$i++;
		$sub_array = array();
		$sub_array[] = $i;
		$sub_array[] = $row1['customer_name'];
		$sub_array[] = $row1['inventory_order_date'];
		$sub_array[] = '--';
		$sub_array[] = $row1['stock_out'];
		
		$sale_uom = $row1['sale_uom'];
	//	$product_unit = $row1['product_unit'];
	//	$pc = $row1['pc'];
		
	//	if($sale_uom == $product_unit)
			$quantity -=$row1['stock_out'];
	//	else
	//		$quantity -= round(($row1['quantity']/$pc),2);
		$sub_array[] = $quantity;
		$data[] = $sub_array;
	}
	
	*/
	
	
	

	
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("select * from productwise_stock_out_details");
	$statement->execute();
	//print $statement->rowCount();
	return $statement->rowCount();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  get_total_all_records($connect),
	"recordsFiltered" 	=> 	$filtered_rows,
	"data"    			=> 	$data
);	

echo json_encode($output);

?>