<?php
//sales.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
	
	
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="css/bootstrap-select.min.css">
	<script src="js/bootstrap-select.min.js"></script>
	<script>
	$(document).click(function(){
		$('#inventory_order_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#date_of_payment').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
		$('#cheque_date').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>
	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			
			<div class="panel panel-default">
                <div class="panel-heading">
                	<div class="row">
                    	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            <h3 class="panel-title">Quotation List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="quotation_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Quotation ID</th>
								<th>Quotation Date</th>
								<th>Customer Name</th>
								<th>Total Amount</th>
								<th>Quotation Status</th>
								<?php
								if($_SESSION['type'] == 'master')
								{
									echo '<th>Created By</th>';
								}
								?>
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

    <div id="quotationModal" class="modal fade">
-
    	<div class="modal-dialog">
    		<form method="post" id="quotation_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create Quotation</h4>
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Select Customer Name</label>
									<select name="customer_id" id="customer_id" class="form-control" required>
										<?php echo fill_customer_list($connect);?>										
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<input type="text" name="inventory_order_date" id="inventory_order_date" class="form-control" required />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Enter Product Details</label>
							<hr />
							<div class="col-md-3">
								<label>Select Product </label>
							</div>
							<div class="col-md-2">
								<label>Quantity</label>
							</div>
							<div class="col-md-3">
								<label>Select UoM</label>
							</div>
							<div class="col-md-2">
								<label>Unit Price</label>
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
						<div class="row">
							<div class="col-md-2">
								<button type="button" name="recalculate" id="recalculate" class="btn btn-warning btn-xxs recalculate">Calculate</button>
								<input type="hidden" name="cbill_amount" id="cbill_amount"></input>						
							</div>
							<div class="col-md-10">
								<label><span id="quotation_value"></span></label>
							</div>
							
						</div>
    				</div>
					<br />
					<br />
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

    	var orderdataTable = $('#quotation_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"quotation_fetch.php",
				type:"POST"
			},
			<?php
			if($_SESSION["type"] == 'master')
			{
			?>
			"columnDefs":[
				{
					"targets":[4, 5, 6, 7,8],
					"orderable":false,
				},
			],
			<?php
			}
			else
			{
			?>
			"columnDefs":[
				{
					"targets":[4, 5, 6],
					"orderable":false,
				},
			],
			<?php
			}
			?>
			"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
		});

		$('#add_button').click(function(){
			$('#quotationModal').modal('show');
			$('#quotation_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Quotation");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});
		function add_product_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-3">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker product_id" data-live-search="true" required>';
			html += '<?php echo fill_product_HSG_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" class="form-control hidden_product_id" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" name="quantity[]" id="quantity'+count+'" class="form-control quantity" required />';
			html += '</div>';
			html += '<div class="col-md-3">';
			html += '<select name="product_unit[]" id="product_unit'+count+'" class="form-control selectpicker product_unit" data-live-search="true" required>';
			html += '<?php echo fill_product_units(); ?>';
			html += '</select><input type="hidden" name="hidden_product_unit[]" id="hidden_product_unit'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" name="unit_cost[]" id="unit_cost'+count+'" class="form-control unit_cost" required />';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">-</button>';
			html += '</div>';
			html += '</div></div><br /></span>';
			$('#span_product_details').append(html);
		//	filterSelectedOptions();
			$('.selectpicker').selectpicker();
			
		}
		function filterSelectedOptions(){
           // ADD THE CODE TO FIX CURRENT ISSUE
           // To get the already selected values
            var selecteditems = document.getElementsByName('hidden_product_id');

			alert(selecteditems);
            $(document.getElementById("product_id"+count).options).each(function(index, option) {
                for (var i = 0; i <selecteditems.length; i++) {
                    var itm=selecteditems[i];
                    if( option.value == itm.value() ) {
                      option.hidden = true; // not fully compatible. option.style.display = 'none'; would be an alternative or $(option).hide();
						$(option).hide();
						alert(option.value);
                    }
                }

            });
           // Hint code end
       }
		
		
		
		$(document).on('click', '#recalculate', function(){
			$('#quotation_value1').remove(html);
			bill_amount = find_bill_amount();
			var grand_total = bill_amount;
			$('#bill_amount').val(grand_total);
			var html = '';
			
			//(Inclusive of Tax)
			html += '<span id="quotation_value1"><div class="row">';
			html += '<div class="col-md-12">';
			html += 'Total Bill Amount:';
			html += '<label>'+bill_amount+'</label>';
			html += '</div>';
			html += '</div></span>';
			$('#quotation_value').append(html);
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
	
			
			var bill_amount=0,SGST_tax=0,CGST_tax=0, grand_total=0;	
			
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
		//	alert(u_values);
			var p_values = [];
			$("select[name='product_id[]']").each(function() {
				var attr_id = $(this).attr('id');
				p_values.push($('#'+attr_id+' option:selected').val());
			});	
		//	alert(p_values);
			
			var p_u_values = [];
			$("select[name='product_unit[]']").each(function() {
				var attr_id = $(this).attr('id');
				p_u_values.push($('#'+attr_id+' option:selected').val());
			});
		//	alert(p_u_values);
			//var pdiscount = $('#pdiscount').val();
			//alert(pdiscount);
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
						url:"quotation_action.php",
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
		//		SGST_tax += parseFloat(((quantity*uc_value*SGST)/100).toFixed(2));
		//		CGST_tax += parseFloat(((quantity*uc_value*CGST)/100).toFixed(2));	
		
			}	
			$('#bill_amount').val(bill_amount);
			$('#SGST').val(SGST_tax);
			$('#CGST').val(CGST_tax);
			$('#total_amount').val(parseFloat(bill_amount).toFixed(2));	
			grand_total = parseFloat(bill_amount).toFixed(2);
			$('#cbill_amount').val(parseFloat(bill_amount).toFixed(2));
			return grand_total;
		}
			
		var count = 0;
		var deleted_row_id=[];
		var c=0;
		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			$('#quotation_value1').remove(html);
			var row_no = $(this).attr("id");
			deleted_row_id[c]=row_no;
			c=c+1;
	//		count = count - 1 ;
			$('#row'+row_no).remove();
			bill_amount = find_bill_amount();
		//	var pdiscount = $('#pdiscount').val();
		//	var discount_amount = ((bill_amount*pdiscount)/100).toFixed(2);
			var grand_total = bill_amount;
			$('#bill_amount').val(grand_total);
			var html = '';
			
			//(Inclusive of Tax)
			html += '<span id="quotation_value1"><div class="row">';
			html += '<div class="col-md-12">';
			html += 'Total Bill Amount:';
			html += '<label>'+bill_amount+'</label>';
			html += '</div>';
			html += '</div></span>';
			$('#quotation_value').append(html);
			
		});
		function reduce_total_amount(row_no)
		{
		//	alert(row_no);
			var bill_amount=0;	
			var q_id, uc_id;
			var q_value,uc_value;
			for(i=0;i<=count;i++)
			{
				
				if(i==0)
				{
					q_id = 'quantity' + '';
					uc_id = 'unit_cost' + '';
				}	
				else
				{
					q_id = 'quantity' + i;
					uc_id = 'unit_cost' + i;
				}	
				q_value = document.getElementById(q_id).value;
				uc_value = document.getElementById(uc_id).value;
				bill_amount += (q_value * uc_value);
			}
			alert(bill_amount);
			return bill_amount;
		}
		$(document).on('submit', '#quotation_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"quotation_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#quotation_form')[0].reset();
					$('#quotationModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					orderdataTable.ajax.reload();
				}
			});
		});
		
		$(document).on('click', '.update', function(){
			var inventory_order_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"quotation_action.php",
				method:"POST",
				data:{inventory_order_id:inventory_order_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
				//	console.log("entered");
					$('#quotationModal').modal('show');
					$('#customer_id').val(data.customer_id);
					$('#customer_name').val(data.customer_name);
					$('#inventory_order_date').val(data.inventory_order_date);
					$('#span_product_details').html(data.product_details);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Quotation");
					$('#inventory_order_id').val(inventory_order_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				 }
			});
		});

		$(document).on('click', '.delete', function(){
			var inventory_order_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"quotation_action.php",
					method:"POST",
					data:{inventory_order_id:inventory_order_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						orderdataTable.ajax.reload();
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