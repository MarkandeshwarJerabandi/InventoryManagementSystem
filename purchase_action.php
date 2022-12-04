<?php

//purchase_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	
	
	if($_POST['btn_action'] == 'Add')
	{
		$sql = "SELECT invoice_cash_bill_no
				FROM purchase_invoice
				order by invoice_cash_bill_no DESC
				LIMIT 1
				";
		$statementi = $connect->prepare($sql);
		$statementi->execute();
		$resulti = $statementi->fetch(PDO::FETCH_ASSOC);
		//echo $resulti['invoice_no'];
		if($resulti['invoice_cash_bill_no'])
			$invoice_cash_bill_no = $resulti['invoice_cash_bill_no'] + 1;
		else	
			$invoice_cash_bill_no = 1;
		
		$query = "
		INSERT INTO purchase_invoice (supplier_id, date_of_purchase, invoice_cash_bill_no, bill_amount, advance_paid, balance_amount, purchase_status, entered_by, date_of_entry) 
							  VALUES (:supplier_id, :date_of_purchase, :invoice_cash_bill_no, :bill_amount, :advance_paid,:balance_amount,:purchase_status, :entered_by, :date_of_entry)
		";
		$flag=0;
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':supplier_id'					=>	$_POST['supplier_id'],
				':date_of_purchase'				=>	$_POST['date_of_purchase'],
				':invoice_cash_bill_no'			=>	$invoice_cash_bill_no,
				':bill_amount'					=>	$_POST['total_amount'],
				':advance_paid'					=>	$_POST['advance_paid'],
				':balance_amount'					=>	$_POST['balance_amount'],
				':purchase_status'				=>	'active',
				':entered_by'					=>	$_SESSION["user_id"],
				':date_of_entry'				=>	date("Y-m-d")
			)
		)or die("Error in Purchase Invoice Query" . print_r($statement->errorInfo()));
		
		
		$total_amount = $_POST['total_amount'];
		$advance_paid = $_POST['advance_paid'];
		$balance_amount = $_POST['balance_amount'];
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$purchase_id = $statement->fetchColumn();

		if(isset($purchase_id))
		{
				$sub_query = "
				INSERT INTO purchase_details (purchase_id,season_year,sugar_cane_variety,harvester_name,
										vehicle_owner_name, vehicle_no, loaded_weight,empty_weight,gross_weight,deduction,net_weight,rate_per_ton)
										VALUES (:purchase_id,:season_year,:sugar_cane_variety,:harvester_name,
										:vehicle_owner_name, :vehicle_no, :loaded_weight,:empty_weight,:gross_weight,:deduction,:net_weight,:rate_per_ton)
				";				
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':purchase_id'			=>	$purchase_id,
						':season_year'			=>	$_POST['season_year'],
						':sugar_cane_variety'	=>	$_POST['sugar_cane_variety'],
						':harvester_name'		=>	$_POST['harvester_name'],
						':vehicle_owner_name'	=>	$_POST['vehicle_owner_name'],
						':vehicle_no'			=>	$_POST['vehicle_no'],
						':loaded_weight'		=>	$_POST['loaded_weight'],
						':empty_weight'			=>	$_POST['empty_weight'],
						':gross_weight'			=>	$_POST['gross_weight'],
						':deduction'			=>	$_POST['deduction'],
						':net_weight'			=>	$_POST['net_weight'],
						':rate_per_ton'			=>	$_POST['rate_per_ton']
					)
				);
				$flag=1;
		}
		if($flag==1)
		{
			// supplier payment update
			echo 'Purchase Details Entered and Bill Generated';
			$supplier_id = $_POST['supplier_id'];
			$total_amount_to_be_paid = $total_amount;
			$amount_paid = $advance_paid;
			$balance = $balance_amount;
			
			$entered_by = $_SESSION['user_id'];
			$date_of_entry = date('Y-m-d');
			
			$query_supplier_id = "SELECT * from supplier_payment where supplier_id = '".$supplier_id."'";
			$statement = $connect->prepare($query_supplier_id);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
			$row = $statement->rowCount();
			$payment_id = $result['payment_id'];
			$old_total_amount_to_be_paid = $result['total_amount_to_be_paid'];
			$old_amount_paid = $result['amount_paid'];
			$old_balance = $result['balance'];
			if($row>0)
			{
				//	echo $payment_id . " " . $old_total_amount_to_be_paid . " " . $old_amount_paid . " " . $balance;
					$supplier_payment_update_query = "
						UPDATE supplier_payment 
						SET
							payment_id = :payment_id,
							supplier_id = :supplier_id,
							total_amount_to_be_paid = :total_amount_to_be_paid,
							amount_paid = :amount_paid, 
							balance = :balance,
							entered_by = :entered_by,
							date_of_entry = :date_of_entry
							where payment_id = '".$payment_id."'
					";
					$statement1 = $connect->prepare($supplier_payment_update_query);
					$statement1->execute(
						array(
							':payment_id'					=>	$payment_id,
							':supplier_id'					=>	$supplier_id,
							':total_amount_to_be_paid'		=>	round(($total_amount_to_be_paid + $old_total_amount_to_be_paid),2),
							':amount_paid'					=>	round(($amount_paid + $old_amount_paid),2),
							':balance'						=>	round(($balance + $old_balance),2),
							':entered_by'					=>	$_SESSION["user_id"],
							':date_of_entry'				=>	$date_of_entry,
							)
					);
					$result = $statement1->fetchAll();
					if(isset($result))
					{
						echo ' and Supplier payment Updated...';
						echo '<br />';
						echo '<br />';
					}	
			}
			else
			{
				
				$supplier_payment_query = "
					INSERT INTO supplier_payment (supplier_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
					VALUES (:supplier_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
					";
				$statement1 = $connect->prepare($supplier_payment_query);
				$statement1->execute(
						array(
							':supplier_id'					=>	$supplier_id,
							':total_amount_to_be_paid'		=>	$total_amount_to_be_paid,
							':amount_paid'					=>	$amount_paid,
							':balance'						=>	$balance,
							':entered_by'					=>	$_SESSION["user_id"],
							':date_of_entry'				=>	$date_of_entry
							)
				);
				$result = $statement1->fetchAll();
				$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
				$payment_id = $statement2->fetchColumn();				
			}
			if($payment_id)
			{
				$supplier_payment_details_query = "
					INSERT INTO supplier_payment_details (payment_id, supplier_id, purchase_id, bill_amount, amount_paid, balance, mode_of_payment, UTR_number, UTR_bank_name, date_of_payment) 
					VALUES (:payment_id, :supplier_id, :purchase_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :UTR_number, :UTR_bank_name, :date_of_payment)
					";
				$statement1 = $connect->prepare($supplier_payment_details_query);
				$statement1->execute(
						array(
							':payment_id'					=>	$payment_id,
							':supplier_id'					=>	$supplier_id,
							':purchase_id'					=>	$purchase_id,
							':bill_amount'					=>	$total_amount_to_be_paid,
							':amount_paid'					=>	$amount_paid,
							':balance'						=>	$balance,
							':mode_of_payment'				=>	'',
							':UTR_number'					=>	0,
							':UTR_bank_name'				=>	'',
							':date_of_payment'				=>	$date_of_entry
							)
				);
				$result = $statement1->fetchAll();
				if(isset($result))
				{
					echo ' and Supplier payment details Added...';
					echo '<br />';
					echo '<br />';
				}
			}
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT distinct *
		FROM purchase_invoice 
		INNER JOIN supplier_details ON supplier_details.supplier_id = purchase_invoice.supplier_id
		INNER JOIN purchase_details ON purchase_details.purchase_id = purchase_invoice.purchase_id
		WHERE purchase_invoice.purchase_id = :purchase_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':purchase_id'	=>	$_POST["purchase_id"]
			)
		) or die("error in fetch_single" . print_r($statement->errorInfo()));
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['purchase_id'] = $row['purchase_id'];
			$output['supplier_id'] = $row['supplier_id'];
			$output['date_of_purchase'] = $row['date_of_purchase'];
			$output['invoice_cash_bill_no'] = $row['invoice_cash_bill_no'];
			$output['purchase_status'] = $row['purchase_status'];
			$output['season_year'] = $row['season_year'];
			$output['sugar_cane_variety'] = $row['sugar_cane_variety'];
			$output['harvester_name'] = $row['harvester_name'];
			$output['vehicle_owner_name'] = $row['vehicle_owner_name'];
			$output['vehicle_no'] = $row['vehicle_no'];
			$output['loaded_weight'] = $row['loaded_weight'];
			$output['empty_weight'] = $row['empty_weight'];
			$output['gross_weight'] = $row['gross_weight'];
			$output['deduction'] = $row['deduction'];
			$output['net_weight'] = $row['net_weight'];
			$output['rate_per_ton'] = $row['rate_per_ton'];
			$output['bill_amount'] = $row['bill_amount'];
			$output['advance_paid'] = $row['advance_paid'];
			$output['balance_amount'] = $row['balance_amount'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'fetch_SGST_CGST')
	{
		$product_details = fetch_product_details($_POST["product_id"], $connect);
		$output['SGST']=$product_details['SGST'];
		$output['CGST']=$product_details['CGST'];
		//$output['weight']=$product_details['size'];
		$output['product_unit'] = $product_details["product_unit"];
		$output['unit_conversion']=$product_details['unit_conversion'];
		echo json_encode($output);		
		
	//	c = count($_POST["quantity"]);
	//	echo '<script type="text/javascript">alert(c);</script>';
		
/*		$product_details = fetch_product_details($_POST["product_id"][0], $connect);
		$output['SGST_tax']=$product_details['SGST'];
		$output['CGST_tax']=$product_details['CGST'];
		$output['bill_amount'] = $product_details["SGST"]*$product_details["CGST"];
		$output['total_amount']=1121;
		echo json_encode($output);	*/
	}
	
	

	if($_POST['btn_action'] == 'Edit')
	{
			$flag=0;
			//echo "Purchase ID: " . $_POST["purchase_id"] . " " . "<br />";				
					//supplier payment deletion and updation
			$purchase_invoice_payment1 = "
								select bill_amount, advance_paid, balance_amount
								from purchase_invoice
								WHERE purchase_id = :purchase_id
								";
			$payment_statement1 = $connect->prepare($purchase_invoice_payment1);
			$payment_statement1->execute(
									array(
										':purchase_id'					=>	$_POST["purchase_id"]
									)
								);
			$purchase_result1 = $payment_statement1->fetch(PDO::FETCH_ASSOC);
			if(isset($purchase_result1))
			{
				$bill_amount = $purchase_result1["bill_amount"];
				$advance_paid = $purchase_result1["advance_paid"];
				$balance_amount = $purchase_result1["balance_amount"];

				$sup_payment_update_query2 = "
									UPDATE supplier_payment
									SET 
										total_amount_to_be_paid = total_amount_to_be_paid - :bill_amount,
										amount_paid = amount_paid - :amount_paid,
										balance = balance - :balance_amount
									WHERE supplier_id = :supplier_id
									";
				$payment_statement2 = $connect->prepare($sup_payment_update_query2);
				$payment_statement2->execute(
										array(
											':bill_amount'					=>	$bill_amount,
											':amount_paid'					=>	$advance_paid,
											':balance_amount'					=>	$balance_amount,
											':supplier_id'					=>	$_POST["supplier_id"]
										)
									);
				$payment_result2 = $payment_statement2->fetchAll();
				if(isset($payment_result2))
				{
										$delete_query2 = "
										DELETE FROM supplier_payment_details
										WHERE purchase_id = '".$_POST["purchase_id"]."' and supplier_id = '".$_POST["supplier_id"]."'
										";
										$statement2 = $connect->prepare($delete_query2);
										$statement2->execute();
										$delete_result2 = $statement2->fetchAll();
										if(isset($delete_result2))
										{
											$delete_query3 = "
											DELETE FROM purchase_invoice
											WHERE purchase_id = '".$_POST["purchase_id"]."' and supplier_id = '".$_POST["supplier_id"]."'
											";
											$statement3 = $connect->prepare($delete_query3);
											$statement3->execute();
											$delete_result3 = $statement3->fetchAll();
											if(isset($delete_result3))
											{
												echo " Supplier Details updated";											
												$flag=1;
											}
										}
				}
			}
			echo $flag;
			//add code to insert new purchase details here
			if($flag==1)
			{
				$sql = "SELECT invoice_cash_bill_no
				FROM purchase_invoice
				order by invoice_cash_bill_no DESC
				LIMIT 1
				";
				$statementi = $connect->prepare($sql);
				$statementi->execute();
				$resulti = $statementi->fetch(PDO::FETCH_ASSOC);
				//echo $resulti['invoice_no'];
				if($resulti['invoice_cash_bill_no'])
					$invoice_cash_bill_no = $resulti['invoice_cash_bill_no'] + 1;
				else	
					$invoice_cash_bill_no = 1;
				
				$query = "
				INSERT INTO purchase_invoice (supplier_id, date_of_purchase, invoice_cash_bill_no, bill_amount, advance_paid, balance_amount, purchase_status, entered_by, date_of_entry) 
									  VALUES (:supplier_id, :date_of_purchase, :invoice_cash_bill_no, :bill_amount, :advance_paid,:balance_amount,:purchase_status, :entered_by, :date_of_entry)
				";
				$fflag=0;
				
				$statement = $connect->prepare($query);
				$statement->execute(
					array(
						':supplier_id'					=>	$_POST['supplier_id'],
						':date_of_purchase'				=>	$_POST['date_of_purchase'],
						':invoice_cash_bill_no'			=>	$invoice_cash_bill_no,
						':bill_amount'					=>	$_POST['total_amount'],
						':advance_paid'					=>	$_POST['advance_paid'],
						':balance_amount'					=>	$_POST['balance_amount'],
						':purchase_status'				=>	'active',
						':entered_by'					=>	$_SESSION["user_id"],
						':date_of_entry'				=>	date("Y-m-d")
					)
				)or die("Error in Purchase Invoice Query" . print_r($statement->errorInfo()));
				
				
				$total_amount = $_POST['total_amount'];
				$advance_paid = $_POST['advance_paid'];
				$balance_amount = $_POST['balance_amount'];
				$result = $statement->fetchAll();
				$statement = $connect->query("SELECT LAST_INSERT_ID()");
				$purchase_id = $statement->fetchColumn();

				if(isset($purchase_id))
				{
						$sub_query = "
						INSERT INTO purchase_details (purchase_id,season_year,sugar_cane_variety,harvester_name,
												vehicle_owner_name, vehicle_no, loaded_weight,empty_weight,gross_weight,deduction,net_weight,rate_per_ton)
												VALUES (:purchase_id,:season_year,:sugar_cane_variety,:harvester_name,
												:vehicle_owner_name, :vehicle_no, :loaded_weight,:empty_weight,:gross_weight,:deduction,:net_weight,:rate_per_ton)
						";				
						$statement = $connect->prepare($sub_query);
						$statement->execute(
							array(
								':purchase_id'			=>	$purchase_id,
								':season_year'			=>	$_POST['season_year'],
								':sugar_cane_variety'	=>	$_POST['sugar_cane_variety'],
								':harvester_name'		=>	$_POST['harvester_name'],
								':vehicle_owner_name'	=>	$_POST['vehicle_owner_name'],
								':vehicle_no'			=>	$_POST['vehicle_no'],
								':loaded_weight'		=>	$_POST['loaded_weight'],
								':empty_weight'			=>	$_POST['empty_weight'],
								':gross_weight'			=>	$_POST['gross_weight'],
								':deduction'			=>	$_POST['deduction'],
								':net_weight'			=>	$_POST['net_weight'],
								':rate_per_ton'			=>	$_POST['rate_per_ton']
							)
						);
						$fflag=1;
				}
				if($fflag==1)
				{
					// supplier payment update
					echo 'Purchase Details Entered and Bill Generated';
					$supplier_id = $_POST['supplier_id'];
					$total_amount_to_be_paid = $total_amount;
					$amount_paid = $advance_paid;
					$balance = $balance_amount;
					
					$entered_by = $_SESSION['user_id'];
					$date_of_entry = date('Y-m-d');
					
					$query_supplier_id = "SELECT * from supplier_payment where supplier_id = '".$supplier_id."'";
					$statement = $connect->prepare($query_supplier_id);
					$statement->execute();
					$result = $statement->fetch(PDO::FETCH_ASSOC);
					$row = $statement->rowCount();
					$payment_id = $result['payment_id'];
					$old_total_amount_to_be_paid = $result['total_amount_to_be_paid'];
					$old_amount_paid = $result['amount_paid'];
					$old_balance = $result['balance'];
					if($row>0)
					{
						//	echo $payment_id . " " . $old_total_amount_to_be_paid . " " . $old_amount_paid . " " . $balance;
							$supplier_payment_update_query = "
								UPDATE supplier_payment 
								SET
									payment_id = :payment_id,
									supplier_id = :supplier_id,
									total_amount_to_be_paid = :total_amount_to_be_paid,
									amount_paid = :amount_paid, 
									balance = :balance,
									entered_by = :entered_by,
									date_of_entry = :date_of_entry
									where payment_id = '".$payment_id."'
							";
							$statement1 = $connect->prepare($supplier_payment_update_query);
							$statement1->execute(
								array(
									':payment_id'					=>	$payment_id,
									':supplier_id'					=>	$supplier_id,
									':total_amount_to_be_paid'		=>	round(($total_amount_to_be_paid + $old_total_amount_to_be_paid),2),
									':amount_paid'					=>	round(($amount_paid + $old_amount_paid),2),
									':balance'						=>	round(($balance + $old_balance),2),
									':entered_by'					=>	$_SESSION["user_id"],
									':date_of_entry'				=>	$date_of_entry,
									)
							);
							$result = $statement1->fetchAll();
							if(isset($result))
							{
								echo ' and Supplier payment Updated...';
								echo '<br />';
								echo '<br />';
							}	
					}
					else
					{
						
						$supplier_payment_query = "
							INSERT INTO supplier_payment (supplier_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
							VALUES (:supplier_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
							";
						$statement1 = $connect->prepare($supplier_payment_query);
						$statement1->execute(
								array(
									':supplier_id'					=>	$supplier_id,
									':total_amount_to_be_paid'		=>	$total_amount_to_be_paid,
									':amount_paid'					=>	$amount_paid,
									':balance'						=>	$balance,
									':entered_by'					=>	$_SESSION["user_id"],
									':date_of_entry'				=>	$date_of_entry
									)
						);
						$result = $statement1->fetchAll();
						$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
						$payment_id = $statement2->fetchColumn();				
					}
					if($payment_id)
					{
						$supplier_payment_details_query = "
							INSERT INTO supplier_payment_details (payment_id, supplier_id, purchase_id, bill_amount, amount_paid, balance, mode_of_payment, UTR_number, UTR_bank_name, date_of_payment) 
							VALUES (:payment_id, :supplier_id, :purchase_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :UTR_number, :UTR_bank_name, :date_of_payment)
							";
						$statement1 = $connect->prepare($supplier_payment_details_query);
						$statement1->execute(
								array(
									':payment_id'					=>	$payment_id,
									':supplier_id'					=>	$supplier_id,
									':purchase_id'					=>	$purchase_id,
									':bill_amount'					=>	$total_amount_to_be_paid,
									':amount_paid'					=>	$amount_paid,
									':balance'						=>	$balance,
									':mode_of_payment'				=>	'',
									':UTR_number'					=>	0,
									':UTR_bank_name'				=>	'',
									':date_of_payment'				=>	$date_of_entry
									)
						);
						$result = $statement1->fetchAll();
						if(isset($result))
						{
							echo ' and Supplier payment details Added...';
							echo '<br />';
							echo '<br />';
						}
					}
				}
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
		UPDATE purchase_invoice 
		SET purchase_status = :purchase_status 
		WHERE purchase_id = :purchase_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':purchase_status'	=>	$status,
				':purchase_id'		=>	$_POST["purchase_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Purchase status change to ' . $status;
		}
	}
}

?>