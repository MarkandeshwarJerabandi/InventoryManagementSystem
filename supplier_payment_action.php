<?php

//supplier_payment_action.php

include('database_connection.php');

include('function.php');

	
	$payment_id = $_POST['payment_id']; 
	$supplier_id = $_POST['supplier_id'];
	$mode_of_payment = $_POST['mode_of_payment'];
	if($mode_of_payment == 'cash')
	{
		$current_amount_paid = $_POST['current_amount_paid'];
		$new_balance = $_POST['new_balance'];
		$UTR_number = 0;
		$UTR_bank_name='';
	}
	else if($mode_of_payment == 'RTGS')
	{
		$current_amount_paid = $_POST['UTR_amount'];
		$new_balance = $_POST['new_balance'];
		$UTR_number = $_POST['UTR_number'];
		$UTR_bank_name = $_POST['UTR_bank_name'];
	}
	if($current_amount_paid>0)
	{
		$update_supplier_payment = "
									UPDATE supplier_payment 
									SET amount_paid = amount_paid + $current_amount_paid,
										balance = '".$new_balance."'
									WHERE supplier_id = '".$supplier_id."'
									";
		$statement = $connect->prepare($update_supplier_payment);
		$statement->execute();
		$rowcount = count($_POST['invoice_cash_bill_no']);
		if($rowcount>0)
		{
			$date_of_payment = $_POST['date_of_payment'];
			
			for($i=0;$i<$rowcount;$i++)
			{
				if($_POST['invoice_amount_paid_new'][$i]>0)
				{
					$query = "
					INSERT INTO supplier_payment_details (payment_id, supplier_id, purchase_id, bill_amount, amount_paid, balance, mode_of_payment, UTR_number, UTR_bank_name, date_of_payment)
					VALUES (:payment_id, :supplier_id, :purchase_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :UTR_number, :UTR_bank_name, :date_of_payment)
					";
					$statement = $connect->prepare($query);
					$statement->execute(
						array(
							':payment_id'					=>	$payment_id,
							':supplier_id'					=>	$supplier_id,
							':purchase_id'					=>	$_POST['purchase_id'][$i],
							':bill_amount'					=>	$_POST['invoice_bill_amount'][$i],
							':amount_paid'					=>	$_POST['invoice_amount_paid_new'][$i],
							':balance'						=>	($_POST['invoice_balance'][$i] - $_POST['invoice_amount_paid_new'][$i]),
							':mode_of_payment'				=>	$mode_of_payment,
							':UTR_number'					=>	$UTR_number,
							':UTR_bank_name'				=>	$UTR_bank_name,
							':date_of_payment'				=>	$date_of_payment
						)
					);
					$result = $statement->fetchAll();
					if(isset($result))
					{
						echo "supplier payment updated";
						echo "<script type='text/javascript'>alert(<?php echo 'supplier Payment Updated';?>);</script>";
						header('supplier_payment.php');
					}
				}
				
			}
			
		}
	}
	else
	{
		echo "<script type='text/javascript'>alert(<?php echo 'Amount Paid Must be More than Zero!!!Please ReTry';?>);</script>";
		header('supplier_payment.php');
	}
	
	
	
	

?>