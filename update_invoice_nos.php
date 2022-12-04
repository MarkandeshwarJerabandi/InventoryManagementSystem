<?php

include('database_connection.php');


$sql = "ALTER TABLE `inventory_order` ADD `invoice_no` INT NULL DEFAULT NULL AFTER `inventory_order_id`;";

$statement2 = $connect->prepare($sql);
$statement2->execute();


$query = '';

$query .= "
	SELECT * FROM inventory_order 
	WHERE inventory_order.bill_type !='BroughtForward'
	order by inventory_order_id
";

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$invoice_id = 0;

foreach($result as $row)
{
	$query1 = "UPDATE inventory_order
				SET invoice_no = $invoice_id + 1
				where inventory_order_id='".$row['inventory_order_id']."'
			  ";
	$statement1 = $connect->prepare($query1);
	$statement1->execute();
	$invoice_id++;
}

?>