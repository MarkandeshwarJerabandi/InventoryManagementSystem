<?php
//supplier_payment.php

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

	
	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Supplier Payment List</h3>
						</div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <div class="row" align="right">
                             <button type="button" name="view_payment" id="view_payment" data-toggle="modal" class="btn btn-success btn-xs">View Consolidated Payment</button>   		
                        </div>
                    </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="payment_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Payment ID</th>
								<th>Supplier Name</th>
								<th>Total Amount to be Paid</th>
								<th>Amount Paid</th>
								<th>Balance</th>
								<th>Entered By</th>
								<th>Date of Entry</th>
								<th></th>
								<th></th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal fade">
-
    	<div class="modal-dialog">
    		<form method="post" id="payment_form">
    			<div class="modal-content">
				
    				<div class="modal-footer">
    					<input type="hidden" name="payment_id" id="payment_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
		
<script type="text/javascript">
	
    $(document).ready(function(){

    	var paymentdataTable = $('#payment_data').DataTable({
			"processing":true,
			"serverSide":true,
			"payment":[],
			"ajax":{
				url:"supplier_payment_fetch.php",
				type:"POST"
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
			"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
		});

		$('#view_payment').click(function(){
			
				window.location="view_supplier_payment_report_copy250119.php";

		});
		
		$(document).on('submit', '#payment_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"supplier_payment_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#payment_form')[0].reset();
					$('#paymentModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					paymentdataTable.ajax.reload();
				}
			});
		});
		
		$(document).on('click', '.update', function(){
			var payment_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"supplier_payment_action.php",
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
			})
		});

		$(document).on('click', '.delete', function(){
			var supplier_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"supplier_payment_action.php",
					method:"POST",
					data:{supplier_id:supplier_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						paymentdataTable.ajax.reload();
					}
				})
			}
			else
			{
				return false;
			}
		});

    });
</script>