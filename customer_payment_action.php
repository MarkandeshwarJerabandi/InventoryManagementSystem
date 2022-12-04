<?php

//customer_payment_action.php

include('database_connection.php');

include('function.php');

	
	$payment_id = $_POST['payment_id']; 
	$customer_id = $_POST['customer_id'];
	$mode_of_payment = $_POST['mode_of_payment'];
	if($mode_of_payment == 'cash')
	{
		$current_amount_paid = $_POST['current_amount_paid'];
		$new_balance = $_POST['new_balance'];
		$cheque_number = 0;
		$cheque_date = 0000-00-00;
		$cheque_bank_name='';
	}
	else if($mode_of_payment == 'cheque')
	{
		$current_amount_paid = $_POST['cheque_amount'];
		$new_balance = $_POST['new_balance'];
		$cheque_number = $_POST['cheque_number'];
		$cheque_bank_name = $_POST['cheque_bank_name'];
		$cheque_date = $_POST['cheque_date'];
	}
	if($current_amount_paid>0)
	{
		$update_customer_payment = "
									UPDATE customer_payment 
									SET amount_paid = amount_paid + $current_amount_paid,
										balance = '".$new_balance."'
									WHERE customer_id = '".$customer_id."'
									";
		$statement = $connect->prepare($update_customer_payment);
		$statement->execute();
		$rowcount = count($_POST['invoice_id']);
		if($rowcount>0)
		{
			$date_of_payment = $_POST['date_of_payment'];
			for($i=0;$i<$rowcount;$i++)
			{
				$query = "
				INSERT INTO customer_payment_details (payment_id, customer_id, inventory_order_id, bill_amount, amount_paid, balance, mode_of_payment, cheque_number, cheque_date, cheque_bank_name, date_of_payment) 
				VALUES (:payment_id, :customer_id, :inventory_order_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :cheque_number, :cheque_date, :cheque_bank_name, :date_of_payment)
				";
				$statement = $connect->prepare($query);
				$statement->execute(
					array(
						':payment_id'					=>	$payment_id,
						':customer_id'					=>	$customer_id,
						':inventory_order_id'			=>	$_POST['invoice_id'][$i],
						':bill_amount'					=>	$_POST['invoice_bill_amount'][$i],
						':amount_paid'					=>	$_POST['invoice_amount_paid_new'][$i],
						':balance'						=>	($_POST['invoice_balance'][$i] - $_POST['invoice_amount_paid_new'][$i]),
						':mode_of_payment'				=>	$mode_of_payment,
						':cheque_number'					=>	$cheque_number,
						':cheque_date'					=>	$cheque_date,
						':cheque_bank_name'					=>	$cheque_bank_name,
						':date_of_payment'					=>	$date_of_payment
					)
				);
				$result = $statement->fetchAll();
				if(isset($result))
				{
					echo "customer payment updated";
					echo "<script type='text/javascript'>alert(<?php echo 'Customer Payment Updated';?>);</script>";
					header('customer_payment.php');
				}
			}
		}
	}
	else
	{
		echo "<script type='text/javascript'>alert(<?php echo 'Amount Paid must be More than Zero!!! Please ReTry';?>);</script>";
		header('customer_payment.php');
	}
	
	
	

?>