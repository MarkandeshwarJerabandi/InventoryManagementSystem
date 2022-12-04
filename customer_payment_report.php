<?php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('report_header.php');


?>

	<link rel="stylesheet" type="text/css" href="css/datatables.min.css"/>
 
<!--	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>		-->
	
	<script src="js/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="css/jquery.mobile.min.css">
	<link rel="stylesheet" href="css/jquery-ui.css">
	<script src="js/jquery.min.js"></script>
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
                            <h3 class="panel-title">Customer Payment List</h3>
                        </div>
                        
                    </div>
                </div>
                <div class="panel-body">
                	<table id="payment_data" class="table tabel-bordered table-stripped" border="1">
					<caption><h1 align="center"><strong>Customer Payment Due Report</strong></h1></caption>
                		<thead>
							
							<tr>
								<th>SL No</th>
								<th>Customer Name</th>
								<th class="sum">Total Amount to be Paid</th>
								<th class="sum">Amount Paid</th>
								<th class="sum">Balance</th>
								
							</tr>
							
						</thead>
						
						
						
						<tfoot>
							<tr>
								<th colspan="2">Total Amount in Rupees</th>
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

    <div id="paymentModal" class="modal fade">
-
    	<div class="modal-dialog">
    		<form method="post" id="payment_form">
    			<div class="modal-content">
				
    				<div class="modal-footer">
    					<input type="hidden" name="inventory_order_id" id="inventory_order_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
		
<script type="text/javascript">
	
    $(document).ready(function(){
		
		$('#purchase_data tfoot th').each( function () {
			var title = $(this).text();
			if(title !== 'Sl.No')
			{
				$(this).html( '<input type="text" id="'+title+'" placeholder="Search '+title+'" size="6" />' );
			}
			} );
		var paymentdataTable = $('#payment_data').DataTable({
			"processing":true,
			"serverSide":true,
			"searching":true,
			"footer":true,
			"payment":[],
			"ajax":{
				url:"customer_payment_report_fetch.php",
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
                    return Math.round((intVal(a) + intVal(b)),2);
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

    });
</script>