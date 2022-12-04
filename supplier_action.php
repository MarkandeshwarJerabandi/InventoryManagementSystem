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
		INSERT INTO supplier_details (firm_name,contact_person_name, address, contact_no, alt_contact_no, email_id, zipcode, GSTIN, bank_name,branch_name,
		bank_act_name, bank_act_no, IFSC_code, entered_by, supplier_status) 
		VALUES (:firm_name, :contact_person_name, :address, :contact_no, :alt_contact_no, :email_id, :zipcode, :GSTIN, :bank_name, :branch_name, :bank_act_name, 
		:bank_act_no, :IFSC_code, :entered_by, :supplier_status)
		";
		
		if(isset($_POST['alt_contact_no']))
			$alt_contact_no = $_POST['alt_contact_no'];
		else
			$alt_contact_no=0;
		if(isset($_POST['email_id']))
			$email_id=$_POST['email_id'];
		else
			$email_id='';
		if(isset($_POST['zipcode']))
			$zipcode=$_POST['zipcode'];
		else
			$zipcode='';
		if(isset($_POST['branch_name']))
			$branch_name=$_POST['branch_name'];
		else
			$branch_name='';
		$contact_person_name='';
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':firm_name'			=>	$_POST['firm_name'],
				':contact_person_name'		=>	$contact_person_name,
				':address'				=>	$_POST['address'],
				':contact_no'			=>	$_POST['contact_no'],
				':alt_contact_no'		=>	$alt_contact_no,
				':email_id'				=>	$email_id,
				':zipcode'				=>	$zipcode,
				':GSTIN'				=>	$_POST['GSTIN'],
				':bank_name'			=>	$_POST['bank_name'],
				':branch_name'			=>	$branch_name,
				':bank_act_name'		=>	$_POST['bank_act_name'],
				':bank_act_no'			=>	$_POST['bank_act_no'],
				':IFSC_code'			=>	$_POST['IFSC_code'],
				':entered_by'			=>	$_SESSION["user_id"],
				':supplier_status'		=>	'active',
			)
		);
		$result = $statement->fetchAll();
		$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
		$supplier_id = $statement2->fetchColumn();				
		if($supplier_id)
		{
			
			$outstanding_date = $_POST['outstanding_date'];
			$current_outstanding = $_POST['current_outstanding'];
			if($current_outstanding>0)
			{
				$query1 = "
					INSERT INTO supplier_payment (supplier_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
					VALUES (:supplier_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
				";
				$statement1 = $connect->prepare($query1);
				$statement1->execute(
				array(
					':supplier_id'						=>	$supplier_id,
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
						INSERT INTO purchase_invoice (supplier_id, date_of_purchase, invoice_cash_bill_no, bill_amount, SGST, CGST, total_amount, purchase_status,entered_by, date_of_entry) 
						VALUES (:supplier_id, :date_of_purchase, :invoice_cash_bill_no, :bill_amount, :SGST, :CGST, :total_amount, :purchase_status,:entered_by,:date_of_entry)
					";
					$statement5 = $connect->prepare($query5);
					$statement5->execute(
					array(
						':supplier_id'						=>	$supplier_id,
						':date_of_purchase'					=>	$outstanding_date,
						':invoice_cash_bill_no'				=>	0,
						':bill_amount'						=>  $current_outstanding,
						':SGST'								=>  0,
						':CGST'								=>  0,
						':total_amount'						=>  $current_outstanding,
						':purchase_status'					=>  'active',
						':entered_by'						=>	$_SESSION["user_id"],
						':date_of_entry'					=>	date('Y-m-d'),
						)
					);
					$result5 = $statement5->fetchAll();
					$statement6 = $connect->query("SELECT LAST_INSERT_ID()");
					$purchase_id = $statement6->fetchColumn();
					if($purchase_id)
					{
						$query2 = "
							INSERT INTO supplier_payment_details (payment_id, supplier_id, purchase_id, bill_amount, amount_paid, balance, mode_of_payment,
							UTR_number, UTR_bank_name, date_of_payment) 
							VALUES (:payment_id, :supplier_id, :purchase_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :UTR_number,
							:UTR_bank_name, :date_of_payment)
						";
						$statement4 = $connect->prepare($query2);
						$statement4->execute(
						array(
							':payment_id'						=>	$payment_id,
							':supplier_id'						=>	$supplier_id,
							':purchase_id'						=>	$purchase_id,
							':bill_amount'						=>	$current_outstanding,
							':amount_paid'						=>	0,
							':balance'							=>  $current_outstanding,
							':mode_of_payment'					=>	'BroughtForward',
							':UTR_number'						=>	0,
							':UTR_bank_name'					=>	'',
							':date_of_payment'					=>	$outstanding_date,
							)
						);
						$result4 = $statement4->fetchAll();
						if(isset($result4))
						{
							echo ' Supplier Records Added...';
						}
					}
				}
			}
		}
	}
	if($_POST['btn_action'] == 'supplier_details')
	{
		$query = "
		SELECT * FROM supplier_details 
		INNER JOIN user_details ON user_details.user_id = supplier_details.entered_by 
		WHERE supplier_details.supplier_id = '".$_POST["supplier_id"]."'
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
				SELECT bill_amount,date_of_payment FROM supplier_payment_details
				WHERE supplier_id = '".$_POST["supplier_id"]."'
			";
			$statement1 = $connect->prepare($query1);
			$statement1->execute();
			$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
			$status = '';
			if($row['supplier_status'] == 'active')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			
			<tr>
				<td>Farmer Name</td>
				<td>'.$row["firm_name"].'</td>
			</tr>
			<tr>
				<td>Address</td>
				<td>'.$row["address"].'</td>
			</tr>
			<tr>
				<td>Contact Number</td>
				<td>'.$row["contact_no"].'</td>
			</tr>
			<tr>
				<td>Alternate Contact Number</td>
				<td>'.$row["alt_contact_no"].'</td>
			</tr>
			<tr>
				<td>Email ID</td>
				<td>'.$row["email_id"].'</td>
			</tr>
			<tr>
				<td>ZipCode</td>
				<td>'.$row["zipcode"].'</td>
			</tr>
			<tr>
				<td>GSTIN</td>
				<td>'.$row["GSTIN"].'</td>
			</tr>
			<tr>
				<td>Bank Name</td>
				<td>'.$row["bank_name"].'</td>
			</tr>
			<tr>
				<td>Branch Name</td>
				<td>'.$row["branch_name"].'</td>
			</tr>
			<tr>
				<td>Bank Account Name</td>
				<td>'.$row["bank_act_name"].'</td>
			</tr>
			<tr>
				<td>Bank Account Number</td>
				<td>'.$row["bank_act_no"].'</td>
			</tr>
			<tr>
				<td>IFSC Code</td>
				<td>'.$row["IFSC_code"].'</td>
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
		SELECT * FROM supplier_details WHERE supplier_id = :supplier_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':supplier_id'	=>	$_POST["supplier_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$query1 = "
				SELECT bill_amount,date_of_payment FROM supplier_payment_details
				WHERE supplier_id = '".$_POST["supplier_id"]."'
			";
			$statement1 = $connect->prepare($query1);
			$statement1->execute();
			$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
			$rowcount = $statement1->rowCount();
			
			$output['supplier_id'] = $row['supplier_id'];
			$output["firm_name"] = $row["firm_name"];
			//$output['contact_person_name'] = $row['contact_person_name'];
			$output['address'] = $row['address'];
			$output['contact_no'] = $row['contact_no'];
			$output['alt_contact_no'] = $row['alt_contact_no'];
			$output['email_id'] = $row['email_id'];
			$output['zipcode'] = $row['zipcode'];
			$output['GSTIN'] = $row['GSTIN'];
			$output['bank_name'] = $row['bank_name'];
			$output['branch_name'] = $row['branch_name'];			
			$output['bank_act_name'] = $row['bank_act_name'];
			$output['bank_act_no'] = $row['bank_act_no'];
			$output['IFSC_code'] = $row['IFSC_code'];
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
		if(isset($_POST['alt_contact_no']))
			$alt_contact_no = $_POST['alt_contact_no'];
		else
			$alt_contact_no=0;
		if(isset($_POST['email_id']))
			$email_id=$_POST['email_id'];
		else
			$email_id='';
		if(isset($_POST['zipcode']))
			$zipcode=$_POST['zipcode'];
		else
			$zipcode='';
		if(isset($_POST['branch_name']))
			$branch_name=$_POST['branch_name'];
		else
			$branch_name='';
		$contact_person_name='';
		
		$query = "
		UPDATE supplier_details 
		set  
		supplier_id = :supplier_id,
		firm_name = :firm_name,
		contact_person_name = :contact_person_name,
		address = :address, 
		contact_no = :contact_no, 
		alt_contact_no = :alt_contact_no, 
		email_id = :email_id, 
		zipcode = :zipcode, 
		GSTIN = :GSTIN,
		bank_name=:bank_name,
		branch_name=:branch_name,
		bank_act_name=:bank_act_name,
		bank_act_no=:bank_act_no,
		IFSC_code=:IFSC_code
		WHERE supplier_id = :supplier_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':supplier_id'			=>	$_POST['supplier_id'],
				':firm_name'			=>	$_POST['firm_name'],
				':contact_person_name'	=>	$contact_person_name,
				':address'				=>	$_POST['address'],
				':contact_no'			=>	$_POST['contact_no'],
				':alt_contact_no'		=>	$alt_contact_no,
				':email_id'				=>	$email_id,
				':zipcode'				=>	$zipcode,
				':GSTIN'				=>	$_POST['GSTIN'],
				':bank_name'				=>	$_POST['bank_name'],
				':branch_name'				=>	$branch_name,
				':bank_act_name'				=>	$_POST['bank_act_name'],
				':bank_act_no'				=>	$_POST['bank_act_no'],
				':IFSC_code'				=>	$_POST['IFSC_code'],
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Supplier Details Edited';
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
		UPDATE supplier_details 
		SET supplier_status = :supplier_status 
		WHERE supplier_id = :supplier_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':supplier_status'	=>	$status,
				':supplier_id'		=>	$_POST["supplier_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'supplier status change to ' . $status;
		}
	}
}


?>