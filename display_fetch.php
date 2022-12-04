<?php

//product_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM display_details
		INNER JOIN product ON product.product_id = display_details.product_id
		INNER JOIN category ON category.category_id = product.category_id
		INNER JOIN supplier_details ON supplier_details.supplier_id = product.supplier_id
		INNER JOIN user_details ON user_details.user_id = product.entered_by
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE supplier_details.firm_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR category.category_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.HSN_code LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.size LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.grade LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY product.product_id DESC ';
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
	$status='Active';
	$sub_array = array();
	$sub_array[] = $row['display_id'];
	$sub_array[] = $row['category_name'];
	$sub_array[] = $row['product_name'];
	$sub_array[] = $row['firm_name'];
	$sub_array[] = $row['HSN_code'];
	$sub_array[] = $row['size'];
	$sub_array[] = $row['grade'];
	$sub_array[] = $row['date_of_display'];
	$sub_array[] = $row["unit_display"];
	$sub_array[] = $row["unit_rate"];
	$sub_array[] = $row["total_display_amount"];
	$sub_array[] = $row['user_name'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["display_id"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["display_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["display_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["product_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM display_details');
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