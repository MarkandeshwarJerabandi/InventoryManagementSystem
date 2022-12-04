<?php
//customer.php

include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
    header('location:login.php');
}

if($_SESSION['type'] != 'master')
{
    header('location:index.php');
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
<script type="text/javascript">
function fnExcelReport()
{
    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('customer_data'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE "); 

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
    {
        txtArea1.document.open("txt/html","replace");
        txtArea1.document.write(tab_text);
        txtArea1.document.close();
        txtArea1.focus(); 
        sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
    }  
    else                 //other browser not tested on IE 11
        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

    return (sa);
}
</script>
        <span id='alert_action'></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">Customers List</h3>
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add New</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="customer_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>
                                    <th>Customer Name</th>
									<th>Firm Name</th>
                                    <th>Address</th>
									<th>Deatils of Consignee(Shipped Details)</th>
									<th>ZipCode</th>
									<th>Customer Type</th>
									<th>GSTIN</th>
                                    <th>Contact Number</th>
                                    <th>Email ID</th>
                                    <th>Enter By</th>
                                    <th>Status</th>
									<th></th>
									<th></th>
									<th></th>
                                </tr></thead>
                            </table>
							<button id="btnExport" onclick="fnExcelReport();"> EXPORT </button>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

		<script>
	$(document).click(function(){
		$('#outstanding_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>
        <div id="customerModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="customer_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Customer</h4>
                        </div>
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <label>Enter Customer Name</label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control" required pattern="[a-zA-Z ]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter Firm Name(Optional)</label>
                                <input type="text" name="firm_name" id="firm_name" class="form-control" pattern="[a-zA-Z ]+"/>
                            </div>
                            <div class="form-group">
                                <label>Enter Address</label>
                                <textarea name="address" id="address" class="form-control" rows="5" required></textarea>
                            </div>
							<div class="form-group">
                                <label>Enter Details of Consignee</label>
								<textarea name="place" id="place" class="form-control" rows="5" ></textarea>
                            </div>
							<div class="form-group">
                                <label>Enter Zipcode(Optional)</label>
                                <input type="text" name="zipcode" id="zipcode" class="form-control" pattern="[0-9]+"/>
                            </div>
							<div class="form-group">
                                <label>Customer Type</label>
                                <select id="customer_type" name="customer_type" required>
									<option value="">Select Customer Type</option>
									<option value="Unregistered">Unregistered Consumer</option>
									<option value="Registered Business Regular">Registered Business - Regular</option>
									<option value="Registered Business Composite">Registered Business - Composite</option>
									<option value="Outlet">Outlet</option>
								</select>
                            </div>
							<div class="form-group" id="GSTNdiv">
                                <label>Enter GSTIN</label>
                                <input type="text" name="GSTIN" id="GSTIN" class="form-control" pattern="[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z][0-9][a-zA-Z][0-9A-Za-z]"/>
                            </div>
                            <div class="form-group">
                                <label>Enter Contact Number</label>
                                <div class="input-group">
                                    <input type="text" name="contact_no" id="contact_no" class="form-control" required pattern="[0-9]{10}" /> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Enter Email ID(optional)</label>
                                <input type="email" name="email_id" id="email_id" class="form-control"  />
                            </div>
							<div class="form-group">
                                <label>Enter Current Outstanding Balance as on <input type="date" class="form-control" id="outstanding_date" required name="outstanding_date"/></label>
                                <input type="text" name="current_outstanding" id="current_outstanding" pattern="[0-9]+" required class="form-control"  />
                            </div>
							
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="customer_id" id="customer_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="customerdetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="customer_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Customer Details</h4>
                        </div>
                        <div class="modal-body">
                            <Div id="customer_details"></Div>
                        </div>
                        <div class="modal-footer">
                            
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

<script>
$(document).ready(function(){
	
	
    var customerdataTable = $('#customer_data').DataTable({
        "processing":true,
        "serverSide":true,
		"searching":true,
        "order":[],
        "ajax":{
            url:"customer_fetch.php",
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

    $('#add_button').click(function(){
        $('#customerModal').modal('show');
        $('#customer_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Customer");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });

    $(document).on('change', '#customer_type', function(event){
		var ct = $('#customer_type').val();
		if(ct == '' || ct == 'Unregistered')
		{
		//	alert(ct);
			$('#GSTNdiv' ).toggle(false);
			$('#GSTIN').attr('required',false);
			
		}
		else
		{
			$('#GSTNdiv').toggle(true);
			$('#GSTIN').attr('required',true);
			$('#GSTIN').attr('readonly',false);
		}	
		
	});
    $(document).on('submit', '#customer_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"customer_action.php",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                $('#customer_form')[0].reset();
                $('#customerModal').modal('hide');
                $('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
                $('#action').attr('disabled', false);
                customerdataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var customer_id = $(this).attr("id");
        var btn_action = 'customer_details';
        $.ajax({
            url:"customer_action.php",
            method:"POST",
            data:{customer_id:customer_id, btn_action:btn_action},
            success:function(data){
                $('#customerdetailsModal').modal('show');
                $('#customer_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var customer_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"customer_action.php",
            method:"POST",
            data:{customer_id:customer_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#customerModal').modal('show');
                $('#customer_name').val(data.customer_name);
				$('#firm_name').val(data.firm_name);
                $('#address').val(data.address);
				$('#place').val(data.place);
				$('#zipcode').val(data.zipcode);
				$('#customer_type').val(data.customer_type);
				if(data.customer_type!='' || data.customer_type!='Unregistered')
				{
					$('#GSTIN').val(data.GSTIN);
					$('#GSTIN').attr('readonly',true);
				}
				else
				{
					$('#GSTIN').val(data.GSTIN);
					$('#GSTIN').attr('readonly',false);
				}
                $('#contact_no').val(data.contact_no);
                $('#email_id').val(data.email_id);
				$('#current_outstanding').val(data.current_outstanding)
				$('#current_outstanding').attr('readonly', true);
				$('#outstanding_date').val(outstanding_date);
				$('#outstanding_date').attr('readonly', true);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Customer");
                $('#customer_id').val(customer_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var customer_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to change status?"))
        {
            $.ajax({
                url:"customer_action.php",
                method:"POST",
                data:{customer_id:customer_id, status:status, btn_action:btn_action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    customerdataTable.ajax.reload();
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
