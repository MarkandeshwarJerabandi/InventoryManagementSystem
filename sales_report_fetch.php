<?php

//sales_report_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

	$query .= "
		select * from
		customer_details, inventory_order, inventory_order_product
		where customer_details.customer_id = inventory_order.customer_id and inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	";

	if(isset($_POST['from_date'],$_POST['to_date']) && $_POST['from_date']!='' && $_POST['to_date']!='')
	{
		$query .= '
					and (inventory_order.inventory_order_date between "'.$_POST['from_date'].'" and "'.$_POST['to_date'].'")
		';
	}

   if(isset($_POST["search"]["value"]))
   {
	   $query .= 'and (inventory_order.inventory_order_date LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or customer_details.customer_name LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or inventory_order.inventory_order_id LIKE "%'.$_POST["search"]["value"].'%" ';
		
	   $query .= 'or customer_details.GSTIN LIKE "%'.$_POST["search"]["value"].'%" )';
   }
   
   
//$query .= 'group by(purchase_invoice.purchase_id)';

 if(isset($_POST["sales"]))
 {
	 $query .= 'ORDER BY '.$_POST['sales']['0']['column'].' '.$_POST['sales']['0']['dir'].' ';
 }
 else
 {
	 $query .= 'group by(inventory_order.inventory_order_id)
	 order by(inventory_order.inventory_order_id) ';
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
$Tvalue0=0;
$Tvalue2=0;
$Tvalue6=0;
$Tvalue9=0;
$Tvalue14=0;
$Tvalue=0;

$TSGST0=0;
$TSGST2=0;
$TSGST6=0;
$TSGST9=0;
$TSGST14=0;

$TCGST0=0;
$TCGST2=0;
$TCGST6=0;
$TCGST9=0;
$TCGST14=0;

$TGST=0;
foreach($result as $row)
{
	$status = '';
	$i++;
	$sub_array = array();
	$sub_array[] = $i;
	$sub_array[] = $row['invoice_no'];
	$sub_array[] = $row['inventory_order_date'];
	$sub_array[] = $row['customer_name'] . '  <br / >' . $row['address'];
	$sub_array[] = $row['GSTIN'];
	
	$inventory_order_id = $row['inventory_order_id'];
	
//	$purchase_id = $row['purchase_id'];
//	$sub_query = '';
	$sub_query = 'select inventory_order.*,inventory_order_product.product_id,inventory_order_product.price,inventory_order_product.quantity, inventory_order_product.sale_uom,
	product.tax_status,product.SGST as sgst_tax,product.CGST as cgst_tax, product.size as size,product.product_unit as product_unit
	from
	inventory_order, inventory_order_product, product
	where inventory_order.inventory_order_id = inventory_order_product.inventory_order_id and inventory_order_product.product_id = product.product_id
	and inventory_order.inventory_order_id = ' . $inventory_order_id;
	$statement1 = $connect->prepare($sub_query);
	$statement1->execute();
	$result1 = $statement1->fetchAll();
	$filtered_rows1 = $statement1->rowCount();
	$value0=0;
	$value2=0;
	$value6=0;
	$value9=0;
	$value14=0;
	$value=0;
	$SGST0=0;
	$SGST2=0;
	$SGST6=0;
	$SGST9=0;
	$SGST14=0;
	$CGST0=0;
	$CGST2=0;
	$CGST6=0;
	$CGST9=0;
	$CGST14=0;
	$TSGST=0;
	$TCGST=0;
	$GST=0;
	if($filtered_rows1>0)
	{
		foreach($result1 as $row1)
		{
			if($row1['sale_uom']=='Box')
				$size = 18;
			else
				$size = 1;
			if($row1['sgst_tax']==0)
			{
				$value0 += ($size*$row1['quantity']*$row1['price']);
				$SGST0 += ($size*$row1['quantity']*$row1['price']*$row1['sgst_tax'])/100;
				$CGST0 += ($size*$row1['quantity']*$row1['price']*$row1['cgst_tax'])/100;
			}
			if($row1['sgst_tax']==2.5)
			{
				$value2 += ($size*$row1['quantity']*$row1['price']);
				$SGST2 += ($size*$row1['quantity']*$row1['price']*$row1['sgst_tax'])/100;
				$CGST2 += ($size*$row1['quantity']*$row1['price']*$row1['cgst_tax'])/100;
			}
			else if($row1['sgst_tax']==6)
			{
				$value6 += ($size*$row1['quantity']*$row1['price']);
				$SGST6 += ($size*$row1['quantity']*$row1['price']*$row1['sgst_tax'])/100;
				$CGST6 += ($size*$row1['quantity']*$row1['price']*$row1['cgst_tax'])/100;
			}	
			else if($row1['sgst_tax']==9)
			{
				$value9 += ($size*$row1['quantity']*$row1['price']);
				$SGST9 += ($size*$row1['quantity']*$row1['price']*$row1['sgst_tax'])/100;
				$CGST9 += ($size*$row1['quantity']*$row1['price']*$row1['cgst_tax'])/100;
			}	
			else if($row1['sgst_tax']==14)
			{
				$value14 += ($size*$row1['quantity']*$row1['price']);
				$SGST14 += ($size*$row1['quantity']*$row1['price']*$row1['sgst_tax'])/100;	
				$CGST14 += ($size*$row1['quantity']*$row1['price']*$row1['cgst_tax'])/100;	
			}
		}
		$SGST = $SGST0+$SGST2+$SGST6+$SGST9+$SGST14;
		$CGST = $CGST0+$CGST2+$CGST6+$CGST9+$CGST14;
		$value = $value0+$value2+$value6+$value9+$value14;
	}
	$sub_array[] = round($value,2);
	$sub_array[] = round($SGST,2);
	$sub_array[] = round($CGST,2);
	$sub_array[] =$row['pdiscount'];
	$sub_array[] = round($value+$SGST+$CGST);
	
	$Tvalue0 +=$value0;
	$Tvalue2 +=$value2;
	$Tvalue6 +=$value6;
	$Tvalue9 +=$value9;
	$Tvalue14 +=$value14;
	$Tvalue +=$value;
	
	$TSGST0+=$SGST0;
	$TSGST2+=$SGST2;
	$TSGST6+=$SGST6;
	$TSGST9+=$SGST9;
	$TSGST14+=$SGST14;
	$TSGST += $SGST;
	
	$TCGST0+=$CGST0;
	$TCGST2+=$CGST2;
	$TCGST6+=$CGST6;
	$TCGST9+=$CGST9;
	$TCGST14+=$CGST14;
	$TCGST += $CGST;
	$TGST += $SGST+$CGST;

	 $sub_array[] =$row['dispatch_through'];
	 $sub_array[] =$row['dispatch_no'];
	 $sub_array[] =$row['delivery_address'];
	 $sub_array[] =$row['delivery_station'];
	 $sub_array[] =$row['order_no'];
	 $sub_array[] =$row['order_date'];
	 $sub_array[] =$row['truck_no'];
	 $sub_array[] =$row['broker'];
	

	$data[] = $sub_array;
}


function get_total_all_records($connect)
{
	$statement = $connect->prepare("select * from customer_details, inventory_order, inventory_order_product
	where customer_details.customer_id = inventory_order.customer_id and inventory_order.inventory_order_id = inventory_order_product.inventory_order_id");
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