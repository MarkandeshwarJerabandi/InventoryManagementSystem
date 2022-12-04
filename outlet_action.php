<?php

//outlet_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO outlet (outlet_name) 
		VALUES (:outlet_name)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':outlet_name'	=>	$_POST["outlet_name"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'outlet Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM outlet WHERE outlet_id = :outlet_id";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':outlet_id'	=>	$_POST["outlet_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['outlet_name'] = $row['outlet_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE outlet set outlet_name = :outlet_name  
		WHERE outlet_id = :outlet_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':outlet_name'	=>	$_POST["outlet_name"],
				':outlet_id'		=>	$_POST["outlet_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'outlet Name Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';	
		}
		$query = "
		UPDATE outlet 
		SET outlet_status = :outlet_status 
		WHERE outlet_id = :outlet_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':outlet_status'	=>	$status,
				':outlet_id'		=>	$_POST["outlet_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'outlet status change to ' . $status;
		}
	}
}

?>