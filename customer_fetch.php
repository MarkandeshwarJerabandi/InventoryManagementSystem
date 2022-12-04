<?php

//product_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM customer_details 
	INNER JOIN user_details ON user_details.user_id = customer_details.entered_by 
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE customer_details.customer_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.firm_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.place LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.zipcode LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.GSTIN LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.contact_no LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.email_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_details.customer_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY customer_id DESC ';
}

if($_POST['length'] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$status = '';
	if($row['customer_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['customer_id'];
	$sub_array[] = $row['customer_name'];
	$sub_array[] = $row['firm_name'];
	$sub_array[] = $row['address'];
	$sub_array[] = $row['place'];
	$sub_array[] = $row['zipcode'];
	$sub_array[] = $row['customer_type'];
	$sub_array[] = $row['GSTIN'];
	$sub_array[] = $row['contact_no'];
	$sub_array[] = $row['email_id'];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["customer_id"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["customer_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["customer_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["customer_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM customer_details');
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