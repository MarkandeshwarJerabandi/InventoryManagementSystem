<?php
//stock_report.php

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
                            <h3 class="panel-title" style="font-weight:bold;">Product Wise Consolidated Stock Report</h3>
                        </div>
					</div>
					<br />
					<div class="row" >
							<div class="form-group" align="center">
								<div class="col-md-2">Generate Stock Details for </div>
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
        </div>
                <div class="panel-body">
					
                	<table id="stock_data" class="table table-bordered table-striped">
					<caption><h1 align="center"><strong>Stock Report and Closing Balance</strong></h1></caption>
                		<thead>
							<tr  align="center" >
								<th rowspan="1">Sl. No</th>
								<th rowspan="1">Product Name</th>
								<th rowspan="1" class="sum">Opening Balance</th>
								<th rowspan="1" class="sum">Purchase</th>
							<!--	<th rowspan="1" class="sum">Purchase Amount</th>	-->
								<th rowspan="1" class="sum">Sales</th>
								<th rowspan="1" class="sum">Sales Amount</th>
								<th rowspan="1" class="sum">Closing Balance</th>
								<th rowspan="1" class="sum">Closing Balance Amount</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="2"><center><b>Total Count</b></center></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								
							</tr>
						</tfoot>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="stockModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="stock_form">
    			<div class="modal-content"> 
					<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Search By Month </h4>
    				</div>
					<div class="modal-body">
    					<div class="row">
						
							<div class="col-md-12">
								<div class="form-group">
									<label>Month of Purchase</label>
									
								</div>
							</div>
						</div>
					</div>
					
    				<div class="modal-footer">
    					<input type="hidden" name="product_id" id="product_id" />
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
			var stockdataTable = $('#stock_data').DataTable({
			"processing":true,
			"serverSide":true,
			"searching":true,
			"footer":true,
			"stock":[],
			"ajax":{
				url:"stock_report_fetch.php",
				type:"POST",
				data:{from_date:from_date,to_date:to_date}
			},
			"dom":'lBfrtrip',
			"columnDefs":[
				{
					"targets":'_all',
					"orderable":false,
					"searching":true,
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
              
              this.footer().innerHTML = Number(sum).toFixed(2);
				} );
				},
				
				
				"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
					//debugger;
					var index = iDisplayIndexFull + 1;
					$("td:first", nRow).html(index);
					return nRow;
				  },
				"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]	
			 
				
			});
		}
		
		$('#filter').click(function(){
			var from_date =  $('#from_date').val();
			var to_date =  $('#to_date').val();
			if(from_date != '' && to_date != '')
			{
				$('#stock_data').DataTable().destroy();
				fill_datatable(from_date,to_date);
			}
			else
			{
				alert('Select Both the Filters');
				$('#stock_data').DataTable().destroy();
				fill_datatable();
			}
		});
		
		
		$('#search_button').click(function(){
			$('#stockModal').modal('show');
			$('#stock_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Search By Month");
			$('#action').val('search');
			$('#btn_action').val('search');
		});
		
		$(document).on('submit', '#stock_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"stock_report_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#stock_form')[0].reset();
					$('#stockModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					stockdataTable.ajax.reload();
				}
			});
		});


    });
</script>