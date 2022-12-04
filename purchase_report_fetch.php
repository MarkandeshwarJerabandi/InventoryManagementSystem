<?php

//purchase_report_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
select * from
	supplier_details, purchase_invoice, purchase_details
	where supplier_details.supplier_id = purchase_invoice.supplier_id and purchase_invoice.purchase_id = purchase_details.purchase_id
	
";

if(isset($_POST['from_date'],$_POST['to_date']) && $_POST['from_date']!='' && $_POST['to_date']!='')
{
	$query .= '
				and (purchase_invoice.date_of_purchase between "'.$_POST['from_date'].'" and "'.$_POST['to_date'].'")
	';
}

   if(isset($_POST["search"]["value"]))
   {
	   $query .= 'and (purchase_invoice.date_of_purchase LIKE "%'.$_POST["search"]["value"].'%" ';
	 // $query .= 'and purchase_invoice.purchase_id LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or supplier_details.firm_name LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or purchase_invoice.invoice_cash_bill_no LIKE "%'.$_POST["search"]["value"].'%" ';
		   
	   $query .= 'or supplier_details.GSTIN LIKE "%'.$_POST["search"]["value"].'%" )';
   }
   
   
//$query .= 'group by(purchase_invoice.purchase_id)';

 if(isset($_POST["purchase"]))
 {
	 $query .= 'ORDER BY '.$_POST['purchase']['0']['column'].' '.$_POST['purchase']['0']['dir'].' ';
 }
 else
 {
	 $query .= 'group by(purchase_invoice.purchase_id)
	order by(date_of_purchase) ';
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
	$sub_array[] = $row['invoice_cash_bill_no'];
	$sub_array[] = $row['date_of_purchase'];
	$sub_array[] = $row['firm_name'] . '<br / >' . $row['address'];
	$sub_array[] = $row['GSTIN'];
	
	$purchase_id = $row['purchase_id'];
	
//	$purchase_id = $row['purchase_id'];
//	$sub_query = '';
	$sub_query = 'select purchase_invoice.*,purchase_details.product_id,purchase_details.unit_cost,purchase_details.quantity,
	product.tax_status,product.SGST as sgst_tax,product.CGST as cgst_tax
	from
	purchase_invoice, purchase_details, product
	where purchase_invoice.purchase_id = purchase_details.purchase_id and purchase_details.product_id = product.product_id
	and purchase_invoice.purchase_id = ' . $purchase_id;
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
			if($row1['tax_status']=='non-taxable')
			{
				$value0 += ($row1['quantity']*$row1['unit_cost']);
				$SGST0 += ($row1['quantity']*$row1['unit_cost']*$row1['sgst_tax'])/100;
				$CGST0 += ($row1['quantity']*$row1['unit_cost']*$row1['cgst_tax'])/100;
			}
			if($row1['sgst_tax']==2.5)
			{
				$value2 += ($row1['quantity']*$row1['unit_cost']);
				$SGST2 += ($row1['quantity']*$row1['unit_cost']*$row1['sgst_tax'])/100;
				$CGST2 += ($row1['quantity']*$row1['unit_cost']*$row1['cgst_tax'])/100;
			}
			else if($row1['sgst_tax']==6)
			{
				$value6 += ($row1['quantity']*$row1['unit_cost']);
				$SGST6 += ($row1['quantity']*$row1['unit_cost']*$row1['sgst_tax'])/100;
				$CGST6 += ($row1['quantity']*$row1['unit_cost']*$row1['cgst_tax'])/100;
			}	
			else if($row1['sgst_tax']==9)
			{
				$value9 += ($row1['quantity']*$row1['unit_cost']);
				$SGST9 += ($row1['quantity']*$row1['unit_cost']*$row1['sgst_tax'])/100;
				$CGST9 += ($row1['quantity']*$row1['unit_cost']*$row1['cgst_tax'])/100;
			}	
			else if($row1['sgst_tax']==14)
			{
				$value14 += ($row1['quantity']*$row1['unit_cost']);
				$SGST14 += ($row1['quantity']*$row1['unit_cost']*$row1['sgst_tax'])/100;	
				$CGST14 += ($row1['quantity']*$row1['unit_cost']*$row1['cgst_tax'])/100;	
			}
		}
		$SGST = $SGST0+$SGST2+$SGST6+$SGST9+$SGST14;
		$CGST = $CGST0+$CGST2+$CGST6+$CGST9+$CGST14;
		$value = $value0+$value2+$value6+$value9+$value14;
	}
	$sub_array[] = round($value,2);
	$sub_array[] = round($SGST,2);
	$sub_array[] = round($CGST,2);
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
	$TGST += $TSGST+$TCGST;
	
	// $sub_array[] =0;
	// $sub_array[] =0;
	// $sub_array[] =0;
	// $sub_array[] =0;
	// $sub_array[] = 'Total Amount in Rupees';
	// $sub_array[] = $Tvalue0;
	// $sub_array[] = $Tvalue2;
	// $sub_array[] = $Tvalue6;
	// $sub_array[] = $Tvalue9;
	// $sub_array[] = $Tvalue14;
	// $sub_array[] = $TSGST2;
	// $sub_array[] = $TSGST6;
	// $sub_array[] = $TSGST9;
	// $sub_array[] = $TSGST14;
	// $sub_array[] = $TCGST2;
	// $sub_array[] = $TCGST6;
	// $sub_array[] = $TCGST9;
	// $sub_array[] = $TCGST14;
	// $sub_array[] = $TGST;
	// $sub_array[] = $Tvalue + $TGST;

	$data[] = $sub_array;
}



function get_total_all_records($connect)
{
	$statement = $connect->prepare("select * from
	supplier_details, purchase_invoice, purchase_details
	where supplier_details.supplier_id = purchase_invoice.supplier_id and purchase_invoice.purchase_id = purchase_details.purchase_id");
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