<?php

//sales_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM inventory_order 
	INNER JOIN outlet ON outlet.outlet_id = inventory_order.customer_id
	WHERE inventory_order.bill_type !='BroughtForward'
";

if($_SESSION['type'] == 'user')
{
	$query .= 'user_id = "'.$_SESSION["user_id"].'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= 'and (inventory_order_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR outlet_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_total LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_status LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order_date LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY inventory_order_id ASC ';
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
$invoice_id = 0;
foreach($result as $row)
{
	$invoice_id=$invoice_id+1;
	$payment_status = '';

	if($row['payment_status'] == 'cash')
	{
		$payment_status = '<span class="label label-primary">Cash</span>';
	}
	else
	{
		$payment_status = '<span class="label label-warning">Credit</span>';
	}

	$status = '';
	if($row['inventory_order_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['inventory_order_id'];
	$sub_array[] = $row['invoice_no'];//$invoice_id;
	$sub_array[] = $row['outlet_name'];
	$sub_array[] = $row['inventory_order_total'];
	$sub_array[] = $row['inventory_order_date'];
	//$sub_array[] = '<a href="view_sales.php?order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-xs">View PDF</a>';
	$sub_array[] = '<a href="view_sales.php?invoice_id='.$invoice_id.'&order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-xs">View PDF</a>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["inventory_order_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM inventory_order");
	$statement->execute();
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