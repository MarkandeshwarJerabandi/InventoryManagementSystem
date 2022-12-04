<?php

//supplier_payment_report_print.php

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
				<caption>Consolidated Supplier Payment Details</caption>
                <tr  align="center" >
					<th>Sl. No</th>
					<th>Name of the Supplier</th>
					<th>Invoice Number</th>
					<th>Invoice Date</th>
					<th class="sum">Invoice Bill Amount</th>
					<th class="sum">Amount Paid</th>
					<th class="sum">Balance Amount</th>
					<th>Mode of Payment</th>
					<th>UTR Number</th>
					<th>Bank Name</th>
					<th>Date of Payment</th>
				</tr>'; 
				if(isset($_GET['payment_id']))
					$payment_id = $_GET['payment_id'];
			//	echo $payment_id;
				$query = "SELECT * FROM supplier_payment,supplier_details
							where supplier_payment.supplier_id=supplier_details.supplier_id
						";
				if(isset($_GET['payment_id']) && $_GET['payment_id']!='')
					$query .= " and supplier_payment.payment_id = '".$payment_id."' ";
				$query .= "order by supplier_payment.supplier_id";
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
						$supplier_id = $row['supplier_id'];
						$supplier_name = $row['firm_name'];
						$total_amount_to_be_paid = $row['total_amount_to_be_paid'];
						$total_amount_paid = $row['amount_paid'];
						$total_balance = $row['balance'];
						$query1 = "
									SELECT * FROM supplier_payment_details, purchase_invoice
											where supplier_payment_details.supplier_id='".$supplier_id."'
											and supplier_payment_details.supplier_id = purchase_invoice.supplier_id 
											and supplier_payment_details.purchase_id = purchase_invoice.purchase_id
											order by purchase_invoice.purchase_id, purchase_invoice.invoice_cash_bill_no, supplier_payment_details.date_of_payment
															
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
								$purchase_id = $row1['purchase_id'];
								$date_of_purchase = $row1['date_of_purchase'];
								$invoice_cash_bill_no = $row1['invoice_cash_bill_no'];
								$mode_of_payment = $row1['mode_of_payment'];
								if($mode_of_payment=='RTGS')
								{
									$UTR_number = $row1['UTR_number'];
									$UTR_bank_name = $row1['UTR_bank_name'];
								}
								else
								{
									$UTR_number = 0;
									$UTR_bank_name = '';
								}
								$date_of_payment = $row1['date_of_payment'];
								$invoice_bill_amount = $row1['bill_amount'];//+$row1['SGST']+$row1['CGST'];
								$invoice_amount_paid = $row1['amount_paid'];
								$invoice_balance=$row1['balance'];;
								$output .= '<tr>';
								if($flag==0)
								{
									$output .= '<td rowspan='.$rowcount1.'>
												'.$i.'
												</td>
												<td rowspan='.$rowcount1.'>
												'.$supplier_name.'
												</td >';	
									$flag=1;
								}
								$output .= '<td>
												'.$invoice_cash_bill_no.'
											</td>
											<td>
												'.$date_of_purchase.'
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
												'.$UTR_number.'
											</td>
											<td>
												'.$UTR_bank_name.'
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
										<th colspan="4">
										</th>
									</tr>';	
						$i++;	
					}
				}		
				$output .='</table>';	
	$pdf = new Pdf();
	$file_name = 'supplier_payment_report.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->set_paper('A4','landscape');
	$pdf->stream($file_name, array("Attachment" => false));
	
	// $doc = new DOMDocument();
// $doc->loadHTML("<html><body>Test<br></body></html>");
// echo $doc->saveHTML();
}
?>