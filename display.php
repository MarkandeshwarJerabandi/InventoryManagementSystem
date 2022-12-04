<?php
//product.php

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
<style type="text/css">
blink {
    -webkit-animation: 2s linear infinite condemned_blink_effect; // for android
    animation: 2s linear infinite condemned_blink_effect;
}
@-webkit-keyframes condemned_blink_effect { // for android
    0% {
        visibility: hidden;
    }
    50% {
        visibility: hidden;
    }
    100% {
        visibility: visible;
    }
}
@keyframes condemned_blink_effect {
    0% {
        visibility: hidden;
    }
    50% {
        visibility: hidden;
    }
    100% {
        visibility: visible;
    }
}
</style>

	
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<script src="js/bootstrap-select.min.js"></script>

<script type="text/javascript">
$(document).ready(function(){
		$('#as_on_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});

function fnExcelReport()
{
    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
    var textRange; var j=0;
    tab = document.getElementById('product_data'); // id of table

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
                            	<h3 class="panel-title">Product Display List</h3>
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add New</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="product_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>Display ID</th>
                                    <th>Category</th>
                                    <th>Product Name</th>
									<th>Supplier Name</th>
									<th>HSN Code</th>
									<th>Size</th>
									<th>Grade</th>
									<th>Date of Display</th>
									<th>No of Units Displayed</th>
									<th>Unit Rate</th>
									<th>Total Display Amount</th>
                                    <th>Entered By</th>
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

        <div id="productModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Product to Display</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Select Category</label>
                                <select name="category_id" id="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php echo fill_category_list($connect);?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Product Name</label>
                                <select name="product_name" id="product_name" class="form-control" required />
                                    <?php echo fill_product_HSG_list($connect);?>
                                </select>
                            </div>
							<div class="form-group">
                                <label>Select Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-control" required>
                                    <option value="">Select Supplier</option>
									<?php echo fill_supplier_list($connect);?>
                                </select>
                            </div>
							<div class="form-group" id="dod" name="dod">
                                <label>Select Date of Display</label>
                                <input type="date" name="date_of_display" id="date_of_display" class="form-control" />
                            </div>
							<div class="form-group" id="ud" name="ud">
                                <label>Enter Number of Units to Display</label>
                                <input type="number" name="unit_display" id="unit_display" class="form-control" pattern="[0-9]+" />
                            </div>
							<div class="form-group" id="ur" name="ur">
                                <label>Enter Unit Rate</label>
                                <input type="number" name="unit_rate" id="unit_rate" class="form-control" pattern="[0-9]+" />
                            </div>
							<div class="form-group" id="total" name="total">
                                <label>Total Display Amount</label>
                                <input type="number" name="total_display_amount" id="total_display_amount" class="form-control" readonly pattern="[0-9]+" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="display_id" id="display_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="productdetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Display Product Details</h4>
                        </div>
                        <div class="modal-body">
                            <Div id="product_details"></Div>
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
	
    var productdataTable = $('#product_data').DataTable({
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"display_fetch.php",
            type:"POST"
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
        "pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
    });

    $('#add_button').click(function(){
        $('#productModal').modal('show');
        $('#product_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product to display");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });
	
	
    $('#category_id').change(function(){
        var category_id = $('#category_id').val();
        var btn_action = 'load_supplier';
        $.ajax({
            url:"display_action.php",
            method:"POST",
            data:{category_id:category_id, btn_action:btn_action},
            success:function(data)
            {
                $('#supplier_id').html(data);
            }
        });
    });

	$(document).on('keyup', '#unit_rate', function(event){
        var unit_display = $('#unit_display').val();
		var unit_rate = $('#unit_rate').val();
        var total_amount = unit_display * unit_rate;
		$('#total_display_amount').val(total_amount);
    });
	
    $(document).on('submit', '#product_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"display_action.php",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                $('#product_form')[0].reset();
                $('#productModal').modal('hide');
                $('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
                $('#action').attr('disabled', false);
                productdataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var display_id = $(this).attr("id");
        var btn_action = 'product_details';
        $.ajax({
            url:"display_action.php",
            method:"POST",
            data:{display_id:display_id, btn_action:btn_action},
            success:function(data){
                $('#productdetailsModal').modal('show');
                $('#product_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var display_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"display_action.php",
            method:"POST",
            data:{display_id:display_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#productModal').modal('show');
                $('#category_id').val(data.category_id);
                $('#product_name').val(data.product_name);
				$('#supplier_id').html(data.supplier_select_box);
                $('#supplier_id').val(data.supplier_id);
				$('#date_of_display').val(data.date_of_display);
				$('#unit_display').val(data.unit_display);
				$('#unit_rate').val(data.unit_rate);
				
				$('#total_display_amount').val(data.total_display_amount);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Display Details of Product");
                $('#display_id').val(display_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var display_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to change status?"))
        {
            $.ajax({
                url:"display_action.php",
                method:"POST",
                data:{display_id:display_id, status:status, btn_action:btn_action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    productdataTable.ajax.reload();
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