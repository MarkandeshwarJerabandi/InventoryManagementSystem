<?php

//customer_payment_report_print.php

if(isset($_GET["pdf"]))
{
	
	require_once 'pdf.php';
	include('database_connection.php');
	include('function.php');
	if(!isset($_SESSION['type']))
	{
		header('location:login.php');
	}
	$output = '';
	$output = '<h4 align="right">'.'Till: ' . date("Y-m-d").'</h4>';
	
	$output .= '<table width="100%" border="1" cellpadding="0" cellspacing="0">
				<caption>Consolidated Customer Payment Details</caption>
                <tr  align="center" >
					<th>Sl. No</th>
					<th>Name of the Customer</th>
					<th>Invoice Number</th>
					<th>Invoice Date</th>
					<th class="sum">Invoice Bill Amount</th>
					<th class="sum">Amount Paid</th>
					<th class="sum">Balance Amount</th>
					<th>Mode of Payment</th>
					<th rowspan="1">Cheque Number</th>
					<th rowspan="1">Cheque Date</th>
					<th rowspan="1">Cheque Bank Name</th>
					<th>Date of Payment</th>
				</tr>'; 
				if(isset($_GET['payment_id']))
					$payment_id = $_GET['payment_id'];
			//	echo $payment_id;
				$query = "SELECT * FROM customer_payment,customer_details
							where customer_payment.customer_id=customer_details.customer_id
						";
				if(isset($_GET['payment_id']) && $_GET['payment_id']!='')
					$query .= " and customer_payment.payment_id = '".$payment_id."' ";
				$query .= "order by customer_payment.customer_id";
				$statement = $connect->prepare($query);
				$statement->execute();
				$result = $statement->fetchAll(PDO::FETCH_ASSOC);
				$rowcount = $statement->rowCount();
				//	echo $rowcount;
				if($rowcount>0)
				{
					$i = 1;
					foreach($result as $row)
					{
						$customer_id = $row['customer_id'];
						$customer_name = $row['customer_name'];
						$total_amount_to_be_paid = $row['total_amount_to_be_paid'];
						$total_amount_paid = $row['amount_paid'];
						$total_balance = $row['balance'];
						$query1 = "
									SELECT * FROM customer_payment_details, inventory_order
											where customer_payment_details.customer_id='".$customer_id."'
											and customer_payment_details.customer_id = inventory_order.customer_id 
											and customer_payment_details.inventory_order_id = inventory_order.inventory_order_id
											order by inventory_order.inventory_order_id, customer_payment_details.date_of_payment
															
								";
						$statement1 = $connect->prepare($query1);
						$statement1->execute();
						$result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
						$rowcount1 = $statement1->rowCount();
						if($rowcount1>0)
						{
							$flag=0;		
							foreach($result1 as $row1)
							{
								$inventory_order_id = $row1['inventory_order_id'];
								$inventory_order_date = $row1['inventory_order_date'];
								$mode_of_payment = $row1['mode_of_payment'];
								if($mode_of_payment=='cheque')
								{
									$cheque_number = $row1['cheque_number'];
									$cheque_date = $row1['cheque_date'];
									$cheque_bank_name = $row1['cheque_bank_name'];
								}
								else
								{
									$cheque_number = 0;
									$cheque_date = '0000-00-00';
									$cheque_bank_name = '';
								}
								$date_of_payment = $row1['date_of_payment'];
								$invoice_bill_amount = $row1['inventory_order_total'];
								$invoice_amount_paid = $row1['amount_paid'];
								$invoice_balance=$row1['balance'];;
								$output .= '<tr>';
								if($flag==0)
								{
									$output .= '<td rowspan='.$rowcount1.'>
												'.$i.'
												</td>
												<td rowspan='.$rowcount1.'>
												'.$customer_name.'
												</td >';	
									$flag=1;
								}
								$output .= '<td>
												'.$inventory_order_id.'
											</td>
											<td>
												'.$inventory_order_date.'
											</td>
											<td>
												'.$invoice_bill_amount.'
											</td>
											<td>
												'.$invoice_amount_paid.'
											</td>
											<td>
												'.$invoice_balance.'
											</td>
											<td>
												'.$mode_of_payment.'
											</td>
											<td>
												'.$cheque_number.'
											</td>
											<td>
												'.$cheque_date.'
											</td>
											<td>
												'.$cheque_bank_name.'
											</td>
											<td>
												'.$date_of_payment.'
											</td>
											</tr>';		
							}
						}
						$output .= '<tr>
										<th colspan="4">
											Total in Rupees
										</th>
										<th>
											'.$total_amount_to_be_paid.'
										</th>
										<th>
											'.$total_amount_paid.'
										</th>
										<th>
											'.$total_balance.'
										</th>
										<th colspan="5">
										</th>
									</tr>';	
						$i++;	
					}
				}		
				$output .='</table>';	
	$pdf = new Pdf();
	$file_name = 'customer_payment_report.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->set_paper('A4','landscape');
	$pdf->stream($file_name, array("Attachment" => false));
	
	// $doc = new DOMDocument();
// $doc->loadHTML("<html><body>Test<br></body></html>");
// echo $doc->saveHTML();
}
?>