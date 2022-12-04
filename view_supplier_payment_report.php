
<?php
//view_supplier_payment_report.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');

if(isset($_GET['payment_id']))
	$payment_id = $_GET['payment_id'];
else
	$payment_id = '';
//echo $payment_id;
?>
	<script type="text/javascript" src="js/jquery-3.3.1.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<script type="text/javascript" src="js/pdfmake.min.js"></script>
	<script type="text/javascript" src="js/vfs_fonts.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<script src="js/bootstrap-select.min.js"></script>

	<script>
	$(document).ready(function(){
		$('#from_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#to_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>

	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-21">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title" style="font-weight:bold;">Supplier Payment and Due Report</h3>
                        </div>						
						<br />
                    </div>
					
                </div>
                <div class="panel-body">
					<div class="modal-footer">
    					<input type="hidden" name="payment_id" id="payment_id" value=".$payment_id." />
    					<a href="supplier_payment_report_print.php?pdf=1&payment_id=<?php echo $payment_id;?>" class="btn btn-info btn-xs">Export to Print/PDF</a>
    				</div>
                	<table id="supplier_payment_data" class="table table-bordered table-striped">
                		
							<tr  align="center" >
								<th rowspan="1">Sl. No</th>
								<th rowspan="1">Name of the Supplier</th>
								<th rowspan="1">Invoice Number</th>
								<th rowspan="1">Invoice Date</th>
								<th rowspan="1" class="sum">Invoice Bill Amount</th>
								<th rowspan="1" class="sum">Amount Paid</th>
								<th rowspan="1" class="sum">Balance Amount</th>
								<th rowspan="1">Mode of Payment</th>
								<th rowspan="1">UTR Number</th>
								<th rowspan="1">Bank Name</th>
								<th rowspan="1">Date of Payment</th>
							</tr>
						
						<tbody>
							<?php 
												if(isset($_GET['payment_id']))
													$payment_id = $_GET['payment_id'];
												$query = "
												SELECT s.*,p.invoice_cash_bill_no,p.date_of_purchase,supplier_details.firm_name FROM supplier_payment_details as s
												INNER JOIN purchase_invoice as p ON p.purchase_id = s.purchase_id
												INNER JOIN supplier_details ON supplier_details.supplier_id = s.supplier_id
												";
												if(isset($_GET['payment_id']))
												{
													$query .= "WHERE payment_id = '".$payment_id."' ";
											//					group by(purchase_id)";
												}
												// else
													// $query .= "group by(purchase_id)";
												$query .= "order by s.supplier_id, s.purchase_id, p.invoice_cash_bill_no, s.date_of_payment";		
											//	group by(purchase_id)
												$statement = $connect->prepare($query);
												$statement->execute();
												$result = $statement->fetchAll(PDO::FETCH_ASSOC);
												$rowcount = $statement->rowCount();
												
											//	echo $rowcount;
												if($rowcount>0)
												{
													$i = 1;
													$flag = '';
													$count=1;
													foreach($result as $row)
													{
														$supplier_id = $row['supplier_id'];
														$supplier_name = $row['firm_name'];
														$purchase_id = $row['purchase_id'];
														$invoice_cash_bill_no = $row['invoice_cash_bill_no'];
														$invoice_bill_amount = $row['bill_amount'];
														$mode_of_payment = $row['mode_of_payment'];
														if($mode_of_payment=='RTGS')
														{
															$UTR_number = $row['UTR_number'];
															$UTR_bank_name = $row['UTR_bank_name'];
														}
														else
														{
															$UTR_number = 0;
															$UTR_bank_name = '';
														}
														$date_of_purchase = $row['date_of_purchase'];
														$date_of_payment = $row['date_of_payment'];
														$invoice_amount_paid = $row['amount_paid'];
													//	$invoice_amount_paid = $row['amount_paid'];
													//	$invoice_balance=$row['balance'];
														$invoice_balance=$row['balance'];;
														
							?>
														<tr>
															<td>
																<label><?php echo $i;?></label>
															</td>
															<td>
																<label><?php echo $supplier_name;?></label>
															</td>
															<td>
																<label><?php echo $invoice_cash_bill_no;?></label>
															</td>
															<td>
																<label><?php echo $date_of_purchase;?></label>
															</td>
															<td>
																<label><?php echo $invoice_bill_amount;?></label>
															</td>
															<td>
																<label><?php echo $invoice_amount_paid;?></label>
															</td>
															<td>
																<label><?php echo $invoice_balance;?></label>
															</td>
															<td>
																<label><?php echo $mode_of_payment;?></label>
															</td>
															<td>
																<label><?php echo $UTR_number;?></label>
															</td>
															<td>
																<label><?php echo $UTR_bank_name;?></label>
															</td>
															<td>
																<label><?php echo $date_of_payment;?></label>
															</td>
														</tr>
							<?php
														$i++;
														
															
							?>
							
															
							<?php						
														
																
													}
													$query1 ="select * from supplier_payment where supplier_id='".$supplier_id."'";
													$statement1 = $connect->prepare($query1);
													$statement1->execute();
													$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
													$rowcount1 = $statement1->rowCount();
													//echo $rowcount1;
													$total_bill_amount = $result1['total_amount_to_be_paid'];
													$total_amount_paid = $result1['amount_paid'];
													$total_balance = $result1['balance'];
							?>
							
							
													<tr>
																<th colspan="4">
																	Total Amount in Rupees
																</th>
																<th>
																	<?php echo $total_bill_amount;?>
																</th>
																<th>
																	<?php echo $total_amount_paid;?>
																</th>
																<th>
																	<?php echo $total_balance;?>
																</th>
																<th colspan="4">
																</th>
													</tr>
							<?php
												}
							?>
						
						</tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="salesModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="sales_form">
    			<div class="modal-content"> 
					<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Search By Month </h4>
    				</div>
					<div class="modal-body">
    					<div class="row">
						
							<div class="col-md-12">
								<div class="form-group">
									<label>Month of Sale</label>
									<select name="month_of_sale" id="month_of_sale" class="form-control" required />
										<option value="">Select Month</option>
										<option value="jan">January</option>
										<option value="feb">February</option>
										<option value="mar">March</option>
										<option value="apr">April</option>
										<option value="may">May</option>
										<option value="jun">June</option>
										<option value="jul">July</option>
										<option value="aug">August</option>
										<option value="sep">September</option>
										<option value="oct">October</option>
										<option value="nov">November</option>
										<option value="dec">December</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					
    				<div class="modal-footer">
    					<input type="hidden" name="payment_id" id="payment_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Search" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
	
<script type="text/javascript">

	
    
			
			
	$(document).ready(function(){
		
		var supplier_paymentdataTable = $('#supplier_payment_data').DataTable({
				"processing":true,
				"searching":true,
				"ajax":{
				url:"view_supplier_payment_report.php",
				method:"POST"
				},
				"dom":'lBfrtrip',
				"columnDefs":[
					{
						"targets":'_all',
						"orderable":false,
						"searchable":true,
					},
				],
				"buttons": [
				{
					extend: 'collection',
					text: 'Export',
					footer:true,
					buttons: [
						
						'copy',
						'excel',
						'csv',
						'pdf',
						'print'
					],
					
				}
				],				
				"pageLength": 10
				
			});
	});