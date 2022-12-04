<?php
//purchase.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<script type="text/javascript" src="js/pdfmake.min.js"></script>
	<script type="text/javascript" src="js/vfs_fonts.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<script src="js/bootstrap-select.min.js"></script>
	
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<script src="js/bootstrap-select.min.js"></script>

	<script>
	$(document).ready(function(){
		$('#date_of_purchase').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>

	<style type="text/css">
    @media screen and (min-width: 768px) {
        .modal-dialog {
          width: 700px; /* New width for default modal */
        }
        .modal-sm {
          width: 350px; /* New width for small modal */
        }
    }
    @media screen and (min-width: 992px) {
        .modal-lg {
          width: 950px; /* New width for large modal */
        }
    }
	</style>


	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-22">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Purchases List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="purchase_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Purchase ID</th>
								<th>Invoice No</th>
								<th>Supplier Name</th>
								<th>Purchase Date</th>
								<th>Season Year</th>
								<th>Sugar Cane Variety</th>
								<th>Harvester Name</th>
								<th>Vehicle Owner Name</th>
								<th>Vehicle No</th>
								<th>Loaded Weight</th>
								<th>Empty Weight</th>
								<th>Gross Weight</th>
								<th>Deduction</th>
								<th>Net Weight</th>
								<th>Rate Per Ton</th>
								<th>Total Amount</th>
								<th>Advance Paid</th>
								<th>Balance Amount</th>		
								<th>Status</th>
								<th></th>
								<th></th>
								<th></th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="purchaseModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="purchase_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Purchase Entry </h4>
    				</div>
    				<div class="modal-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Season</label>
									<select name="season_year" id="season_year" class="form-control" required />										
										<?php echo fill_season_list($connect);?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Sugar Cane Variety</label>
									<select name="sugar_cane_variety" id="sugar_cane_variety" class="form-control" required />										
										<?php echo fill_sugarcane_list($connect);?>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Date of Purchase</label>
									<input type="text" name="date_of_purchase" id="date_of_purchase" class="form-control" required />
								</div>
							</div>
						</div>
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Farmer Name</label>
									<select name="supplier_id" id="supplier_id" class="form-control" required />										
										<?php echo fill_supplier_list($connect);?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Harvester Code and Name</label>
									<input type="text" name="harvester_name" id="harvester_name" pattern="[a-zA-Z0-9 ]*" class="form-control" required />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Vehicle Owner Code and Name</label>
									<input type="text" name="vehicle_owner_name" id="vehicle_owner_name" pattern="[a-zA-Z0-9 ]*" class="form-control" required />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Enter Vehicle Number</label>
									<input type="text" name="vehicle_no" id="vehicle_no" class="form-control" pattern="[a-zA-Z0-9 ]+" required />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group" align="center">
									<label ><strong>VEHICLE LOADED DETAILS</strong></label>
								</div>
							</div>
						</div>
				
						<div class="row" style="font-size:25px;">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>WEIGHT</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="center">
								<div class="form-group">
									<label><strong>In TONS</strong></label>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>LOADED</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="left">
								<div class="form-group">
									<input type="text" name="loaded_weight" id="loaded_weight" pattern="[0-9]*\.?[0-9]+" class="form-control" required />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>EMPTY</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="left">
								<div class="form-group">
									<input type="text" name="empty_weight" id="empty_weight" pattern="[0-9]*\.?[0-9]+" class="form-control" required />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>Gross WEIGHT</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="left">
								<div class="form-group">
									<input type="text" name="gross_weight" id="gross_weight" class="form-control" readonly required />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>Deduction</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="left">
								<div class="form-group">
									<input type="text" name="deduction" id="deduction" class="form-control" required readonly />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6" align="center">
								<div class="form-group">
									<label><strong>NET WEIGHT</strong></label>
								</div>
							</div>
							<div class="col-md-4" align="left">
								<div class="form-group">
									<input type="text" name="net_weight" id="net_weight" class="form-control" readonly required />
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group">
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-md-3" align="center">
								<div class="form-group">
									<label><strong>Rate Per Ton</strong></label>
									<input type="text" name="rate_per_ton" id="rate_per_ton" pattern="([0-9]+(\.[0-9]+)?|\.[0-9]+)" class="form-control" required />
								</div>
							</div>
							<div class="col-md-3" align="center">
								<div class="form-group">
									<label><strong>Total Amount</strong></label>
									<input type="text" name="total_amount" id="total_amount" class="form-control" readonly required />
								</div>
							</div>
							<div class="col-md-3" align="center">
								<div class="form-group">
									<label><strong>Advance Paid</strong></label>
									<input type="text" name="advance_paid" id="advance_paid" pattern="([0-9]+(\.[0-9]+)?|\.[0-9]+)" class="form-control" required />
								</div>
							</div>
							<div class="col-md-3" align="center">
								<div class="form-group">
									<label><strong>Balance Amount</strong></label>
									<input type="text" name="balance_amount" id="balance_amount" class="form-control" readonly required />
								</div>
							</div>
						</div>
						
						<div class="form-group" class="col-md-12" align="center">
							<label>Click To Calculate</label><br/>
							<button type="button" name="calculate" id="calculate" class="btn btn-warning btn-xs calculate" >Calculate</button>
							<input type="text" name="old_count_hidden" id="old_count_hidden" val="" readonly hidden/>
						</div>
						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="purchase_id" id="purchase_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
		
<script type="text/javascript">

			
	$(document).ready(function(){
		
		var count = 0;
		// if($('#old_count_hidden').val())
				// count = $('#old_count_hidden').val();
		// else
				// count = 0;
		
		var deleted_row_id=[];
		var c=0;
		
    	var purchasedataTable = $('#purchase_data').DataTable({
			"processing":true,
			"serverSide":true,
			"purchase":[],
			"searching":true,
			"ajax":{
				url:"purchase_fetch.php",
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

		purchasedataTable.columns().every( function ( colIdx ) {
				$( 'input', purchasedataTable.column( colIdx ).footer() ).on( 'keyup change', function () {
			//		alert(salesdataTable.colIdx.value);
				purchasedataTable.column(colIdx).search( this.value ).draw();
				} );
			} );
		$('#add_button').click(function(){
			$('#purchaseModal').modal('show');
			$('#purchase_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Purchase Entry");
			$('#action').val('Add');
			$('#btn_action').val('Add');			
		});
		
		$(document).on('click', '#calculate', function(){
			find_bill_amount();
			});	
		
		function find_bill_amount()
		{
			var gross_weight=0,deduction=0,net_weight=0;	
			var total_amount=0,advance_paid=0,balance_amount=0;
			var loaded_weight = parseFloat($('#loaded_weight').val());
			var empty_weight = parseFloat($('#empty_weight').val());
			
			var rate_per_ton = parseFloat($('#rate_per_ton').val());
			var advance_paid = parseFloat($('#advance_paid').val());
			
			gross_weight = parseFloat(loaded_weight - empty_weight);
			deduction = parseFloat(gross_weight * 0.01);
			net_weight = parseFloat(gross_weight - deduction);
			total_amount = parseFloat(net_weight * rate_per_ton);
			balance_amount = parseFloat(total_amount - advance_paid);
			
			$('#gross_weight').val(parseFloat(gross_weight).toFixed(2));
			$('#deduction').val(parseFloat(deduction).toFixed(2));
			$('#net_weight').val(parseFloat(net_weight).toFixed(2));
			$('#total_amount').val(parseFloat(total_amount).toFixed(2));
			$('#balance_amount').val(parseFloat(balance_amount).toFixed(2));
			
		}
		
		$(document).on('submit', '#purchase_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"purchase_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#purchase_form')[0].reset();
					$('#purchaseModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					purchasedataTable.ajax.reload();
				}
			});
		});

		$(document).on('click', '.update', function(){
			var purchase_id = $(this).attr("id");
			alert(purchase_id);
			var btn_action = 'fetch_single';
			$.ajax({
				url:"purchase_action.php",
				method:"POST",
				data:{purchase_id:purchase_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#purchaseModal').modal('show');
					$('#purchase_id').val(data.purchase_id);
					$('#supplier_id').val(data.supplier_id);
					$('#date_of_purchase').val(data.date_of_purchase);
					$('#invoice_cash_bill_no').val(data.invoice_cash_bill_no);
					$('#season_year').val(data.season_year);
					$('#sugar_cane_variety').val(data.sugar_cane_variety);
					$('#harvester_name').val(data.harvester_name);
					$('#vehicle_owner_name').val(data.vehicle_owner_name);
					$('#vehicle_no').val(data.vehicle_no);
					$('#loaded_weight').val(data.loaded_weight);
					$('#empty_weight').val(data.empty_weight);
					$('#gross_weight').val(data.gross_weight);
					$('#deduction').val(data.deduction);
					$('#net_weight').val(data.net_weight);
					$('#rate_per_ton').val(data.rate_per_ton);
					$('#total_amount').val(data.bill_amount);
					$('#advance_paid').val(data.advance_paid);
					$('#balance_amount').val(data.balance_amount);
					
					count = data.old_count_hidden;
				//	alert("old:"+count);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Purchase");
					$('#purchase_id').val(purchase_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');					
					
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var purchase_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"purchase_action.php",
					method:"POST",
					data:{purchase_id:purchase_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						purchasedataTable.ajax.reload();
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