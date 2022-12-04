<?php

//sales_action.php

include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'AddCustomer')
	{
		if(isset($_POST['customer_name']) && $_POST['customer_id']=="other")
		{
			$customer_name = $_POST['customer_name'];
			$query = "
					INSERT INTO customer_details (customer_name, entered_by,customer_status) 
					VALUES (:customer_name,:user_id, :customer_status)
					";
			$statement = $connect->prepare($query);
			$connect->beginTransaction();
			$statement->execute(
				array(
					':customer_name'					=>	$customer_name,
					':user_id'						=>	$_SESSION["user_id"],
					':customer_status'		=>	'active'
				)
			) or die("Error in Customer Query" . print_r($statement->errorInfo()));
			$result = $statement->fetchAll();
			$statement = $connect->query("SELECT LAST_INSERT_ID()");
			$customer_id = $statement->fetchColumn();
			if($customer_id)
			{
				echo "Customer has been Added";
			}
		}
	}
	if($_POST['btn_action'] == 'Add')
	{
		if(isset($_POST['customer_name']) && $_POST['customer_id']=="other")
		{
			$customer_name = $_POST['customer_name'];
			$query = "
					INSERT INTO customer_details (customer_name, entered_by,customer_status) 
					VALUES (:customer_name,:user_id, :customer_status)
					";
			$statement = $connect->prepare($query);
			$connect->beginTransaction();
			$statement->execute(
				array(
					':customer_name'					=>	$customer_name,
					':user_id'						=>	$_SESSION["user_id"],
					':customer_status'		=>	'active'
				)
			) or die("Error in Customer Query" . print_r($statement->errorInfo()));
			$result = $statement->fetchAll();
			$statement = $connect->query("SELECT LAST_INSERT_ID()");
			$customer_id = $statement->fetchColumn();
			if($customer_id)
			{
				//echo "Customer has been Added";
				$connect->commit();
			}
		}
		else
			$customer_id = $_POST['customer_id'];
		//echo $customer_id . "<br/>";	
		$sql = "SELECT invoice_no
				FROM inventory_order
				Where bill_type!='BroughtForward'
				order by invoice_no DESC
				LIMIT 1
				";
		
		$statementi = $connect->prepare($sql);
		$statementi->execute() or die("Error in Invoice Query" . print_r($statement->errorInfo()));
		$resulti = $statementi->fetch(PDO::FETCH_ASSOC);
		//echo $resulti['invoice_no'];
		$invoice_no = $resulti['invoice_no'] + 1;
		
		$query = "
		INSERT INTO inventory_order (invoice_no,user_id, inventory_order_total, pdiscount, inventory_order_date, customer_id, 
									bill_type, payment_status, inventory_order_status, inventory_order_created_date,dispatch_through,
									delivery_address,delivery_station,order_no,order_date,truck_no,broker,dispatch_no) 
		VALUES (:invoice_no,:user_id, :inventory_order_total, :pdiscount, :inventory_order_date, :customer_id, :bill_type, 
				:payment_status, :inventory_order_status, :inventory_order_created_date,:dispatch_through,:delivery_address,
				:delivery_station,:order_no,:order_date,:truck_no,:broker,:dispatch_no)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':invoice_no'					=>	$invoice_no,
				':user_id'						=>	$_SESSION["user_id"],
				':inventory_order_total'		=>	0,
				':pdiscount'					=>	$_POST['pdiscount'],
				':inventory_order_date'			=>	$_POST['inventory_order_date'],
				':customer_id'					=>	$customer_id,
				':bill_type'					=>	$_POST['bill_type'],
				':payment_status'				=>	$_POST['payment_status'],
				':inventory_order_status'		=>	'active',
				':inventory_order_created_date'	=>	date("Y-m-d"),
				':dispatch_through' => $_POST['dispatch_through'],
				':delivery_address' => $_POST['delivery_address'],
				':delivery_station' => $_POST['delivery_station'],
				':order_no' => $_POST['order_no'],
				':order_date' => $_POST['order_date'],
				':truck_no' => $_POST['truck_no'],
				':broker' => $_POST['broker'],
				':dispatch_no' => $_POST['dispatch_no']
			)
		)or die("Error in Inventory Order Query" . print_r($statement->errorInfo()));
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$inventory_order_id = $statement->fetchColumn();

		if(isset($inventory_order_id))
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["product_id"]); $count++)
			{
				$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
				$sub_query = "
				INSERT INTO inventory_order_product (inventory_order_id, product_id, quantity, price, tax,sale_uom) 
				VALUES (:inventory_order_id, :product_id, :quantity, :price, :tax, :sale_uom)
				";
				$statement = $connect->prepare($sub_query);
				
				$GST = $product_details['SGST'] + $product_details['CGST']; 
				//$size = $product_details['size'];
				//$base_price = ($_POST["unit_cost"][$count]/(100+$GST))*100;
				$base_price = $_POST["unit_cost"][$count];
				$quantity =	$_POST["quantity"][$count];
				$tax = ($base_price * $GST)/100;
				
				$statement->execute(
					array(
						':inventory_order_id'	=>	$inventory_order_id,
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':price'				=>	$base_price,
						':tax'					=>	$tax,
						':sale_uom'				=>	$_POST["product_unit"][$count]
					)
				)or die("Error in Inventory Order Product Query" . print_r($statement->errorInfo()));
				$uom = $_POST['product_unit'][$count];
				if($uom=="Box")
					$size = 18;
				else
					$size = 1;
				$total_amount = ($total_amount + (($base_price + $tax) * ($size* $quantity)));
				$product_id = $_POST["product_id"][$count];
				
				//$category_name = strtolower($product_details['category_name']);
				//if(($uom == 'Dozens')
					//$total_items_in_box =round(($quantity/$product_details['unit_conversion']),4);
				//else
				$total_items_in_box = $size * $quantity;
				
				
				$stock_details_query = "SELECT * from stock_details where product_id = '".$product_id."'";
				$stock_details = $connect->query($stock_details_query);
				$sdresult = $stock_details->fetch(PDO::FETCH_ASSOC);
	
				$rows = $stock_details->rowCount();
				$TPQ = $sdresult['total_purchase_quantity'];
				$TSQ = $sdresult['total_sales_quantity'];
				$SA = $sdresult['stock_available'];
				if($rows>0)
				{
				//	$TSQ = $TSQ + $_POST["quantity"][$count];
					$TSQ = $TSQ + $total_items_in_box;
				//	$SA = $SA - $_POST["quantity"][$count];
					$SA = $SA - $total_items_in_box;
					$update_sdquery = "	
									UPDATE stock_details 
									SET total_purchase_quantity = '".$TPQ."',
										total_sales_quantity = '".$TSQ."',
										stock_available = '".$SA."'	
									WHERE product_id = '".$product_id."'
									";
					$statement = $connect->prepare($update_sdquery);
					$statement->execute()or die("Error in Stock Update Query" . print_r($statement->errorInfo()));
				}
				else
				{
					$insert_sdquery = "
									INSERT into stock_details(product_id, total_purchase_quantity, total_sales_quantity, stock_available)
									VALUES(:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available) 
									";
					$sdinsert = $connect->prepare($insert_sdquery);
					$sdinsert->execute(
						array(
							':product_id'				=>	$_POST["product_id"][$count],
							':total_purchase_quantity'	=>	0,
							':total_sales_quantity'		=>	$total_items_in_box,
							':stock_available'			=>	-$total_items_in_box
						)
					) or die("Error in Stock Insert Query" . print_r($sdinsert->errorInfo()));
				}
				$cust_details_query = "SELECT * from customer_details where customer_id = '".$customer_id."'";
				$cust_details = $connect->query($cust_details_query);
				$cdresult = $cust_details->fetch(PDO::FETCH_ASSOC);
				$customer_type = $cdresult['customer_type'];
				if($customer_type=="Outlet")
				{
					$query = "
					INSERT INTO ostock_entry_details (entered_by) 
										  VALUES (:entered_by)
					";
					$flag=0;
					
					$statement = $connect->prepare($query);
					$statement->execute(
						array(
							':entered_by'					=>	$_SESSION["user_id"]
						)
					)or die("Error in oStock Entry Query" . print_r($statement->errorInfo()));
					
					$result = $statement->fetchAll();
					$statement = $connect->query("SELECT LAST_INSERT_ID()");
					$stock_entry_id = $statement->fetchColumn();

					if(isset($stock_entry_id))
					{
						for($count = 0; $count<count($_POST["product_id"]); $count++)
						{
							$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
							$product_name = $product_details['product_name'];
							$product_unit = $product_details['product_unit'];
							$size = $product_details['size'];
							$product_id = $_POST["product_id"][$count];
							$quantity = $_POST["quantity"][$count];
							$unit_of_measurement = $_POST["product_unit"][$count];
							$sub_query = "
							INSERT INTO ostock_entry_history (stock_entry_id,product_id,quantity,unit_of_measurement)
													VALUES (:stock_entry_id,:product_id,:quantity,:unit_of_measurement)
							";					
							$statement = $connect->prepare($sub_query);
							$statement->execute(
								array(
									':stock_entry_id'		=>	$stock_entry_id,
									':product_id'			=>	$product_id,
									':quantity'				=>	$quantity,
									':unit_of_measurement'	=>	$unit_of_measurement
								)
							) or die("Error in oStock_entry_history query" . print_r($statement->errorInfo()));
							
							$stock_details_query = "SELECT total_purchase_quantity, stock_available from ostock_details where product_id = '".$product_id."'";
							$stock_details = $connect->query($stock_details_query);
							$sdresult = $stock_details->fetch(PDO::FETCH_ASSOC);
							$rows = $stock_details->rowCount();
							$total_purchase_quantity = $sdresult['total_purchase_quantity'];
							$stock_available = $sdresult['stock_available'];
							if($product_unit != $unit_of_measurement && $unit_of_measurement=="Pieces" && ($product_name=="jaggery" or $product_name=="Jaggery"))
							{
									$pieces_into_box = round($quantity/$size,2);
							}
							else
									$pieces_into_box = $quantity;
							if($rows>0)
							{
							//	$total_purchase_quantity = $total_purchase_quantity + $quantity;
							//	$stock_available = $stock_available + $quantity; 
									
								/* convert into pieces into box for jaggery or Jaggery and store in stock */
								
								
								$total_purchase_quantity = $total_purchase_quantity + $pieces_into_box;
						//		echo " = new stock :" . $total_purchase_quantity . "<br/>";
								$stock_available = $stock_available + $pieces_into_box;
								$update_sdquery = "
												UPDATE ostock_details 
												SET total_purchase_quantity = '".$total_purchase_quantity."',
													stock_available = '".$stock_available."'	
												WHERE product_id = '".$product_id."'
												";
								$statement = $connect->prepare($update_sdquery);
								$statement->execute();
								$flag=1;
							}
							else
							{
								$insert_sdquery = "
												INSERT into ostock_details(product_id, total_purchase_quantity, total_sales_quantity, stock_available)
												VALUES(:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available) 
												";
								$sdinsert = $connect->prepare($insert_sdquery);
								$sdinsert->execute(
									array(
										':product_id'				=>	$_POST["product_id"][$count],
										':total_purchase_quantity'	=>	$pieces_into_box, //$quantity,
										':total_sales_quantity'		=>	0,
										':stock_available'			=>	$pieces_into_box //$quantity
									)
								);
								//$flag=1;
							}
						}
					}
					
				}
				
			}
			$pdiscount = $_POST['pdiscount'];
			$total_amount -= round(($total_amount*$pdiscount)/100,0);
			$update_query = "
			UPDATE inventory_order 
			SET inventory_order_total = '".$total_amount."' 
			WHERE inventory_order_id = '".$inventory_order_id."'
			";
			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				$payment_status = $_POST["payment_status"];
		//		echo $payment_status;
				//$customer_id = $_POST['customer_id'];
				$total_amount_to_be_paid = $total_amount;
				$entered_by = $_SESSION['user_id'];
				$date_of_entry = date('Y-m-d');
				
				if($payment_status == "credit")
				{
					$amount_paid = 0;
					$balance = $total_amount_to_be_paid - $amount_paid;
					$mode_of_payment="credit";
					$cheque_number = 0;
					$cheque_date = 0000-00-00;
					$cheque_bank_name = 0;
					$date_of_payment =0000-00-00;
				}
				else
				{
					$mode_of_payment = $_POST['mode_of_payment'];
					$amount_paid = $_POST['amount_paid'];
					$balance = $total_amount_to_be_paid - $amount_paid;
					if($mode_of_payment == "cheque")
					{
						$cheque_number = $_POST['cheque_number'];
						$cheque_date = $_POST['cheque_date'];
						$cheque_bank_name = $_POST['cheque_bank_name'];
					}
					else
					{
						$cheque_number = 0;
						$cheque_date = 0000-00-00;
						$cheque_bank_name = 0;
					}
					$date_of_payment = $_POST['date_of_payment'];
				}
				
				$query_customer_id = "SELECT * from customer_payment where customer_id = '".$customer_id."'";
				$statement = $connect->prepare($query_customer_id);
				$statement->execute();
				$result = $statement->fetch(PDO::FETCH_ASSOC);
				$row = $statement->rowCount();
				$payment_id = $result['payment_id'];
				$old_total_amount_to_be_paid = $result['total_amount_to_be_paid'];
				$old_amount_paid = $result['amount_paid'];
				$old_balance = $result['balance'];
				if($row>0)
				{
					echo $payment_id . " " . $old_total_amount_to_be_paid . " " . $old_amount_paid . " " . $balance;
					$customer_payment_update_query = "
						UPDATE customer_payment 
						SET
							payment_id = :payment_id,
							customer_id = :customer_id,
							total_amount_to_be_paid = :total_amount_to_be_paid,
							amount_paid = :amount_paid, 
							balance = :balance,
							entered_by = :entered_by,
							date_of_entry = :date_of_entry
							where payment_id = '".$payment_id."'
					";
					$statement1 = $connect->prepare($customer_payment_update_query);
					$statement1->execute(
						array(
							':payment_id'					=>	$payment_id,
							':customer_id'					=>	$customer_id,
							':total_amount_to_be_paid'		=>	($total_amount_to_be_paid + $old_total_amount_to_be_paid),
							':amount_paid'					=>	($amount_paid + $old_amount_paid),
							':balance'						=>	($balance + $old_balance),
							':entered_by'					=>	$_SESSION["user_id"],
							':date_of_entry'				=>	$date_of_entry,
							
							)
					) or die("Error in Payment Query" . print_r($statement1->errorInfo()));
					$result = $statement1->fetchAll();
				}
				else
				{
					$customer_payment_query = "
					INSERT INTO customer_payment (customer_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
					VALUES (:customer_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
					";
					$statement1 = $connect->prepare($customer_payment_query);
					$statement1->execute(
						array(
							':customer_id'					=>	$customer_id,
							':total_amount_to_be_paid'		=>	$total_amount_to_be_paid,
							':amount_paid'					=>	$amount_paid,
							':balance'						=>	$balance,
							':entered_by'					=>	$_SESSION["user_id"],
							':date_of_entry'				=>	$date_of_entry
							)
					) or die("Error in Payment Insert Query" . print_r($statement1->errorInfo()));
					$result = $statement1->fetchAll();
					$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
					$payment_id = $statement2->fetchColumn();
				}
				
				if($payment_id)
				{
					$customer_payment_query1 = "
					INSERT INTO customer_payment_details (payment_id, customer_id, inventory_order_id, bill_amount, amount_paid, balance,
					mode_of_payment, cheque_number, cheque_date, cheque_bank_name, date_of_payment) 
					VALUES (:payment_id, :customer_id, :inventory_order_id, :bill_amount, :amount_paid, :balance,
					:mode_of_payment, :cheque_number, :cheque_date, :cheque_bank_name, :date_of_payment)
					";
					$statement1 = $connect->prepare($customer_payment_query1);
					$statement1->execute(
						array(
							':payment_id'					=>	$payment_id,
							':customer_id'					=>	$customer_id,
							':inventory_order_id'			=>	$inventory_order_id,
							':bill_amount'					=>	$total_amount_to_be_paid,
							':amount_paid'					=>	$amount_paid,
							':balance'						=>	$balance,
							':mode_of_payment'				=>	$mode_of_payment,
							':cheque_number'				=>	$cheque_number,
							':cheque_date'					=>	$cheque_date,
							':cheque_bank_name'				=>	$cheque_bank_name,
							':date_of_payment'				=>	$date_of_payment
							)
					) or die("Error in Payment Details Query" . print_r($statement1->errorInfo()));
					$result = $statement1->fetchAll();
					if(isset($result))
					{
						echo 'Sales  Order Created, Customer payment details and Stock Updated...';
						echo '<br />';
					//	echo $total_amount;
						echo '<br />';
					//	echo $inventory_order_id;
					}
				}
				
			}
		}
	}
	
	if($_POST['btn_action'] == 'fetch_SGST_CGST')
	{
		$product_details = fetch_product_details($_POST["product_id"], $connect);
		$output['SGST']=$product_details['SGST'];
		$output['CGST']=$product_details['CGST'];
	//	$output['weight']=$product_details['size'];
		$output['product_unit'] = $product_details["product_unit"];
		echo json_encode($output);
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM inventory_order
		
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_id'	=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['customer_id'] = $row['customer_id'];
			$output['pdiscount'] = $row['pdiscount'];
			$output['inventory_order_date'] = $row['inventory_order_date'];
			$output['bill_type'] = $row['bill_type'];
			$output['payment_status'] = $row['payment_status'];
			$output['dispatch_through'] = $row['dispatch_through'];
			$output['dispatch_no'] = $row['dispatch_no'];
			$output['delivery_address'] = $row['delivery_address'];
			$output['delivery_station'] = $row['delivery_station'];
			$output['order_no'] = $row['order_no'];
			$output['order_date'] = $row['order_date'];
			$output['truck_no'] = $row['truck_no'];
			$output['broker'] = $row['broker'];
			
		}
		$sub_query = "
		SELECT * FROM inventory_order_product 
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
		$statement = $connect->prepare($sub_query);
		$statement->execute();
		$sub_result = $statement->fetchAll();
		$product_details = '';
		$count = 0;
		$payment_details = '';
		$cheque_details = '';
		foreach($sub_result as $sub_row)
		{
			if($count==0)
				$count='';
			$sale_uom = $sub_row['sale_uom'];
			//$unit_cost = round(($sub_row["price"] + $sub_row["tax"]),0);
			$unit_cost = round($sub_row["price"],0);
			$product_details .= '
			<script>
			$(document).ready(function(){
				$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-3">
						<select name="product_id[]" id="product_id'.$count.'" class="form-control selectpicker product_id" data-live-search="true" required>
							'.fill_product_list($connect).'
						</select>
						<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" class="form-control hidden_product_id" value="'.$sub_row["product_id"].'" />
					</div>
					<div class="col-md-2">
						<input type="text" name="quantity[]" class="form-control quantity" value="'.$sub_row["quantity"].'" required />
					</div>
					<div class="col-md-3">
						<select name="product_unit[]" id="product_unit'.$count.'" class="form-control selectpicker product_unit" data-live-search="true" required>
						';
							
							$units = Array("Bags","Bottles","Box","Dozens", "Feet", "Gallon", "Grams", "Inch", "Kg", "Liters", "Meter", "Nos", "Packet", "Pieces", "Rolls");
							foreach($units as $uom)
							{
								$product_details .= '<option value="'.$uom.'"';
								if($sale_uom == $uom) 
									$product_details .= ' selected>';
								else 
									$product_details .= '>';
								$product_details .= $uom .'</option>';
							}
						
						$product_details .= '</select><input type="hidden" name="hidden_product_unit[]" id="hidden_product_unit'.$count.'" />
					</div>
		 			<div class="col-md-2">
						<input type="text" name="unit_cost[]" class="form-control unit_cost" value="'.$unit_cost.'" required />
					</div>
					<div class="col-md-1">
			';

		//	if($count == '')
		//	{
				$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
				$product_details .= '
						</div>
						<div class="col-md-1">';
		//	}
		//	else
		//	{
				$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
		//	}
			$product_details .= '
						</div>
					</div>
				</div><br />
			</span>
			';
			$count = $count + 1;
		}
	
		
		$sub_query = "
		SELECT * FROM customer_payment_details 
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
		$statement = $connect->prepare($sub_query);
		$statement->execute();
		$sub_result = $statement->fetch();
		
		$output['product_details'] = $product_details;	
		
		$payment_status = $row['payment_status'];
		if($payment_status == "cash" or $payment_status == "cheque")
		{
			$payment_details .= '<span id="span_payment_details1"><div class="payment">
								<div class="col-md-6">
								Total Bill Amount
								<input type="text" value="'.$sub_result['bill_amount'].'" name="bill_amount" id="bill_amount" disabled class="form-control" required>
								</div>';
								if($sub_result['mode_of_payment']=="cash")
								{
									$payment_details	.= '<div class="col-md-6">
									Mode of Payment
									<select name="mode_of_payment"  id="mode_of_payment" class="form-control" required>
									<option value="">Select</option>
									<option value="cash" selected>Cash</option>
									<option value="cheque">Cheque</option></select></div>';
								}
								else if($sub_result['mode_of_payment']=="cheque")
								{
									$payment_details	.= '<div class="col-md-6">
									Mode of Payment
									<select name="mode_of_payment"  id="mode_of_payment" class="form-control" required>
									<option value="">Select</option>
									<option value="cash">Cash</option>
									<option value="cheque" selected>Cheque</option></select>
									</div>
									';
								}
			$payment_details	.= '<span id="span_cheque_details"></span>
								<div class="col-md-6">
								Enter Amount Paid/Cheque Amount
								<input type="text" name="amount_paid" id="amount_paid" class="form-control" required value="'.$sub_result["amount_paid"].'"/>
								</div>
								<div class="col-md-6">
								Balance
								<input type="text" name="balance" disabled id="balance" class="form-control" required value="'.$sub_result["balance"].'"/>
								</div>
								<div class="col-md-12">
								Date of Payment
								<input type="text" name="date_of_payment"  id="date_of_payment" class="form-control" required value="'.$sub_result["date_of_payment"].'"/>
								</div>
								</div></span>';
			$span_cheque_details=		'<span id="span_cheque_details1"><div class="cheque">
										<div class="col-md-4">
										Cheque Number
										<input name="cheque_number"  id="cheque_number" class="form-control" required value="'.$sub_result["cheque_number"].'"></input>
										</div><br />
										<div class="col-md-4">
										Cheque Date
										<input type="text" name="cheque_date" id="cheque_date" class="form-control" required value="'.$sub_result["cheque_date"].'"/>
										</div>
										<div class="col-md-4">
										Cheque Bank Name
										<input type="text" name="cheque_bank_name"  id="cheque_bank_name" class="form-control" required value="'.$sub_result["cheque_bank_name"].'"/>
										</div>
									</div></span>';
		}
		$output['payment_details'] =  $payment_details;
		if($sub_result['mode_of_payment']=="cheque")
			$output['span_cheque_details'] =  $span_cheque_details;
		else
			$output['span_cheque_details'] = '';
	//	$output ='data';
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		//echo $_POST["inventory_order_id"] . " ";
		if(isset($_POST["product_id"]))
			$count = count($_POST["product_id"]);
		else
			$count=0;
		if($count>0)
		{
			$flag=0;
			echo "Inventory Order ID: " . $_POST["inventory_order_id"] . " " . "<br />";
			$stock_details_query = "
			SELECT *
			FROM inventory_order_product
			INNER JOIN product ON product.product_id = inventory_order_product.product_id
			WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
			order by inventory_order_product.product_id ASC
			";
			$stock_details = $connect->query($stock_details_query);
			$sdresult = $stock_details->fetchALL(PDO::FETCH_ASSOC);
			$rows = $stock_details->rowCount();
			if($rows>0)
			{
				foreach($sdresult as $row)
				{
					$product_details = fetch_product_details($row["product_id"], $connect);
					//$unit_conversion = $row["unit_conversion"];
					$sale_uom=$row['sale_uom'];
					//$category_name = strtolower($product_details['category_name']);
					//if($category_name=='chips' && $sales_uom == 'Dozens')
					//	$total_pieces = round(($row["quantity"]/$unit_conversion),2);
					//else
					if($sale_uom=="Box")
						$size = 18;
					else
						$size = 1;
					$total_pieces = $size * $row["quantity"];
			//		echo "product id : " . $row["product_id"] . " , Pieces :" . $total_pieces . "<br/>";
						$stock_update_query1 = "
						UPDATE stock_details
						SET 
							total_sales_quantity = total_sales_quantity - :total_sales_quantity,	
							stock_available = stock_available + :stock_available
						WHERE product_id = :product_id
						";
						$stock_statement1 = $connect->prepare($stock_update_query1);
						$stock_statement1->execute(
							array(
								
								':total_sales_quantity'			=>	$total_pieces,
								':stock_available'				=>	$total_pieces,
								':product_id'					=>	$row["product_id"]
							)
						);
						$stock_result1 = $stock_statement1->fetchAll();
						if(isset($stock_result1))
						{
							echo "Stock updated for edited Product";
							$delete_query1 = "
							DELETE FROM inventory_order_product
							WHERE inventory_order_id = '".$_POST["inventory_order_id"]."' and product_id = '".$row["product_id"]."'
							";
							$statement1 = $connect->prepare($delete_query1);
							$statement1->execute();
							$delete_result1 = $statement1->fetchAll();
							if(isset($delete_result1))
							{
								echo " and Inventory details deleted for Product";	
								$flag=1;
							}
						}
				}
				if($flag==1)
				{
					//customer payment deletion and updation
								$sup_payment_update_query1 = "
								select bill_amount,amount_paid
								from customer_payment_details
								WHERE inventory_order_id = :inventory_order_id
								";
								$payment_statement1 = $connect->prepare($sup_payment_update_query1);
								$payment_statement1->execute(
									array(
										':inventory_order_id'					=>	$_POST["inventory_order_id"]
									)
								);
								$payment_result1 = $payment_statement1->fetch(PDO::FETCH_ASSOC);
								if(isset($payment_result1))
								{
									$bill_amount = $payment_result1["bill_amount"];
									$amount_paid = $payment_result1["amount_paid"];
									$sup_payment_update_query2 = "
									UPDATE customer_payment
									SET 
										total_amount_to_be_paid = total_amount_to_be_paid - :bill_amount,
										amount_paid = amount_paid - :amount_paid,		
										balance = balance - (:bill_amount-:amount_paid)
									WHERE customer_id = :customer_id
									";
									$payment_statement2 = $connect->prepare($sup_payment_update_query2);
									$payment_statement2->execute(
										array(
											':bill_amount'					=>	$bill_amount,
											':amount_paid'					=>	$amount_paid,
											':customer_id'					=>	$_POST["customer_id"]
										)
									);
									$payment_result2 = $payment_statement2->fetchAll();
									if(isset($payment_result2))
									{
										$delete_query2 = "
										DELETE FROM customer_payment_details
										WHERE inventory_order_id = '".$_POST["inventory_order_id"]."' and customer_id = '".$_POST["customer_id"]."'
										";
										$statement2 = $connect->prepare($delete_query2);
										$statement2->execute();
										$delete_result2 = $statement2->fetchAll();
										if(isset($delete_result2))
										{
											echo " Customer Payment Details updated";	
										}
									}
								}
				}	
			}
			
			//add code to insert new sales details here
			$query = "
			UPDATE inventory_order 
			SET 
				customer_id = :customer_id, 
				inventory_order_date = :inventory_order_date, 
				inventory_order_total = :inventory_order_total, 
				pdiscount = :pdiscount,
				bill_type = :bill_type, 
				payment_status = :payment_status, 
				inventory_order_status = :inventory_order_status,
				user_id  = :entered_by, 
				inventory_order_created_date = :date_of_entry,
				dispatch_through = :dispatch_through,
				delivery_address = :delivery_address,
				delivery_station = :delivery_station,
				order_no = :order_no,
				order_date = :order_date,
				truck_no = :truck_no,
				broker = :broker,
				dispatch_no = :dispatch_no
				
			WHERE inventory_order_id = :inventory_order_id
			";
			$flag=0;
			
			$statement = $connect->prepare($query);
			$statement->execute(
				array(
					':customer_id'					=>	$_POST['customer_id'],
					':inventory_order_date'			=>	$_POST['inventory_order_date'],
					':pdiscount'					=>	$_POST['pdiscount'],
					':inventory_order_total'		=>	$_POST['cbill_amount'],
					':bill_type'					=>	$_POST['bill_type'],
					':payment_status'				=>	$_POST['payment_status'],
					':inventory_order_status'		=>	'active',
					':entered_by'					=>	$_SESSION["user_id"],
					':date_of_entry'				=>	date("Y-m-d"),
					':inventory_order_id'			=>	$_POST['inventory_order_id'],
					':dispatch_through' => $_POST['dispatch_through'],
					':delivery_address' => $_POST['delivery_address'],
					':delivery_station' => $_POST['delivery_station'],
					':order_no' => $_POST['order_no'],
					':order_date' => $_POST['order_date'],
					':truck_no' => $_POST['truck_no'],
					':broker' => $_POST['broker'],
					':dispatch_no' => $_POST['dispatch_no']
				)
			);
			$result = $statement->fetchAll();
		//	$statement = $connect->query("SELECT LAST_UPDATE_ID()");
		//	$purchase_id = $statement->fetchColumn();
			$inventory_order_id = $_POST['inventory_order_id'];
			if(isset($inventory_order_id))
			{
				$total_amount = 0;
				for($count = 0; $count<count($_POST["product_id"]); $count++)
				{
					$product_details = fetch_product_details($_POST["product_id"][$count], $connect);
					$total_items_in_pieces = 0;
				
					$GST = $product_details['SGST'] + $product_details['CGST']; 
					//$size = $product_details['size'];
					//$base_price = ($_POST["unit_cost"][$count]/(100+$GST))*100;
					$base_price = $_POST["unit_cost"][$count];
					$quantity =	$_POST["quantity"][$count];
					
			//	$total_amount = round(($total_amount + (($base_price + $tax) * $quantity)),2);
					
					$SGST = $product_details['SGST'];
					$CGST = $product_details['CGST'];
					
					$SGST_tax = ($base_price * $SGST)/100;
					$CGST_tax = ($base_price * $CGST)/100;
				//	$total_amount = round($total_amount + (($base_price + $SGST_tax + $CGST_tax) * $quantity),2);
					
					$uom = $_POST['product_unit'][$count];
				//	$category_name = $product_details['category_name'];
				//	if($category_name=='chips' && $uom == 'Dozens')
				//		$total_items_in_pieces =round(($quantity/$product_details['unit_conversion']),2);
				//	else
					
					
				//	$total_items_in_pieces = $quantity;
					
				//	$total_amount = round($total_amount + (($base_price + $SGST_tax + $CGST_tax) * $total_items_in_pieces),2);
				
				
					if($uom=="Box")
						$size = 18;
					else
						$size = 1;
				//$total_amount = ($total_amount + (($base_price + $tax) * ($size* $quantity)));
			
				
					$total_items_in_pieces = $size * $quantity;
					$total_amount = $total_amount + (($base_price + $SGST_tax + $CGST_tax) * ($size*$quantity));
					
					$tax = $SGST_tax + $CGST_tax;
					$product_id = $_POST["product_id"][$count];
					$sub_query = "
					INSERT INTO inventory_order_product (inventory_order_id, product_id, price, quantity, tax, sale_uom) 
					VALUES (:inventory_order_id, :product_id, :price, :quantity, :tax, :sale_uom)
					";				
					$statement = $connect->prepare($sub_query);
					$statement->execute(
						array(
							':inventory_order_id'	=>	$inventory_order_id,
							':product_id'			=>	$product_id,
							':price'				=>	$base_price,
							':quantity'				=>	$quantity,
							':tax'					=>	$tax,
							':sale_uom'				=>	$uom
						)
					);
					
					$stock_details_query = "SELECT total_purchase_quantity, total_sales_quantity, stock_available from stock_details where product_id = '".$product_id."'";
					$stock_details = $connect->query($stock_details_query);
					
					$sdresult = $stock_details->fetch(PDO::FETCH_ASSOC);
		
					$rows = $stock_details->rowCount();
					$total_purchase_quantity = $sdresult['total_purchase_quantity'];
					$total_sales_quantity = $sdresult['total_sales_quantity'];
					$stock_available = $sdresult['stock_available'];
					if($rows>0)
					{
						/* convert into pieces and store */
					//	$total_purchase_quantity = $total_purchase_quantity + $_POST["quantity"][$count];
					//	$stock_available = $stock_available + $_POST["quantity"][$count]; 
						$total_sales_quantity = $total_sales_quantity + $total_items_in_pieces;
						$stock_available = $stock_available - $total_items_in_pieces;
						$update_sdquery = "
										UPDATE stock_details 
										SET total_sales_quantity = '".$total_sales_quantity."',
											stock_available = '".$stock_available."'	
										WHERE product_id = '".$product_id."'
										";
						$statement = $connect->prepare($update_sdquery);
						$statement->execute();
						$flag=1;
					}
					else
					{
						$insert_sdquery = "
										INSERT into stock_details(product_id, total_purchase_quantity, total_sales_quantity, stock_available)
										VALUES(:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available) 
										";
						$sdinsert = $connect->prepare($insert_sdquery);
						$sdinsert->execute(
							array(
								':product_id'				=>	$_POST["product_id"][$count],
								':total_purchase_quantity'	=>	0,
								':total_sales_quantity'		=>	$total_items_in_pieces,
								':stock_available'			=>	-$total_items_in_pieces
							)
						);
						$flag=1;
					}
					
				}
			}
			if($flag==1)
			{
				// customer payment update
				echo 'Sales Details Entered, Stock Updated';
					
				$customer_id = $_POST['customer_id'];
				$pdiscount = $_POST['pdiscount'];
				$total_amount -= round((($total_amount*$pdiscount)/100),0);
				$total_amount_to_be_paid = $total_amount;
				$amount_paid = 0;
				$balance = $total_amount;
				$entered_by = $_SESSION['user_id'];
				$date_of_entry = date('Y-m-d');
				
				$payment_status = $_POST["payment_status"];
		//		echo $payment_status;
				
				if($payment_status == "credit")
				{
					$amount_paid = 0;
					$balance = $total_amount_to_be_paid - $amount_paid;
					$mode_of_payment="credit";
					$cheque_number = 0;
					$cheque_date = 0000-00-00;
					$cheque_bank_name = 0;
					$date_of_payment =0000-00-00;
				}
				else
				{
					$mode_of_payment = $_POST['mode_of_payment'];
					$amount_paid = $_POST['amount_paid'];
					$balance = $total_amount_to_be_paid - $amount_paid;
					if($mode_of_payment == "cheque")
					{
						$cheque_number = $_POST['cheque_number'];
						$cheque_date = $_POST['cheque_date'];
						$cheque_bank_name = $_POST['cheque_bank_name'];
					}
					else
					{
						$cheque_number = 0;
						$cheque_date = 0000-00-00;
						$cheque_bank_name = 0;
					}
					$date_of_payment = $_POST['date_of_payment'];
				}
				
				
				$query_supplier_id = "SELECT * from customer_payment where customer_id = '".$customer_id."'";
				$statement = $connect->prepare($query_supplier_id);
				$statement->execute();
				$result = $statement->fetch(PDO::FETCH_ASSOC);
				$row = $statement->rowCount();
				$payment_id = $result['payment_id'];
				$old_total_amount_to_be_paid = $result['total_amount_to_be_paid'];
				$old_amount_paid = $result['amount_paid'];
				echo "old amount paid:".$old_amount_paid;
				$old_balance = $result['balance'];
				if($row>0)
				{
					//	echo $payment_id . " " . $old_total_amount_to_be_paid . " " . $old_amount_paid . " " . $balance;
						$supplier_payment_update_query = "
							UPDATE customer_payment 
							SET
								payment_id = :payment_id,
								customer_id = :customer_id,
								total_amount_to_be_paid = :total_amount_to_be_paid,
								amount_paid = :amount_paid, 
								balance = :balance,
								entered_by = :entered_by,
								date_of_entry = :date_of_entry
								where payment_id = '".$payment_id."'
						";
						$statement1 = $connect->prepare($supplier_payment_update_query);
						$statement1->execute(
							array(
								':payment_id'					=>	$payment_id,
								':customer_id'					=>	$customer_id,
								':total_amount_to_be_paid'		=>	round(($total_amount_to_be_paid + $old_total_amount_to_be_paid),0),
								':amount_paid'					=>	round(($amount_paid + $old_amount_paid),0),
								':balance'						=>	round(($balance + $old_balance),0),
								':entered_by'					=>	$_SESSION["user_id"],
								':date_of_entry'				=>	$date_of_entry,
								)
						);
						$result = $statement1->fetchAll();
						if(isset($result))
						{
							echo ' and Customer payment Updated...';
							echo '<br />';
							echo '<br />';
						}	
				}
				else
				{
					
					$supplier_payment_query = "
						INSERT INTO customer_payment (customer_id, total_amount_to_be_paid, amount_paid, balance, entered_by, date_of_entry) 
						VALUES (:customer_id, :total_amount_to_be_paid, :amount_paid, :balance, :entered_by, :date_of_entry)
						";
					$statement1 = $connect->prepare($supplier_payment_query);
					$statement1->execute(
							array(
								':customer_id'					=>	$customer_id,
								':total_amount_to_be_paid'		=>	$total_amount_to_be_paid,
								':amount_paid'					=>	$amount_paid,
								':balance'						=>	$balance,
								':entered_by'					=>	$_SESSION["user_id"],
								':date_of_entry'				=>	$date_of_entry
								)
					);
					$result = $statement1->fetchAll();
					$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
					$payment_id = $statement2->fetchColumn();				
				}
				if($payment_id)
				{
					$supplier_payment_details_query = "
						INSERT INTO customer_payment_details (payment_id, customer_id, inventory_order_id, bill_amount, amount_paid, balance, mode_of_payment, cheque_number, cheque_date, cheque_bank_name, date_of_payment) 
						VALUES (:payment_id, :customer_id, :inventory_order_id, :bill_amount, :amount_paid, :balance, :mode_of_payment, :cheque_number, :cheque_date, :cheque_bank_name, :date_of_payment)
						";
					$statement1 = $connect->prepare($supplier_payment_details_query);
					$statement1->execute(
							array(
								':payment_id'					=>	$payment_id,
								':customer_id'					=>	$customer_id,
								':inventory_order_id'			=>	$inventory_order_id,
								':bill_amount'					=>	$total_amount_to_be_paid,
								':amount_paid'					=>	$amount_paid,
								':balance'						=>	$balance,
								':mode_of_payment'				=>	$mode_of_payment,
								':cheque_number'				=>	$cheque_number,
								':cheque_date'					=>	$cheque_date,
								':cheque_bank_name'				=>	$cheque_bank_name,
								':date_of_payment'				=>	$date_of_payment
								)
					);
					$result = $statement1->fetchAll();
					if(isset($result))
					{
						echo ' and customer payment details Added...';
						echo '<br />';
						echo '<br />';
						// delete 
						
						
					}
				}
			}
			
		}
		else
			echo '<script type="text/javascript">alert("No products selected!! Cant Edit");</script>';
	
	
		
		
	}

	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		$query = "
		UPDATE inventory_order 
		SET inventory_order_status = :inventory_order_status 
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_status'	=>	$status,
				':inventory_order_id'		=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Sales Order status change to ' . $status;
		}
	}
}

?>
