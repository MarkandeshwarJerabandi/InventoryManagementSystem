<?php
//make_customer_payment.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


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
	$(document).click(function(){
		$('#inventory_order_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#date_of_payment').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#cheque_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>
	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title"><b>Enter Customer Payment Details</b></h3>
                        </div>
                        
                    </div>
                </div>
                <div class="panel-body">
                	<form method="post" id="payment_form">
						<div class="modal-content">
						<?php 
												$payment_id = $_GET['payment_id'];
												$query = "
												SELECT * FROM customer_payment 
												INNER JOIN customer_details ON customer_details.customer_id = customer_payment.customer_id
												WHERE payment_id = '".$payment_id."' 
												";
												$statement = $connect->prepare($query);
												$statement->execute();
												$result = $statement->fetch(PDO::FETCH_ASSOC);
												$customer_id = $result["customer_id"];
												$customer_name = $result["customer_name"];
												$total_amount_to_be_paid = $result['total_amount_to_be_paid'];
												$amount_paid=$result['amount_paid'];
												$balance = $result['balance'];
												
											//	echo $customer_id;
										?>
							<div class="row">
								<div class="col-md-1">
								</div>
								
								<div class="col-md-3">
									<div class="form-group">
										<label>Customer Name</label>									
										<input type="hidden" name="customer_id" id="customer_id" class="form-control" required value="<?php echo $customer_id;?>"></input>
										<input readonly type="text" name="customer_name" id="customer_name" class="form-control" required value="<?php echo $customer_name;?>"></input>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Date</label>
										<input type="text" name="date_of_payment" id="date_of_payment" class="form-control" required />
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Mode of Payment</label>
										<select name="mode_of_payment"  id="mode_of_payment" class="form-control" required>
											<option value="">Select</option>
											<option value="cash">Cash</option>
											<option value="cheque">Cheque</option></select>
									</div>
								</div>
								<div class="col-md-2">
								</div>
							</div>
							<div class="row">
								<div class="col-md-1">
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Total Amount</label>
										<input readonly type="text" name="total_amount_to_be_paid" id="total_amount_to_be_paid" class="form-control" required value="<?php echo $total_amount_to_be_paid;?>"></input>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Total Amount Paid Till Today</label>
										<input readonly type="text" name="amount_paid" id="amount_paid" class="form-control" required value="<?php echo $amount_paid;?>"></input>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>Total Balance Amount as on Today</label>
										<input readonly type="text" name="balance" id="balance" class="form-control" required value="<?php echo $balance;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
								</div>
							</div>
							<div class="row">
								<div class="col-lg-10">
									<span id="span_payment_details"></span>
								</div>
							</div>
							<br/>
							<br/>
							<br/>
							
							<div class="row">
								<div class="col-md-1">
								</div>
								<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
									<h3 class="panel-title"><b>Invoice Wise Customer Payment Details</b></h3>
								</div>
							</div>
							<hr />
							<div class="row">
								
								<div class="col-md-2">
									<div class="form-group">
										<label>Invoice Number</label>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Invoice Date</label>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Invoice Bill Amount</label>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Amount Paid</label>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Balance Amount</label>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Amount Adjusted Towards Balance Amount</label>
									</div>
								</div>
							</div>
							<?php 
												$payment_id = $_GET['payment_id'];
												$query = "
												SELECT customer_payment_details.*,inventory_order.inventory_order_date as invoice_date
												FROM customer_payment_details, inventory_order
												WHERE payment_id = '".$payment_id."' and inventory_order.inventory_order_id = customer_payment_details.inventory_order_id
												group by(inventory_order_id)
												";
												$statement = $connect->prepare($query);
												$statement->execute();
												$result = $statement->fetchall(PDO::FETCH_ASSOC);
												$rowcount = $statement->rowCount();
												$flag=array($rowcount);
										//		echo $rowcount;
												if($rowcount>0)
												{
													$i = 0;
													$old_invoice_id =0; 
													foreach($result as $row)
													{
														$invoice_id = $row['inventory_order_id'];
														$query1 = "
														SELECT sum(amount_paid) as amount_paid
														FROM customer_payment_details
														WHERE inventory_order_id = '".$invoice_id."'
														";
														$statement1 = $connect->prepare($query1);
														$statement1->execute();
														$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
														if($result1)
														{
															$old_invoice_id = $invoice_id;
														}
														$invoice_bill_amount = $row['bill_amount'];
														$invoice_amount_paid = $result1['amount_paid'];
													//	$invoice_amount_paid = $row['amount_paid'];
													//	$invoice_balance=$row['balance'];
														$invoice_balance=($invoice_bill_amount-$invoice_amount_paid);
														$invoice_date = $row['invoice_date'];
														if($invoice_balance>0)		// if balance value is greater than zero
														{
							?>
							<div class="row">
								
								<div class="col-md-2">
									<div class="form-group">
										<input readonly type="text" name="invoice_id[]" id="<?php echo 'invoice_id'+$i;?>" class="form-control" required value="<?php echo $invoice_id;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input readonly type="text" name="inventory_order_date[]" id="<?php echo 'inventory_order_date'+$i;?>" class="form-control" required value="<?php echo $invoice_date;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
									<div class="invoice_bill_amount">
										<input readonly type="text" name="invoice_bill_amount[]" id="<?php echo 'invoice_bill_amount'+$i;?>" class="form-control" required value="<?php echo $invoice_bill_amount;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
									<div class="invoice_amount_paid">
										<input readonly type="text" name="invoice_amount_paid[]" id="<?php echo 'invoice_amount_paid'+$i;?>" class="form-control" required value="<?php echo $invoice_amount_paid;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
									<div class="invoice_balance">
										<input readonly type="text" name="invoice_balance[]" id="<?php echo 'invoice_balance'.$i ;?>" class="form-control" required value="<?php echo $invoice_balance;?>"></input>
									</div>
								</div>
								<div class="col-md-2">
									<div class="invoice_amount_paid_new">
										<input readonly type="text" name="invoice_amount_paid_new[]" id="<?php echo 'invoice_amount_paid_new'.$i;?>" class="form-control" required value=""></input>
									</div>
								</div>
							</div>
							<?php
														$i++;
														}		// if balance value is greater than zero
													}
												}
												
												
											//	echo $customer_id;
							?>
							
							<div class="modal-footer">
								<input type="hidden" name="payment_id" id="payment_id"  value="<?php echo $payment_id;?>"/>
								<input type="hidden" name="btn_action" id="btn_action" />
								<input type="submit" name="action" id="action" class="btn btn-info" value="ClickToPay" disabled />
							</div>
						</div>
					</form>					
                </div>
            </div>
        </div>
    </div>

    
		
<script type="text/javascript">
	
    $(document).ready(function(){

    	
		$(document).on('change', '#mode_of_payment', function(){
			var mode = $('#mode_of_payment').val();
		//	alert(mode);
			if(mode == "cash")
			{
				$('#span_cheque_details').remove(html);
				var html='';
				html += '<span id="span_cash_details"><div class="cash">';
				html += '<div class="col-md-3"></div>';
				html += '<div class="col-md-3">';
				html += 'Enter the Amount Paid';
				html += '<input name="current_amount_paid"  id="current_amount_paid" class="form-control" required value=""></input>';
				html += '</div>';
				html += '<div class="col-md-3">';
				html += 'New Balance';
				html += '<input readonly type="text" name="new_balance" id="new_balance" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-3"><input type="button" name="adjust" id="adjust" class="btn btn-info" value="ClickToAdjust" /></div>';
				html += '</div></span>';
				$('#span_payment_details').append(html);
			}
			else if(mode=="cheque")
			{
				$('#span_cash_details').remove(html);
				var html='';
				html += '<span id="span_cheque_details"><div class="cheque">';
				html += '<div class="col-md-1"></div>';
				html += '<div class="col-md-4">';
				html += 'Cheque Number';
				html += '<input name="cheque_number"  id="cheque_number" class="form-control" required></input>';
				html += '</div>';
				html += '<div class="col-md-3">';
				html += 'Cheque Date';
				html += '<input type="text" name="cheque_date" id="cheque_date" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-4">';
				html += 'Cheque Bank Name';
				html += '<input type="text" name="cheque_bank_name"  id="cheque_bank_name" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-3"></div>';
				html += '<div class="col-md-3">';
				html += 'Enter Cheque Amount';
				html += '<input type="text" name="cheque_amount" id="cheque_amount" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-3">';
				html += 'New Balance';
				html += '<input type="text" readonly name="new_balance" id="new_balance" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-3"><input type="button" name="adjust1" id="adjust1" class="btn btn-info" value="ClickToAdjust" /></div>';
				html += '</div></span>';
				$('#span_payment_details').append(html);
			}	
			else
			{
				$('#span_cash_details').remove();
				$('#span_cheque_details').remove();
			}	
		});
		
		$(document).on('click', '#adjust', function(){
			var current_amount_paid = $('#current_amount_paid').val();
			var old_balance = $('#balance').val();
			if(parseInt(current_amount_paid)>0 && parseInt(current_amount_paid)<=parseInt(old_balance))
			{				
				document.getElementById("action").disabled = false;
				var new_balance = old_balance - current_amount_paid;
				var icount = 0;
				var i;
			//	alert(current_amount_paid);
			//	alert(old_balance);
				icount = $(":input[id^=invoice_amount_paid_new]").length;
				for(i=0;i<icount;i++)
				{
						var new_balance_id = "invoice_amount_paid_new"+i;
						$('#'+new_balance_id).val(0);
				//		$('#new_balance').val(0);
				}
				if(parseInt(current_amount_paid)<=parseInt(old_balance))
				{
					$('#new_balance').val(new_balance);
					var amount = parseInt(current_amount_paid);
					
					for(i=0;i<icount && amount > 0;i++)
					{
				//		$('#invoice_id').each(function(index)
						var balance_id = "invoice_balance"+i;
						var invoice_balance = parseInt(document.getElementById(balance_id).value);
						var new_balance_id = "invoice_amount_paid_new"+i;
					//	alert(amount);
					//	alert(invoice_balance);
						$('#'+new_balance_id).val(0);
						if(amount>invoice_balance)
						{
					//		alert("greater")
							//document.getElementById(new_balance_id).value = invoice_balance;
							$('#invoice_amount_paid_new'+i).val(invoice_balance);
							amount = amount - invoice_balance;
						}
						else
						{
						//	document.getElementById(new_balance_id).value = amount;
							$('#'+new_balance_id).val(amount);
							amount = 0;
						
						}
					}
				}
			}
			else
			{
				document.getElementById("action").disabled = true;
					alert("Amount Paid Must be Greater than zero or it must be less than total Balance!!! Please ReTry!!!");
					$('#current_amount_paid').val(0);
				//	$('#current_amount_paid').select();
			}
			
			
		});
		
		$(document).on('click', '#adjust1', function(){
			var current_amount_paid = $('#cheque_amount').val();
			var old_balance = $('#balance').val();
			if(parseInt(current_amount_paid)>0 && parseInt(current_amount_paid)<=parseInt(old_balance))
			{
				document.getElementById("action").disabled = false;
				var new_balance = old_balance - current_amount_paid;
				var icount = 0;
				var i;
			//	alert(current_amount_paid);
			//	alert(old_balance);
				icount = $(":input[id^=invoice_amount_paid_new]").length;
				for(i=0;i<icount;i++)
				{
						var new_balance_id = "invoice_amount_paid_new"+i;
						$('#'+new_balance_id).val(0);
				}
				if(parseInt(current_amount_paid)<=parseInt(old_balance))
				{
					$('#new_balance').val(new_balance);
					var amount = parseInt(current_amount_paid);
					
					for(i=0;i<icount && amount > 0;i++)
					{
				//		$('#invoice_id').each(function(index)
						var balance_id = "invoice_balance"+i;
						var invoice_balance = parseInt(document.getElementById(balance_id).value);
						var new_balance_id = "invoice_amount_paid_new"+i;
					//	alert(amount);
					//	alert(invoice_balance);
						$('#'+new_balance_id).val(0);
						if(amount>invoice_balance)
						{
					//		alert("greater")
							//document.getElementById(new_balance_id).value = invoice_balance;
							$('#invoice_amount_paid_new'+i).val(invoice_balance);
							amount = amount - invoice_balance;
						}
						else
						{
						//	document.getElementById(new_balance_id).value = amount;
							$('#'+new_balance_id).val(amount);
							amount = 0;
						
						}
					}
				}
			}
			else
			{
				document.getElementById("action").disabled = true;
					alert("Amount Paid Must be Greater than zero or it must be less than total Balance!!! Please ReTry!!!");
					$('#cheque_amount').val(0);
			}
			
		});
		
		$(document).on('submit', '#payment_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"customer_payment_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
				//	$('#payment_form')[0].reset();
				//	$('#payment_form')[0].reload();
				//	$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				//	$('#action').attr('disabled', false);
				//	$('#span_cash_details').remove();
				//	$('#span_cheque_details').remove();
					if(data)
					{
						alert("Customer payment updated");
						window.location = "/gpss/customer_payment.php";
					}
				}
			});
		});
		
		$(document).on('click', '.update', function(){
			var inventory_order_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"customer_payment_action.php",
				method:"POST",
				data:{customer_id:customer_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					 $('#paymentModal').modal('show');
					// $('#customer_id').val(data.customer_id);
					// $('#inventory_order_date').val(data.inventory_order_date);
					// $('#bill_type').val(data.bill_type);
					// $('#span_product_details').html(data.product_details);
					// $('#payment_status').val(data.payment_status);
					// $('#span_payment_details').html(data.payment_details);
					// $('#span_cheque_details').html(data.cheque_details);
					// $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Order");
					// $('#inventory_order_id').val(inventory_order_id);
					 $('#action').val('Edit');
					 $('#btn_action').val('Edit');
				}
			});
		});

		$(document).on('click', '.delete', function(){
			var customer_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"customer_payment_action.php",
					method:"POST",
					data:{customer_id:customer_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						paymentdataTable.ajax.reload();
					}
				});
			}
			else
			{
				return false;
			}
		});

    });
</script>