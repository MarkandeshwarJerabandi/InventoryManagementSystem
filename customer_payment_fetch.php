<?php

//customer_payment_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM customer_payment 
	INNER JOIN customer_details ON customer_details.customer_id = customer_payment.customer_id
	WHERE 
";

if($_SESSION['type'] == 'user')
{
	$query .= 'entered_by = "'.$_SESSION["user_id"].'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(payment_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR customer_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR total_amount_to_be_paid LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR amount_paid LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR balance LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["payment"]))
{
	$query .= 'ORDER BY '.$_POST['payment']['0']['column'].' '.$_POST['payment']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY payment_id DESC ';
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
foreach($result as $row)
{
/*	$status = '';
	if($row['inventory_order_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}	*/
	$sub_array = array();
	$sub_array[] = $row['payment_id'];
	$sub_array[] = $row['customer_name'];
	$sub_array[] = $row['total_amount_to_be_paid'];
	$sub_array[] = $row['amount_paid'];
	$sub_array[] = $row['balance'];
	
	if($_SESSION['type'] == 'master')
	{
		$sub_array[] = get_user_name($connect, $row['entered_by']);
	}
	$sub_array[] = $row['date_of_entry'];
	$sub_array[] = '<a href="view_customer_payment.php?pdf=1&payment_id='.$row["payment_id"].'" class="btn btn-info btn-xs">View Payment Report</a>';
	if($row['balance']>0)
		$sub_array[] = '<a href="make_customer_payment.php?payment_id='.$row["payment_id"].'" class="btn btn-info btn-xs">Make Payment</a>';
	else
		$sub_array[] = '<a href="customer_payment.php" class="btn btn-info btn-xs">Make Payment</a>';
//	$sub_array[] = '<a href="make_customer_payment.php?payment_id='.$row["payment_id"].'" class="btn btn-info btn-xs">Update Payment</a>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM customer_payment");
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