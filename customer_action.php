<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_brand_list($connect, $_POST['category_id']);
	}
	
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO customer_details (customer_name, firm_name, address, place, customer_type, GSTIN, contact_no, email_id, zipcode, entered_by, customer_status) 
		VALUES (:customer_name, :firm_name, :address, :place,:customer_type, :GSTIN, :contact_no, :email_id, :zipcode, :entered_by, :customer_status)
		";
		if(isset($_POST['GSTIN']))
			$GSTIN = $_POST['GSTIN'];
		else
			$GSTIN='';
		if($_POST['customer_type']=='Unregistered')
			$GSTIN='';
		else
			$GSTIN=$_POST['GSTIN'];
		if(isset($_POST['email_id']))
			$email_id=$_POST['email_id'];
		else
			$email_id='';
		if(isset($_POST['firm_name']))
			$firm_name=$_POST['firm_name'];
		else
			$firm_name='';
		if(isset($_POST['zipcode']))
			$zipcode=$_POST['zipcode'];
		else
			$zipcode='';
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_name'		=>	$_POST['customer_name'],
				':firm_name'			=>	$firm_name,
				':address'				=>	$_POST['address'],
				':place'				=>	$_POST['place'],
				':customer_type'		=>	$_POST['customer_type'],
				':GSTIN'				=>	$GSTIN,
				':contact_no'			=>	$_POST['contact_no'],
				':email_id'				=>	$email_id,
				':zipcode'				=>	$zipcode,
				':entered_by'			=>	$_SESSION["user_id"],
				':customer_status'		=>	'active',
			)
		);
		$result = $statement->fetchAll();
		$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
		$customer_id = $statement2->fetchColumn();				
		if($customer_id)
		{
		//	echo 'Customer Added';
			
			$outstanding_date = $_POST['outstanding_date'];
			$current_outstanding = $_POST['current_outstanding'];
			if($current_outstanding>0)
			{
				$query1 = "
					INSERT INTO customer_payment (customer_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
					VALUES (:customer_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
				";
				$statement1 = $connect->prepare($query1);
				$statement1->execute(
				array(
					':customer_id'						=>	$customer_id,
					':total_amount_to_be_paid'			=>	$current_outstanding,
					':amount_paid'						=>	0,
					':balance'							=>  $current_outstanding,
					':entered_by'						=>	$_SESSION["user_id"],
					':date_of_entry'					=>	date('Y-m-d'),
					)
				);
				$result1 = $statement1->fetchAll();
				$statement3 = $connect->query("SELECT LAST_INSERT_ID()");
				$payment_id = $statement3->fetchColumn();
				if($payment_id)
				{
					$query5 = "
						INSERT INTO inventory_order (user_id, inventory_order_total, inventory_order_date, customer_id, bill_type, payment_status, inventory_order_status, inventory_order_created_date) 
						VALUES (:user_id, :inventory_order_total, :inventory_order_date, :customer_id, :bill_type, :payment_status, :inventory_order_status, :inventory_order_created_date)
					";
					$statement5 = $connect->prepare($query5);
					$statement5->execute(
					array(
						':user_id'							=>	$_SESSION["user_id"],
						':inventory_order_total'			=>	$current_outstanding,
						':inventory_order_date'				=>	$outstanding_date,
						':customer_id'						=>	$customer_id,
						':bill_type'						=>  'BroughtForward',
						':payment_status'					=>  'credit',
						':inventory_order_status'			=>  'active',
						':inventory_order_created_date'		=>	date('Y-m-d'),
						)
					);
					$result5 = $statement5->fetchAll();
					$statement6 = $connect->query("SELECT LAST_INSERT_ID()");
					$inventory_order_id = $statement6->fetchColumn();
					if($inventory_order_id)
					{
						$query2 = "
							INSERT INTO customer_payment_details (payment_id, customer_id, inventory_order_id, bill_amount, amount_paid, balance, mode_of_payment,
							cheque_number, cheque_date, cheque_bank_name, date_of_payment) 
							VALUES (:payment_id, :customer_id, :inventory_order_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :cheque_number,
							:cheque_date, :cheque_bank_name, :date_of_payment)
						";
						$statement4 = $connect->prepare($query2);
						$statement4->execute(
						array(
							':payment_id'						=>	$payment_id,
							':customer_id'						=>	$customer_id,
							':inventory_order_id'				=>	$inventory_order_id,
							':bill_amount'						=>	$current_outstanding,
							':amount_paid'						=>	0,
							':balance'							=>  $current_outstanding,
							':mode_of_payment'					=>	'BroughtForward',
							':cheque_number'					=>	0,
							':cheque_date'						=>	'0000-00-00',
							':cheque_bank_name'					=>	'',
							':date_of_payment'					=>	$outstanding_date,
							)
						);
						$result4 = $statement4->fetchAll();
						if(isset($result4))
						{
							echo ' Customer Records Added...';
							if($_POST['customer_type']=="Outlet")
							{
								$query2 = "
									INSERT INTO user_details (user_email, user_password, user_name, user_type, user_status) 
									VALUES (:user_email, :user_password, :user_name, :user_type, :user_status)
								";
								$statement4 = $connect->prepare($query2);
								$statement4->execute(
								array(
									':user_email'						=>	$_POST['customer_name'],
									':user_password'						=>	'123456',
									':user_name'				=>	$_POST['customer_name'],
									':user_type'						=>	'user',
									':user_status'						=>	'active'
									)
								);
							}
						}
					}
				}
			}
			
		}
	}
	if($_POST['btn_action'] == 'customer_details')
	{
		$query = "
		SELECT * FROM customer_details 
		INNER JOIN user_details ON user_details.user_id = customer_details.entered_by 
		WHERE customer_details.customer_id = '".$_POST["customer_id"]."'
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
			$query1 = "
				SELECT bill_amount,date_of_payment FROM customer_payment_details
				WHERE customer_id = '".$_POST["customer_id"]."'
			";
			$statement1 = $connect->prepare($query1);
			$statement1->execute();
			$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
			$status = '';
			if($row['customer_status'] == 'active')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Customer Name</td>
				<td>'.$row["customer_name"].'</td>
			</tr>
			<tr>
				<td>Firm Name</td>
				<td>'.$row["firm_name"].'</td>
			</tr>
			<tr>
				<td>Address</td>
				<td>'.$row["address"].'</td>
			</tr>
			<tr>
				<td>Consignee Details</td>
				<td>'.$row["place"].'</td>
			</tr>
			<tr>
				<td>ZipCode</td>
				<td>'.$row["zipcode"].'</td>
			</tr>
			<tr>
				<td>Customer Type</td>
				<td>'.$row["customer_type"].'</td>
			</tr>
			<tr>
				<td>GSTIN</td>
				<td>'.$row["GSTIN"].'</td>
			</tr>
			<tr>
				<td>Contact Number</td>
				<td>'.$row["contact_no"].'</td>
			</tr>
			<tr>
				<td>Email ID</td>
				<td>'.$row["email_id"].'</td>
			</tr>
			<tr>
				<td>Current Outstanding Amount as on '.$result1['date_of_payment'] .'</td>
				<td>'.$result1["bill_amount"].'</td>
			</tr>
			<tr>
				<td>Entered By</td>
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
		SELECT * FROM customer_details WHERE customer_id = :customer_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_id'	=>	$_POST["customer_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$query1 = "
				SELECT bill_amount,date_of_payment FROM customer_payment_details
				WHERE customer_id = '".$_POST["customer_id"]."'
			";
			$statement1 = $connect->prepare($query1);
			$statement1->execute();
			$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
			$rowcount = $statement1->rowCount();
			
			$output['customer_id'] = $row['customer_id'];
			$output['customer_name'] = $row['customer_name'];
			$output["firm_name"] = $row["firm_name"];
			$output['address'] = $row['address'];
			$output['place'] = $row['place'];
			$output['zipcode'] = $row['zipcode'];
			$output['customer_type'] = $row['customer_type'];
			$output['GSTIN'] = $row['GSTIN'];
			$output['contact_no'] = $row['contact_no'];
			$output['email_id'] = $row['email_id'];
			if($rowcount>0)
			{
				$output['current_outstanding'] = $result1['bill_amount'];
				$output['outstanding_date'] = $result1['date_of_payment'];
			}
			else
			{
				$output['current_outstanding'] = 0;
				$output['outstanding_date'] = '';
			}
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['customer_type']=='Unregistered')
			$GSTIN='';
		else
			$GSTIN=$_POST['GSTIN'];
		if(isset($_POST['email_id']))
			$email_id=$_POST['email_id'];
		else
			$email_id='';
		if(isset($_POST['firm_name']))
			$firm_name=$_POST['firm_name'];
		else
			$firm_name='';
		if(isset($_POST['zipcode']))
			$zipcode=$_POST['zipcode'];
		else
			$zipcode='';
		$query = "
		UPDATE customer_details 
		set  
		customer_id = :customer_id,
		customer_name = :customer_name,
		firm_name = :firm_name,
		address = :address,
		place = :place,
		zipcode = :zipcode, 
		customer_type = :customer_type,
		GSTIN = :GSTIN, 
		contact_no = :contact_no, 
		email_id = :email_id 
		WHERE customer_id = :customer_id
		";
		$customer_id = $_POST['customer_id'];
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_id'			=>	$_POST['customer_id'],
				':customer_name'		=>	$_POST['customer_name'],
				':firm_name'			=>	$firm_name,
				':address'				=>	$_POST['address'],
				':place'				=>	$_POST['place'],
				':customer_type'		=>	$_POST['customer_type'],
				':GSTIN'				=>	$GSTIN,
				':contact_no'			=>	$_POST['contact_no'],
				':email_id'				=>	$email_id,
				':zipcode'				=>	$zipcode,
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Customer Details Edited';
	/*		$outstanding_date = $_POST['outstanding_date'];
			$current_outstanding = $_POST['current_outstanding'];
			if($current_outstanding>0)
			{
				$query7 = "
						SELECT payment_id, bill_amount FROM customer_payment_details
						where customer_id = '".$customer_id."' and mode_of_payment= 'BroughtForward'
				";
				$statement7 = $connect->prepare($query7);
				$statement7->execute();
				$result7 = $statement7->fetch(PDO::FETCH_ASSOC);
				$filtered_rows7 = $statement7->rowCount();
				if($filtered_rows7>0)
				{
					$query1 = "
						UPDATE customer_payment SET 
						customer_id = '".$customer_id."', 
						total_amount_to_be_paid = total_amount_to_be_paid - '".$result7['bill_amount']."' + '".$current_outstanding."',
						amount_paid = amount_paid, 
						balance = balance - '".$result7['bill_amount']."' + '".$current_outstanding."', 
						entered_by = '".$_SESSION['user_id']."',
						date_of_entry='".date('Y-m-d')."'
					";
					$statement1 = $connect->prepare($query1);
					$statement1->execute();
					$result1 = $statement1->fetchAll();
					$payment_id = $result7['payment_id'];
					if($payment_id)
					{
						$query2 = "
							UPDATE customer_payment_details SET 
							payment_id=:payment_id,
							customer_id=:customer_id, 
							inventory_order_id=:inventory_order_id, 
							bill_amount=:bill_amount, 
							amount_paid=:amount_paid, 
							balance=:balance, 
							mode_of_payment=:mode_of_payment,
							cheque_number=:cheque_number, 
							cheque_date=:cheque_date,
							cheque_bank_name=:cheque_bank_name,
							date_of_payment=:date_of_payment
						";
						$statement4 = $connect->prepare($query2);
						$statement4->execute(
						array(
							':payment_id'						=>	$payment_id,
							':customer_id'						=>	$customer_id,
							':inventory_order_id'				=>	0,
							':bill_amount'						=>	$current_outstanding,
							':amount_paid'						=>	0,
							':balance'							=>  $current_outstanding,
							':mode_of_payment'					=>	'BroughtForward',
							':cheque_number'					=>	0,
							':cheque_date'						=>	'0000-00-00',
							':cheque_bank_name'					=>	'',
							':date_of_payment'					=>	$outstanding_date,
							)
						);
						$result4 = $statement4->fetchAll();
						if(isset($result4))
						{
							echo ' and Customer payment details Updated...';
						}
					}
				}
			}	*/
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
		UPDATE customer_details 
		SET customer_status = :customer_status 
		WHERE customer_id = :customer_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_status'	=>	$status,
				':customer_id'		=>	$_POST["customer_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Customer status change to ' . $status;
		}
	}
}


?>