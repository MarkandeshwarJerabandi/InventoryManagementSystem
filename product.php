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
                            	<h3 class="panel-title">Product List</h3>
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
                                    <th>ID</th>
                                    <th>Product Name</th>
									<th>HSN Code</th>
									<th>Weight</th>
									<th>Unit of Measurement</th>
                                    <th>Tax Status</th>
									<th>SGST</th>
									<th>CGST</th>
									<th>Minimum Stock Quantity</th>
									<th>Initial Stock Quantity</th>
									<th>Date of Initial Stock Quantity</th>
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

        <div id="productModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Product</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Enter Product Name</label>
                                <input type="text" name="product_name" id="product_name" class="form-control" required />
                            </div>
							<div class="form-group">
                                <label>Enter HSN Code</label>
                                <input type="number" name="HSN_code" id="HSN_code" class="form-control" />
                            </div>
							<div class="form-group">
                                <label>Enter Weight</label>
                                <input type="text" name="size" id="size" class="form-control" />
                            </div>
					<!--		<div class="form-group">
                                <label>Enter Grade</label>
                                <input type="text" name="grade" id="grade" class="form-control" />
							</div>	-->
                            <div class="form-group">
                                <label>Enter Unit of Measurement</label>
                                <select name="product_unit" id="product_unit" class="form-control" required>
                                            <option value="">Select Unit</option>
                                            <option value="Bags">Bags</option>
                                            <option value="Box">Box</option>
                                            <option value="Dozens">Dozens</option>
                                            <option value="Kg">Kg</option>
                                            <option value="Liters">Liters</option>
                                            <option value="Packet">Packet</option>
											<option value="Pieces">Pieces</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Select Tax Status</label>
                                <select name="tax_status" id="tax_status" class="form-control" required>
                                            <option value="">Select Tax Status</option>
                                            <option value="taxable">Taxable</option>
											<option value="non-taxable">Non-Taxable</option>
								</select>
                            </div>
                            <div class="form-group">
                                <label>Enter SGST %</label>
                                <input type="text" name="SGST" id="SGST" class="form-control" required pattern="[0-9]*\.?[0-9]+" />
                            </div>
							<div class="form-group">
                                <label>Enter CGST %</label>
                                <input type="text" name="CGST" id="CGST" class="form-control" required pattern="[0-9]*\.?[0-9]+" />
                            </div>
                            <div class="form-group">
                                <label>Enter Minimum Stock Quantity</label>
                                <input type="text" name="min_stock_quantity" id="min_stock_quantity" class="form-control" required pattern="[0-9]+" />
                            </div>
							<div class="form-group">
                                <label>Enter Initial Stock Quantity<span id="c_value" name="c_value"></span></label>
                                <input type="text" name="init_stock_quantity" id="init_stock_quantity" class="form-control" required pattern="[0-9]*\.?[0-9]+" />
								<label>Enter Date of Initial Stock Quantity </label>
								<input type="text" name="as_on_date" id="as_on_date" class="form-control" required />
                            </div>
							
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="product_id" id="product_id" />
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
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Product Details</h4>
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
            url:"product_fetch.php",
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
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });
	
	
	

	$('#tax_status').change(function(){
        var tax_status = $('#tax_status').val();
		
        if((tax_status.localeCompare("non-taxable"))==0)
		{
			
			var btn_action = 'fill_sgst';
			$.ajax({
				url:"product_action.php",
				method:"POST",
				data:{tax_status:tax_status, btn_action:btn_action},
				success:function(data)
				{
				//	alert(tax_status);
				//	$('#SGST').html(data);
					$('#SGST').val(0);
					$('#CGST').val(0);
				}
			});
		}
		else
		{
			$('#SGST').val(null);
			$('#CGST').val(null);
		}
    });
	
    $(document).on('submit', '#product_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"product_action.php",
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
        var product_id = $(this).attr("id");
        var btn_action = 'product_details';
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:{product_id:product_id, btn_action:btn_action},
            success:function(data){
                $('#productdetailsModal').modal('show');
                $('#product_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var product_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:{product_id:product_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#productModal').modal('show');
                //$('#category_id').val(data.category_id);
                $('#product_name').val(data.product_name);
				//$('#supplier_id').html(data.supplier_select_box);
                //$('#supplier_id').val(data.supplier_id);
				$('#HSN_code').val(data.HSN_code);
				$('#size').val(data.size);
				//$('#grade').val(data.grade);
                $('#product_unit').val(data.product_unit);
				//$('#unit_conversion').val(data.unit_conversion);
				$('#tax_status').val(data.tax_status);
                $('#SGST').val(data.SGST);
				$('#CGST').val(data.CGST);
                $('#min_stock_quantity').val(data.min_stock_quantity);
				$('#init_stock_quantity').val(data.init_stock_quantity);
				$('#as_on_date').val(data.as_on_date);
			//	$('#initial_stock_quantity').val(data.initial_stock_quantity);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Product");
                $('#product_id').val(product_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var product_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to change status?"))
        {
            $.ajax({
                url:"product_action.php",
                method:"POST",
                data:{product_id:product_id, status:status, btn_action:btn_action},
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
