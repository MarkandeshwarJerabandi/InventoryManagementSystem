<?php

//purchase_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
		SELECT *
		FROM purchase_invoice 
		INNER JOIN supplier_details ON supplier_details.supplier_id = purchase_invoice.supplier_id
		INNER JOIN purchase_details ON purchase_details.purchase_id = purchase_invoice.purchase_id
		WHERE invoice_cash_bill_no != 0
		";

//if($_SESSION['type'] == 'user')
//{
//	$query .= 'entered_by = "'.$_SESSION["user_id"].'" AND ';
//}

if(isset($_POST["purchase"]["value"]))
{
	$query .= ' AND (purchase_invoice.purchase_id LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_invoice.date_of_purchase LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_invoice.invoice_cash_bill_no LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_details.season_year LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_details.sugar_cane_variety LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_details.harvester_name LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR (purchase_details.vehicle_owner_name LIKE "%'.$_POST["purchase"]["value"].'%" ';
	$query .= ' OR purchase_invoice.bill_amount LIKE "%'.$_POST["search"]["purchase"].'%") ';
}

if(isset($_POST["purchase"]))
{
	$query .= ' ORDER BY '.$_POST['purchase']['0']['column'].' '.$_POST['purchase']['0']['dir'].' ';
}
else
{
	$query .= ' ORDER BY purchase_invoice.purchase_id DESC ';
}

if($_POST["length"] != -1)
{
	$query .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);
$statement->execute();// or die("error in fetch" . print_r($statement->errorInfo()));
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$status = '';
	if($row['purchase_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$invoice_id=$row['invoice_cash_bill_no'];
	$sub_array = array();
			$sub_array[] = $row['purchase_id'];
			$sub_array[] = $row['invoice_cash_bill_no'];
			$sub_array[] = $row['firm_name'];
			$sub_array[] = $row['date_of_purchase'];
			$sub_array[] = $row['season_year'];
			$sub_array[] = $row['sugar_cane_variety'];
			$sub_array[] = $row['harvester_name'];
			$sub_array[] = $row['vehicle_owner_name'];
			$sub_array[] = $row['vehicle_no'];
			$sub_array[] = $row['loaded_weight'];
			$sub_array[] = $row['empty_weight'];
			$sub_array[] = $row['gross_weight'];
			$sub_array[] = $row['deduction'];
			$sub_array[] = $row['net_weight'];
			$sub_array[] = $row['rate_per_ton'];
			$sub_array[] = $row['bill_amount'];
			$sub_array[] = $row['advance_paid'];
			$sub_array[] = $row['balance_amount'];
			$sub_array[] = $row['purchase_status'];
			
	$sub_array[] = $status;
	$sub_array[] = '<a href="view_purchase.php?invoice_id='.$invoice_id.'&pdf=1&purchase_id='.$row["purchase_id"].'" class="btn btn-info btn-xs">View PDF</a>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["purchase_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["purchase_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["purchase_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM purchase_invoice");
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