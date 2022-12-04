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
                            <h3 class="panel-title">Sales List</h3>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Add</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="order_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Sales ID</th>
								<th>Invoice No</th>
								<th>Customer Name</th>
								<th>Total Amount</th>
								<th>Bill Type</th>
								<th>Payment Status</th>
								<th>Sales Status</th>
								<th>Sold Date</th>
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

    <div id="orderModal" class="modal fade">
-
    	<div class="modal-dialog">
    		<form method="post" id="order_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create Sales Order</h4>
    				</div>
    				<div class="modal-body">
    					<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Select Customer Name</label>
									<select name="customer_id" id="customer_id" class="form-control" required>
										<?php echo fill_customer_list($connect);?>										
									</select>
									<input type="text" name="customer_name" id="customer_name" class="form-control" style="visibility:hidden;"/>
								<!--	<button name="AddCustomer" id="AddCustomer"  class="form-control" style="visibility:hidden;">AddCustomer</button>	-->
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Date</label>
									<input type="text" name="inventory_order_date" id="inventory_order_date" class="form-control" required />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Select Bill Type</label>
									<select name="bill_type" id="bill_type" class="form-control" required>
										<option value="withGST">With GST</option>
										<option value="withoutGST">Without GST</option>
									</select>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Dispatch Through</label>
									<input type="text" name="dispatch_through" id="dispatch_through" class="form-control" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Dispatch Document No</label>
									<input type="text" name="dispatch_no" id="dispatch_no" class="form-control" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Order No:</label>
									<input type="text" name="order_no" rows="5" id="order_no" class="form-control" ></input>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Order Date:</label>
									<input type="date" name="order_date" id="order_date" class="form-control" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Truck No:</label>
									<input type="text" name="truck_no" rows="5" id="truck_no" class="form-control" ></input>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Broker:</label>
									<input type="text" name="broker" id="broker" class="form-control" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Delivery Address</label>
									<textarea name="delivery_address" rows="5" id="delivery_address" class="form-control" ></textarea>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Delivery Station</label>
									<input type="text" name="delivery_station" id="delivery_station" class="form-control" />
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
							<div class="col-md-12">
								<label>Enter % of Discount
								<input type="number" value="0" name="pdiscount" id="pdiscount"></input>						</label>
							</div>
							<hr/>
						</div>
						<div class="row">
							<div class="col-md-2">
							
								<button type="button" name="recalculate" id="recalculate" class="btn btn-warning btn-xxs recalculate">Calculate</button>
								<input type="hidden" name="cbill_amount" id="cbill_amount"></input>						
							</div>
							<div class="col-md-10">
								<label><span id="sale_value"></span></label>
							</div>
							
						</div>
						
						<div class="form-group">
							<label>Select Payment Status</label>
							<select name="payment_status" id="payment_status" class="form-control" required>
								<option value="">Select Payment Status</option>
								<option value="cash">Cash/Cheque</option>
								<option value="credit">Credit</option>
							</select>
							<span id="span_payment_details"></span>
							<hr />
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

    	var orderdataTable = $('#order_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"searching":true,
			"ajax":{
				url:"sales_fetch.php",
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
			"pageLength": 10,
				"lengthMenu": [
				[10, 30, 50, -1],
				[10, 30, 50, "All"]
			  ]
		});
		$('#customer_id').change(function(){
			var customer_id = $('#customer_id').val();
			//alert(customer_id);
			if(customer_id=="other")
			{
				//$('#customer_name').show();
				document.getElementById('customer_name').style.visibility="visible";
				document.getElementById('AddCustomer').style.visibility="visible";
			}
			else
			{
				//$('#customer_name').hide();
				document.getElementById('customer_name').style.visibility="hidden";
				document.getElementById('AddCustomer').style.visibility="hidden";
			}
		});
		$('#AddCustomer').click(function(){
			var customer_name = $('#customer_name').val();
			var customer_id = $('#customer_id').val();
			var btn_action = "AddCustomer";
			if(customer_id=="other" && customer_name!='')
			{
				if(confirm("Are you sure you want to Add Customer?"))
				{
					$.ajax({
						url:"sales_action.php",
						method:"POST",
						data:{customer_id:customer_id,customer_name:customer_name, btn_action:btn_action},
						success:function(data)
						{
							alert("Customer Has been Added");							
						}
					});
				}
				else
				{
					return false;
				}
			}
			else
			{
				alert("Please Enter Customer Name");
			}
			
		});
		$('#add_button').click(function(){
			$('#orderModal').modal('show');
			$('#order_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Order");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
			$('#span_payment_details').html('');
			add_payment_tags();
		});
		
	/*	$('#payment_status').change(function(){
			$('#orderModal').modal('show');
			$('#order_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Order");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_payment_details').html('');
			add_payment_tags();
		});	*/
		
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

			//alert(selecteditems);
            $(document.getElementById("product_id"+count).options).each(function(index, option) {
                for (var i = 0; i <selecteditems.length; i++) {
                    var itm=selecteditems[i];
                    if( option.value == itm.value() ) {
                      option.hidden = true; // not fully compatible. option.style.display = 'none'; would be an alternative or $(option).hide();
						$(option).hide();
						//alert(option.value);
                    }
                }

            });
           // Hint code end
       }
		
		$(document).on('change', '#payment_status', function(){
			add_payment_tags();
		});
		function add_payment_tags()
		{
			var html = '';
			var payment_status = document.getElementById('payment_status').value;
			var bill_amount=0;
			//alert(payment_status);
			if(payment_status=="cash")
			{
			//	find_bill_amount();
				html += '<span id="span_payment_details1"><div class="payment">';
				html += '<div class="col-md-6">';
				html += 'Total Bill Amount';
				html += '<input type="text" value="0" name="bill_amount" id="bill_amount" disabled class="form-control" required>';
				html += '</div>';
				html += '<div class="col-md-6">';
				html += 'Mode of Payment';
				html += '<select name="mode_of_payment"  id="mode_of_payment" class="form-control" required>';
				html += '<option value="">Select</option>';
				html += '<option value="cash">Cash</option>';
				html += '<option value="cheque">Cheque</option></select>';
				html += '</div><br />';
				html += '<span id="span_cheque_details"></span>';
				html += '<div class="col-md-6">';
				html += 'Enter Amount Paid/Cheque Amount';
				html += '<input type="number" name="amount_paid" id="amount_paid" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-6">';
				html += 'Balance';
				html += '<input type="number" name="balance" disabled id="balance" class="form-control" required/>';
				html += '</div>';
				html += '<div class="col-md-12">';
				html += 'Date of Payment';
				html += '<input type="text" name="date_of_payment"  id="date_of_payment" class="form-control" required />';
				html += '</div>';
				html += '</div></span>';
				$('#span_payment_details').append(html);
				
			}
			else
			{
				$('#span_payment_details1').remove();
			}	
		}
		$(document).on('blur', '#amount_paid', function(){
			var bill_amount = $('#bill_amount').val();
			var amount_paid = $('#amount_paid').val();
			if(parseInt(amount_paid)>parseInt(bill_amount))
			{
				alert("Amount Paid is More than Bill Amount!!!");
			//	$('#amount_paid').backgroundcolor="red";
				$('#amount_paid').val(0);
				$('#amount_paid').select();
			}	
			var balance = bill_amount - amount_paid;
			$('#balance').val(balance);
		//	alert(bill_amount);
		});	
		$(document).on('click', '#recalculate', function(){
			$('#sale_value1').remove(html);
			bill_amount = find_bill_amount();
			var pdiscount = $('#pdiscount').val();
			var discount_amount = ((bill_amount*pdiscount)/100).toFixed(2);
			var grand_total = bill_amount - discount_amount;
			$('#bill_amount').val(grand_total);
			var html = '';
			
			//(Inclusive of Tax)
			html += '<span id="sale_value1"><div class="row">';
			html += '<div class="col-md-12">';
			html += 'Total Bill Amount:';
			html += '<label>'+bill_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Discount Amount:';
			html += '<label>'+discount_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Grand Total Bill Amount(Inclusive of Tax):';
			html += '<label>'+grand_total+'</label>';
			html += '</div></div></span>';
			$('#sale_value').append(html);
			});	
		var SGST=0,CGST=0;
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
			var pdiscount = $('#pdiscount').val();
			//alert(pdiscount);
			for(i=0;i<p_values.length;i++)
			{
				product_id = p_values[i];
				quantity = q_values[i];
				uc_value = u_values[i];
				pu_value = p_u_values[i];
				//alert(pu_value);
				var btn_action = 'fetch_SGST_CGST';
				var tax_data = function(){
					var temp=null;
					$.ajax({
						url:"sales_action.php",
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
					if(tax_data.SGST)
						SGST = tax_data.SGST;
					else
						SGST = 0;
					if(tax_data.CGST)
						CGST = tax_data.CGST;
					else
						CGST = 0;
				//alert(SGST);
				//weight = tax_data.weight;
				if(pu_value=="Box")
					weight = 18;
				else
					weight = 1;
				bill_amount += parseFloat((weight*quantity*uc_value).toFixed(2));
				SGST_tax += parseFloat(((weight*quantity*uc_value*SGST)/100).toFixed(2));
				CGST_tax += parseFloat(((weight*quantity*uc_value*CGST)/100).toFixed(2));	
		
			}	
			discount = ((bill_amount*pdiscount)/100).toFixed(2);
			$('#bill_amount').val(bill_amount-discount);
			$('#SGST').val(SGST_tax);
			$('#CGST').val(CGST_tax);
			$('#total_amount').val(parseFloat(bill_amount+SGST_tax+CGST_tax-discount).toFixed(2));	
		//	$('#total_amount').val(parseFloat(bill_amount-discount).toFixed(2));	
			grand_total = parseFloat(bill_amount+SGST_tax+CGST_tax).toFixed(2);
			$('#cbill_amount').val(parseFloat(bill_amount+SGST_tax+CGST_tax-discount).toFixed(2));
			return grand_total;
		}	
		$(document).on('blur', '#pdiscount', function(){
			bill_amount  = find_bill_amount();
			var amount_paid = $('#amount_paid').val();
			var pdiscount = $('#pdiscount').val();
			var discount_amount = ((bill_amount*pdiscount)/100).toFixed(2);
			var grand_total = bill_amount - discount_amount;
			$('#bill_amount').val(grand_total);
			$('#balance').val(grand_total-amount_paid);
			$('#sale_value1').remove(html);
			var html = '';
			
			//(Inclusive of Tax)
			html += '<span id="sale_value1"><div class="row">';
			html += '<div class="col-md-12">';
			html += 'Total Bill Amount:';
			html += '<label>'+bill_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Discount Amount:';
			html += '<label>'+discount_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Grand Total Bill Amount(Inclusive of Tax):';
			html += '<label>'+grand_total+'</label>';
			html += '</div></div></span>';
			$('#sale_value').append(html);
		//	alert(bill_amount);
			});
		$(document).on('change', '#payment_status', function(){
			bill_amount  = find_bill_amount();
			var amount_paid = $('#amount_paid').val();
			var pdiscount = $('#pdiscount').val();
			var discount_amount = ((bill_amount*pdiscount)/100).toFixed(2);
			var grand_total = bill_amount - discount_amount;
			$('#bill_amount').val(grand_total);
			$('#balance').val(grand_total-amount_paid);
		//	alert(bill_amount);
			});	
		$(document).on('change', '#mode_of_payment', function(){
			var payment = $('#mode_of_payment').val();
			var html='';
		//	alert(payment);
			if(payment == "cheque")
			{
			//	alert("adding cheque details");

				html += '<span id="span_cheque_details1"><div class="cheque">';
				html += '<div class="col-md-4">';
				html += 'Cheque Number';
				html += '<input name="cheque_number"  id="cheque_number" class="form-control" required></input>';
				html += '</div><br />';
				html += '<div class="col-md-4">';
				html += 'Cheque Date';
				html += '<input type="text" name="cheque_date" id="cheque_date" class="form-control" required />';
				html += '</div>';
				html += '<div class="col-md-4">';
				html += 'Cheque Bank Name';
				html += '<input type="text" name="cheque_bank_name"  id="cheque_bank_name" class="form-control" required />';
				html += '</div>';
				html += '</div></span>';
				$('#span_cheque_details').append(html);
			}	
			else
				$('#span_cheque_details1').remove(html);
				//$('#span_cheque_details').remove(html);
			});	
			
		var count = 0;
		var deleted_row_id=[];
		var c=0;
		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			$('#sale_value1').remove(html);
			var row_no = $(this).attr("id");
			deleted_row_id[c]=row_no;
			c=c+1;
	//		count = count - 1 ;
			$('#row'+row_no).remove();
			bill_amount = find_bill_amount();
			var pdiscount = $('#pdiscount').val();
			var discount_amount = ((bill_amount*pdiscount)/100).toFixed(2);
			var grand_total = bill_amount - discount_amount;
			$('#bill_amount').val(grand_total);
			var html = '';
			
			//(Inclusive of Tax)
			html += '<span id="sale_value1"><div class="row">';
			html += '<div class="col-md-12">';
			html += 'Total Bill Amount:';
			html += '<label>'+bill_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Discount Amount:';
			html += '<label>'+discount_amount+'</label>';
			html += '</div>';
			html += '<div class="col-md-12">';
			html += 'Grand Total Bill Amount((Inclusive of Tax):';
			html += '<label>'+grand_total+'</label>';
			html += '</div></div></span>';
			$('#sale_value').append(html);
			
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
			//alert(bill_amount);
			return bill_amount;
		}
		$(document).on('submit', '#order_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"sales_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#order_form')[0].reset();
					$('#orderModal').modal('hide');
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
				url:"sales_action.php",
				method:"POST",
				data:{inventory_order_id:inventory_order_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
				//	console.log("entered");
				//	console.log("entered");
					$('#orderModal').modal('show');
					$('#customer_id').val(data.customer_id);
					$('#inventory_order_date').val(data.inventory_order_date);
					$('#bill_type').val(data.bill_type);
					$('#span_product_details').html(data.product_details);
					$('#payment_status').val(data.payment_status);
					$('#dispatch_through').val(data.dispatch_through);
					$('#dispatch_no').val(data.dispatch_no);
					$('#delivery_address').val(data.delivery_address);
					$('#delivery_station').val(data.delivery_station);
					$('#order_no').val(data.order_no);
					$('#order_date').val(data.order_date);
					$('#truck_no').val(data.truck_no);
					$('#broker').val(data.broker);
					$('#span_payment_details').html(data.payment_details);
					$('#pdiscount').val(data.pdiscount);
					if($('#mode_of_payment').val()=="cheque")
						$('#span_cheque_details').html(data.span_cheque_details);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Order");
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
					url:"order_action.php",
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