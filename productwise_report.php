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
                            <h1 class="panel-title" style="font-weight:bold;font-size:30px;">View Stock Details of</h1>
                        </div>
					</div>
					<br />
					<div class="row" >
							<div class ="col-lg-3"></div>
							<div class="form-group col-lg-6" align="center">
									<select name="product_id" id="product_id" class="form-control selectpicker product_id" data-live-search="true" required>
										<?php echo fill_product_list($connect); ?>
									</select>
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
        
                <div class="table-responsive">
                	<table id="stock_data" class="table table-bordered table-striped">
					<caption><h1 align="center"><strong>Product Wise Stock Details</strong></h1></caption>
                		<thead>
							<tr  align="center" >
								<th rowspan="1">Sl. No</th>
								<th rowspan="1">Product Name</th>
								<th rowspan="1">Date of Stock Entry/Sale</th>
								<th rowspan="1">Stock-In (In Pieces)</th>
								<th rowspan="1">Stock-Out (In Pieces)</th>
								<th rowspan="1">Total Stock(In Pieces)</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th colspan="5"><center><b>Current Stock</b></center></th>
								<th></th>
							</tr>
						</tfoot>
                	</table>
                </div>
	</div>
   
		
<script type="text/javascript">
	$(document).ready(function(){
		fill_datatable();
		function fill_datatable(product_id='')
		{
			var stockdataTable = $('#stock_data').DataTable({
			"processing":true,
			"serverSide":true,
			"searching":true,
			"stock":[],
			"ajax":{
					url:"product_report_fetch.php",
					type:"POST",
					data:{product_id:product_id}
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
              
              this.footer().innerHTML = sum.toFixed(2);
				} );
				},
				"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
			
				
			});
		}
		$('#filter').click(function(){
			var product_id =  $('#product_id').val();
			if(product_id != '')
			{
				//alert(product_id);
				$('#stock_data').DataTable().destroy();
				fill_datatable(product_id);
			}
			else
			{
				alert('Select product name');
				$('#stock_data').DataTable().destroy();
				fill_datatable();
			}
		});

    });
</script>