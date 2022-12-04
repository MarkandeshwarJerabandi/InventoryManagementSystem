<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_supplier')
	{
		echo fill_supplier_list($connect);
	}
	
	if($_POST['btn_action'] == 'fill_sgst')
	{
		echo "0";
	}
	if($_POST['btn_action'] == 'fetch_category_name')
	{
		$query = "select * from category where category_id = '".$_POST['category_id']."'";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		$output['category_name'] = $result['category_name'];
		echo json_encode($output);
		
	}
	if($_POST['btn_action'] == 'Add')
	{
		$today = date('Y-m-d');
		$query = "
		INSERT INTO display_details (product_id,unit_display,unit_rate,total_display_amount,date_of_display,entered_by,last_modified_on) 
		VALUES (:product_id, :unit_display, :unit_rate, :total_display_amount,
		:date_of_display, :entered_by, :last_modified_on)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'			=>	$_POST['product_name'],
				':unit_display'			=>	$_POST['unit_display'],
				':unit_rate'			=>	$_POST['unit_rate'],
				':total_display_amount'	=>	$_POST['total_display_amount'],
				':date_of_display'		=>	$_POST['date_of_display'],
				':entered_by'			=>	$_SESSION["user_id"],
				':last_modified_on'		=>	$today
			)
		);
		$result = $statement->fetchAll();
		//$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
		//$product_id = $statement2->fetchColumn();
		if(isset($product_id))
		{
			echo 'Diplay details of Product and ';
			$query1 = "
				UPDATE stock_details SET
				product_id = :product_id,
				stock_available = stock_available - :unit_display
				where product_id = :product_id
				";
				$statement1 = $connect->prepare($query1);
				$result1  = $statement1->execute(
					array(
						':product_id'					=>	$_POST['product_name'],
						':unit_display'					=>	$_POST['unit_display']
					)
				);
			if(isset($result1))
			{
				echo 'Stock Details Updated';
			}
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM display_details
		INNER JOIN product ON product.product_id = display_details.product_id
		INNER JOIN category ON category.category_id = product.category_id
		INNER JOIN supplier_details ON supplier_details.supplier_id = product.supplier_id
		INNER JOIN user_details ON user_details.user_id = product.entered_by
		WHERE display_details.display_id = '".$_POST["display_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$status='Active';
			$output .= '
			<tr>
				<td>Display ID</td>
				<td>'.$row["display_id"].'</td>
			</tr>
			<tr>
				<td>Category</td>
				<td>'.$row["category_name"].'</td>
			</tr>
			<tr>
				<td>Product Name</td>
				<td>'.$row["product_name"].'</td>
			</tr>
			<tr>
				<td>Supplier Name</td>
				<td>'.$row["firm_name"].'</td>
			</tr>
			<tr>
				<td>Date of Display</td>
				<td>'.$row["date_of_display"].'</td>
			</tr>
			<tr>
				<td>Number of Units Displayed</td>
				<td>'.$row["unit_display"].'</td>
			</tr>
			<tr>
				<td>Unit Rate</td>
				<td>'.$row["unit_rate"].'</td>
			</tr>
			<tr>
				<td>Total Display Amount</td>
				<td>'.$row["total_display_amount"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.$row["user_name"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM display_details
		INNER JOIN product ON display_details.product_id = product.product_id
		INNER JOIN category ON category.category_id = product.category_id
		INNER JOIN supplier_details ON supplier_details.supplier_id = product.supplier_id
		INNER JOIN user_details ON user_details.user_id = product.entered_by
		WHERE display_details.display_id = :display_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':display_id'	=>	$_POST["display_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['category_id'] = $row['category_id'];
			$output['product_name'] = $row['product_id'];
			$output['supplier_id'] = $row['supplier_id'];
			$output["supplier_select_box"] = fill_supplier_list($connect);
			$output['HSN_code'] = $row['HSN_code'];
			$output['size'] = $row['size'];
			$output['date_of_display'] = $row['date_of_display'];
			$output['grade'] = strtoupper($row['grade']);
			$output['unit_display'] = $row['unit_display'];
			$output['unit_rate'] = $row['unit_rate'];
			$output['total_display_amount'] = $row['total_display_amount'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		if(isset($_POST['unit_conversion']))
			$unit_conversion = $_POST['unit_conversion'];
		else
			$unit_conversion = 0;
		$display_id = $_POST['display_id'];
		$q = "select unit_display from display_details where display_id = '".$display_id."' ";
		$statement = $connect->prepare($q);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		$old_unit_display = $result["unit_display"];
		$today = date("Y-m-d");
		$query = "
		UPDATE display_details
		set product_id = :product_id, 
		unit_display = :unit_display,
		unit_rate = :unit_rate,
		total_display_amount = :total_display_amount,
		date_of_display = :date_of_display,
		entered_by = :entered_by,
		last_modified_on = :last_modified_on
		WHERE display_id = :display_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'				=>	$_POST['product_name'],
				':unit_display'				=>	$_POST['unit_display'],
				':unit_rate'				=>	$_POST['unit_rate'],
				':total_display_amount'		=>	$_POST['total_display_amount'],
				':date_of_display'			=>	$_POST['date_of_display'],
				':entered_by'				=>	$_SESSION["user_id"],
				':last_modified_on'			=>	$today,
				'display_id'				=>  $display_id
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Display Details Edited';
			$q1 = "select product_id from stock_details where product_id = '".$_POST['product_name']."' ";
			$statement2 = $connect->prepare($q1);
			$result2  = $statement2->execute();
			$result3 = $statement2->fetchAll(PDO::FETCH_COLUMN,0);
			if($result3)
			{
				$query1 = "
				UPDATE stock_details SET
				product_id = :product_id,
				stock_available = stock_available - $old_unit_display + :stock_available
				where product_id = :product_id
				";
				$statement1 = $connect->prepare($query1);
				$result1  = $statement1->execute(
					array(
						':product_id'						=>	$_POST['product_name'],
						':stock_available'					=>	$_POST['unit_display']
					)
				);
			//	$result = $statement->fetchAll();
				if(isset($result1))
				{
					echo 'Stock Details updated';
				}
			}
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
	/*	$query = "
		DELETE from display_details 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$result = $statement->execute(
			array(
				':product_id'		=>	$_POST["product_id"]
			)
		);
		if(isset($result))
		{
			echo 'Display Details Deleted for ' . $_POST["product_id"];
		}	*/
		echo "Contact Admin to Delete Records";
		
	}
}


?>