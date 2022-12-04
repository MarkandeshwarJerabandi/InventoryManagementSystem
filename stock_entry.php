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
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Stock Entry</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Click to Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
					<table id="stock_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Stock Entry ID</th>
								<th>Date of Entry</th>
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
						<h4 class="modal-title"><i class="fa fa-plus"></i> Stock Entry </h4>
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<label>Date of Entry</label>
									<input type="text" name="date_of_entry" id="date_of_entry" class="form-control" required readonly />
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label>Enter Product Details</label>
							<hr />
							<div class="col-md-3">
								<label>Select Product </label>
							</div>
							<div class="col-md-3">
								<label>Quantity</label>
							</div>
							<div class="col-md-4">
								<label>Select Unit of Measurment</label>
							</div>
							<div class="col-md-1">
								<label>Add More</label>
							</div>
							<div class="col-md-1">
								<label>Remove</label>
							</div>
							<span id="span_product_details"></span>
							<hr />
						</div>
						
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="stock_entry_id" id="stock_entry_id" />
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
		
    	var purchasedataTable = $('#stock_data').DataTable({
			"processing":true,
				"serverSide":true,
				"searching":true,
				"stock":[],
				"ajax":{
					url:"stock_entry_fetch.php",
					type:"POST",
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

		
		
		$('#add_button').click(function(){
			$('#purchaseModal').modal('show');
			$('#purchase_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Stock Entry");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});

		function add_product_row(count = '')
		{	
			//alert("count passed:"+count);
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-3">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker product_id" data-live-search="true" required>';
			html += '<?php echo fill_product_HSG_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" class="form-control hidden_product_id" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-3">';
			html += '<input type="text" name="quantity[]" id="quantity'+count+'" class="form-control quantity" required pattern="[0-9]+"/>';
			html += '</div>';
			html += '<div class="col-md-4">';
			html += '<select name="product_unit[]" id="product_unit'+count+'" class="form-control selectpicker product_unit" data-live-search="true" required>';
			html += '<?php echo fill_product_units(); ?>';
			html += '</select><input type="hidden" name="hidden_product_unit[]" id="hidden_product_unit'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">-</button>';
			html += '</div>';
			html += '</div></div><br /></span>';
			$('#span_product_details').append(html);
			$('.selectpicker').selectpicker();
		}
		
		
		$(document).on('click', '#add_more', function(){
		//	alert(count);
		//	old_count_hidden = document.getElementById("old_count_hidden").value;
		//	alert(old_count_hidden);
			count = count + 1;
		//	alert(count);
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			deleted_row_id[c]=row_no;
			c=c+1;
		//	alert("rowno:" +row_no);
		//	old_count = count;
	//		count = count - 1;
			$('#row'+row_no).remove();
			find_bill_amount();
		});
		
		$(document).on('click', '#recalculate', function(){
			find_bill_amount();
			});	
		var SGST,CGST;
		function inArray(j,deleted_row_id)
		{
			var count=deleted_row_id.length;
		//	alert("j:"+j);
			for(var k=0;k<count;k++)
			{
		//		alert("deleted_row_id[k]:"+deleted_row_id[k]);
				if(deleted_row_id[k]==j)
					return true;
			}
			return false;
		}
		
		function find_bill_amount()
		{
			var bill_amount=0,SGST_tax=0,CGST_tax=0;	
			var q_id, uc_id,uom_id;
			var q_value,uc_value,uom_value;
			var tax_data;
			var q_values = [];
			$("input[name='quantity[]']").each(function() {
				q_values.push($(this).val());
			});
	//		alert(q_values);
			var u_values = [];
			$("input[name='unit_cost[]']").each(function() {
				u_values.push($(this).val());
			});
	//		alert(u_values);
			var p_values = [];
			$("select[name='product_id[]']").each(function() {
				var attr_id = $(this).attr('id');
				p_values.push($('#'+attr_id+' option:selected').val());
			});	
	//		alert(p_values);
			
			var p_u_values = [];
			$("select[name='product_unit[]']").each(function() {
				var attr_id = $(this).attr('id');
				p_u_values.push($('#'+attr_id+' option:selected').val());
			});
	//		alert(p_u_values);
			
		//	alert(p_values.length);
		
			for(i=0;i<p_values.length;i++)
			{
				product_id = p_values[i];
				quantity = q_values[i];
				uc_value = u_values[i];
		//		alert(product_id);
				var btn_action = 'fetch_SGST_CGST';
				var tax_data = function(){
					var temp=null;
					$.ajax({
						url:"purchase_action.php",
						method:"POST",
						async:false,
						global:false,
						data:{product_id:product_id, btn_action:btn_action},
						dataType:"json",
						success:function(data)
						{
							temp = data;
						}
					});
					return temp;
					}();
				SGST = tax_data.SGST;
				CGST = tax_data.CGST;
				bill_amount += parseFloat((quantity*uc_value).toFixed(2));
				SGST_tax += parseFloat(((quantity*uc_value*SGST)/100).toFixed(2));
				CGST_tax += parseFloat(((quantity*uc_value*CGST)/100).toFixed(2));	
			}	
			
			$('#bill_amount').val(parseFloat(bill_amount).toFixed(2));
			$('#SGST').val(parseFloat(SGST_tax).toFixed(2));
			$('#CGST').val(parseFloat(CGST_tax).toFixed(2));
			$('#total_amount').val(parseFloat(bill_amount+SGST_tax+CGST_tax).toFixed(2));	
			
		}
		
		$(document).on('submit', '#purchase_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"stock_entry_action.php",
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
			var stock_entry_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"stock_entry_action.php",
				method:"POST",
				data:{stock_entry_id:stock_entry_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#purchaseModal').modal('show');
					$('#date_of_entry').val(data.date_of_entry);
					$('#span_product_details').html(data.product_details);
					$('#old_count_hidden').val(data.old_count_hidden);
					count = data.old_count_hidden;
				//	alert("old:"+count);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Stock");
					$('#stock_entry_id').val(stock_entry_id);
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