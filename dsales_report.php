<?php
//sales_report.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('report_header.php');


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
                            <h3 class="panel-title" style="font-weight:bold;">Search Sales Report Using</h3>
                        </div>						
						<br />
					<div class="row" >
							<div class="form-group" align="center">
								<div class="col-md-2"></div>
								<div class="col-md-1">
									<label> Between Dates</label>
								</div>
								<div class="col-md-3">
									
										<label>From Date</label>
										<input type="text" name="from_date" id="from_date" class="form-control" required />
									
								</div>
								<div class="col-md-3">
									
										<label>To Date</label>
										<input type="text" name="to_date" id="to_date" class="form-control" required />
									
								</div>	
							</div>
					</div>
					<br />
					<div class="row" >
						<div class="form-group" align="center">
							<div class="col-md-5"></div>
							<div class="col-md-2">									
								<button type="button" name="filter" id="filter" class="btn btn-info">Filter</button>
							</div>
						</div>
					</div>
                    </div>
					
                </div>
                <div class="panel-body">
                	<table id="sales_data" class="table table-bordered table-striped">
					<caption><h1 align="center"><strong>Detailed Sales Reports</strong></h1></caption>
                		<thead>
							<tr  align="center" >
								<th rowspan="2">Sl. No</th>
								<th rowspan="2">Name & Address of the Customer</th>
								<th rowspan="2">GSTIN</th>
								<th rowspan="2">Invoice No</th>
								<th rowspan="2">Date of Sale</th>
								<th rowspan="2" class="sum">Value of Tax Free Goods</th>
								<th rowspan="1" class="sum" colspan="4">Value</th>
								<th rowspan="2" class="sum" >Value Sub Total Amount</th>
								<th rowspan="1" colspan="4" class="sum">SGST</th>
								<th rowspan="1" colspan="4" class="sum">CGST</th>
								<th rowspan="2" class="sum">GST Sub Total Amount</th>
								<th rowspan="2" class="sum">URD Sales Value</th>
							</tr>
							<tr>
								<th class="sum">Rs. 2.5%   Ps</th>
								<th class="sum">Rs. 6%   Ps</th>
								<th class="sum">Rs. 9%   Ps</th>
								<th class="sum">Rs. 14%   Ps</th>
								<th class="sum">Rs. 2.5%   Ps</th>
								<th class="sum">Rs. 6%   Ps</th>
								<th class="sum">Rs. 9%   Ps</th>
								<th class="sum">Rs. 14%   Ps</th>
								<th class="sum">Rs. 2.5%   Ps</th>
								<th class="sum">Rs. 6%   Ps</th>
								<th class="sum">Rs. 9%   Ps</th>
								<th class="sum">Rs. 14%   Ps</th>
							</tr>
							
						</thead>
						<tfoot>
							<tr>
								
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td><center><b>Total Amount in Rupees</b><center></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						</tfoot>
						<tbody>
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
    					<input type="hidden" name="inventory_order_id" id="inventory_order_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Search" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
		
<script type="text/javascript">

	
    
			
			
	$(document).ready(function(){
	
		fill_datatable();
		function fill_datatable(from_date='',to_date='')
		{
		 $('#sales_data tfoot th').each( function () {
			var title = $(this).text();
			
			if(title !== 'Sl.No')
			{
				$(this).html( '<input type="text" id="'+title+'" placeholder="Search '+title+'" size="6" />' );
			}
				
			} );
			var salesdataTable = $('#sales_data').DataTable({
				"processing":true,
				"serverSide":true,
				"searching":true,
				"sales":[],
				"ajax":{
					url:"dsales_report_fetch.php",
					type:"POST",
					data:{from_date:from_date,to_date:to_date}
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
				"footerCallback": function ( row, data, start, end, display ) {
				var api = this.api(), data;
				// Remove the formatting to get integer data for summation
				var intVal = function ( i ) {
					return typeof i === 'string' ?
						i.replace(/[\$,]/g, '')*1 :
						typeof i === 'number' ?
							i : 0;
				};
				 
				api.columns('.sum', { page: 'all'}).every( function () {
				  var sum = this
					.data()
					.reduce( function (a, b) {
						return intVal(a) + intVal(b);
					}, 0 );
				  
				  this.footer().innerHTML = sum;
				} );
				},
				"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
				
			});
			salesdataTable.columns().every( function ( colIdx ) {
				$( 'input', salesdataTable.column( colIdx ).footer() ).on( 'keyup change', function () {
			//		alert(salesdataTable.colIdx.value);
				salesdataTable.column(colIdx).search( this.value ).draw();
				} );
			} );
		}
		
		$('#filter').click(function(){
			var from_date =  $('#from_date').val();
			var to_date =  $('#to_date').val();
			if(from_date != '' && to_date != '')
			{
				$('#sales_data').DataTable().destroy();
				fill_datatable(from_date,to_date);
			}
			else
			{
				alert('Select Both the Filters');
				$('#sales_data').DataTable().destroy();
				fill_datatable();
			}
		});
		
		$('#search_button').click(function(){
			$('#salesModal').modal('show');
			$('#sales_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Search By Month");
			$('#action').val('search');
			$('#btn_action').val('search');
		});
		
		$(document).on('submit', '#sales_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"sales_report_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#sales_form')[0].reset();
					$('#salesModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					salesdataTable.ajax.reload();
				}
			});
		});


    });
</script>