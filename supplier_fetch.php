<?php

//product_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM supplier_details 
	INNER JOIN user_details ON user_details.user_id = supplier_details.entered_by 
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE supplier_details.contact_person_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.firm_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.zipcode LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.GSTIN LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.contact_no LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.email_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR supplier_details.supplier_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY supplier_id DESC ';
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
	if($row['supplier_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['supplier_id'];
	$sub_array[] = $row['firm_name'];
	//$sub_array[] = $row['contact_person_name'];
	$sub_array[] = $row['address'];
	$sub_array[] = $row['contact_no'];
	$sub_array[] = $row['alt_contact_no'];
	$sub_array[] = $row['email_id'];
	$sub_array[] = $row['zipcode'];
	$sub_array[] = $row['GSTIN'];
	$sub_array[] = $row['bank_name'];
	$sub_array[] = $row['branch_name'];
	$sub_array[] = $row['bank_act_name'];
	$sub_array[] = $row['bank_act_no'];
	$sub_array[] = $row['IFSC_code'];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["supplier_id"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["supplier_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["supplier_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["supplier_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM supplier_details');
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