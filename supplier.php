<?php
//supplier.php

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
    tab = document.getElementById('supplier_data'); // id of table

    for(j = 0 ; j < tab.rows.length ; j++) 
    {     
        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
        //tab_text=tab_text+"</tr>";
    }

    tab_text=tab_text+"</table>";
    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // removes input params

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
                            	<h3 class="panel-title">Suppliers List</h3>
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add New</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="supplier_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>
									<th>Farmer Name</th>
                                    <th>Address</th>
									<th>Contact Number</th>
									<th>Alternate Contact Number</th>
                                    <th>Email ID</th>
									<th>ZipCode</th>
									<th>GSTIN</th>
									<th>Bank Name</th>
									<th>Branch Name</th>
									<th>Bank Account Name</th>
									<th>Bank A/c Number</th>
									<th>IFSC Code</th>
                                    <th>Entered By</th>
                                    <th>Status</th>
									<th></th>
									<th></th>
									<th></th>
                                </tr></thead>
                            </table>
							
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div id="supplierModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="supplier_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add supplier</h4>
                        </div>
                        <div class="modal-body">
                            
                            <div class="form-group">
                                <label>Enter Farmer Name</label>
                                <input type="text" name="firm_name" id="firm_name" class="form-control" required pattern="[a-zA-Z ]+"/>
                            </div>
							
                            <div class="form-group">
                                <label>Enter Address</label>
                                <textarea name="address" id="address" class="form-control" rows="5" required></textarea>
                            </div>
							<div class="form-group">
                                <label>Enter Contact Number</label>
                                <div class="input-group">
                                    <input type="text" name="contact_no" id="contact_no" class="form-control" required pattern="[0-9]{10}" /> 
                                </div>
                            </div>
							<div class="form-group">
                                <label>Enter Alternate Contact Number(Optional)</label>
                                <div class="input-group">
                                    <input type="text" name="alt_contact_no" id="alt_contact_no" class="form-control" pattern="[0-9]{10}" /> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Enter Email ID(Optional)</label>
                                <input type="email" name="email_id" id="email_id" class="form-control" />
                            </div>
							<div class="form-group">
                                <label>Enter Zipcode(Optional)</label>
                                <input type="text" name="zipcode" id="zipcode" class="form-control"  pattern="[0-9]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter GSTIN</label>
                                <input type="text" name="GSTIN" id="GSTIN" class="form-control" pattern="[0-9]{2}[a-zA-Z]{5}[0-9]{4}[a-zA-Z][0-9][a-zA-Z][0-9A-Za-z]"/>
                            </div>
                            
							<div class="form-group">
                                <label>Enter Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" required pattern="[a-zA-Z ]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter Branch Name(Optional)</label>
                                <input type="text" name="branch_name" id="branch_name" class="form-control" pattern="[a-zA-Z ]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter Bank Account Name</label>
                                <input type="text" name="bank_act_name" id="bank_act_name" class="form-control" required pattern="[a-zA-Z ]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter Bank Account Number</label>
                                <input type="text" name="bank_act_no" id="bank_act_no" class="form-control" required pattern="[0-9]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter IFSC Code</label>
                                <input type="text" name="IFSC_code" id="IFSC_code" class="form-control" required pattern="[a-zA-z0-9]+"/>
                            </div>
							<div class="form-group">
                                <label>Enter Current Outstanding Balance as on <input type="date" id="outstanding_date" required name="outstanding_date"/></label>
                                <input type="text" name="current_outstanding" id="current_outstanding" pattern="[0-9]+" required class="form-control"  />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="supplier_id" id="supplier_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="supplierdetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="supplier_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Supplier Details</h4>
                        </div>
                        <div class="modal-body">
                            <Div id="supplier_details"></Div>
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
    var supplierdataTable = $('#supplier_data').DataTable({
        "processing":true,
        "serverSide":true,
		"searching":true,
        "order":[],
        "ajax":{
            url:"supplier_fetch.php",
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
        $('#supplierModal').modal('show');
        $('#supplier_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add supplier");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });

    

    $(document).on('submit', '#supplier_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"supplier_action.php",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                $('#supplier_form')[0].reset();
                $('#supplierModal').modal('hide');
                $('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
                $('#action').attr('disabled', false);
                supplierdataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var supplier_id = $(this).attr("id");
        var btn_action = 'supplier_details';
        $.ajax({
            url:"supplier_action.php",
            method:"POST",
            data:{supplier_id:supplier_id, btn_action:btn_action},
            success:function(data){
                $('#supplierdetailsModal').modal('show');
                $('#supplier_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var supplier_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"supplier_action.php",
            method:"POST",
            data:{supplier_id:supplier_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#supplierModal').modal('show');
                $('#firm_name').val(data.firm_name);
				//$('#contact_person_name').val(data.contact_person_name);
                $('#address').val(data.address);
				$('#contact_no').val(data.contact_no);
				$('#alt_contact_no').val(data.alt_contact_no);
                $('#email_id').val(data.email_id);
				$('#zipcode').val(data.zipcode);
				$('#GSTIN').val(data.GSTIN);
				$('#bank_name').val(data.bank_name);
				$('#branch_name').val(data.branch_name);
                $('#bank_act_name').val(data.bank_act_name);
				$('#bank_act_no').val(data.bank_act_no);
                $('#IFSC_code').val(data.IFSC_code);
				$('#current_outstanding').val(data.current_outstanding)
				$('#current_outstanding').attr('readonly', true);
				$('#outstanding_date').val(outstanding_date);
				$('#outstanding_date').attr('readonly', true);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit supplier");
                $('#supplier_id').val(supplier_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var supplier_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to change status?"))
        {
            $.ajax({
                url:"supplier_action.php",
                method:"POST",
                data:{supplier_id:supplier_id, status:status, btn_action:btn_action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    supplierdataTable.ajax.reload();
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
