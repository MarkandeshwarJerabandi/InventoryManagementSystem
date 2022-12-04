<?php

//purchase_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM stock_entry_details 
	WHERE 
";

//if($_SESSION['type'] == 'user')
//{
//	$query .= 'entered_by = "'.$_SESSION["user_id"].'" AND ';
//}

if(isset($_POST["search"]["value"]))
{
	$query .= 'date_of_entry LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST["stock"]))
{
	$query .= 'ORDER BY '.$_POST['stock']['0']['column'].' '.$_POST['stock']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY stock_entry_id DESC ';
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
	
	$sub_array = array();
	$sub_array[] = $row['stock_entry_id'];
	$sub_array[] = $row['date_of_entry'];
	$sub_array[] = '<button type="button" name="update" id="'.$row["stock_entry_id"].'" class="btn btn-warning btn-xs update">View/Update</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM stock_entry_details");
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