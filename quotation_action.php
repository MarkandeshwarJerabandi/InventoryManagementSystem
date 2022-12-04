<?php

//quotation_action.php

include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO qinventory_order (user_id, inventory_order_total, inventory_order_date, customer_id, inventory_order_status, inventory_order_created_date) 
		VALUES (:user_id, :inventory_order_total, :inventory_order_date, :customer_id, :inventory_order_status, :inventory_order_created_date)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'						=>	$_SESSION["user_id"],
				':inventory_order_total'		=>	0,
				':inventory_order_date'			=>	$_POST['inventory_order_date'],
				':customer_id'					=>	$_POST['customer_id'],
				':inventory_order_status'		=>	'active',
				':inventory_order_created_date'	=>	date("Y-m-d")
			)
		);
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
				INSERT INTO qinventory_order_product (inventory_order_id, product_id, quantity, price, sale_uom) 
				VALUES (:inventory_order_id, :product_id, :quantity, :price, :sale_uom)
				";
				$statement = $connect->prepare($sub_query);
				$base_price = $_POST["unit_cost"][$count];
				$quantity =	$_POST["quantity"][$count];
				
				$statement->execute(
					array(
						':inventory_order_id'	=>	$inventory_order_id,
						':product_id'			=>	$_POST["product_id"][$count],
						':quantity'				=>	$_POST["quantity"][$count],
						':price'				=>	$base_price,
						':sale_uom'				=>	$_POST["product_unit"][$count]
					)
				);
				
				$total_amount = ($total_amount + ($base_price * $quantity));
				$product_id = $_POST["product_id"][$count];
				$uom = $_POST['product_unit'][$count];
				$category_name = strtolower($product_details['category_name']);
				if(($category_name == 'chips' || $category_name == 'CHIPS') && $uom == 'Dozens')
					$total_items_in_box =round(($quantity/$product_details['unit_conversion']),4);
				else
					$total_items_in_box =$quantity;
			}
			$update_query = "
			UPDATE qinventory_order 
			SET inventory_order_total = '".$total_amount."' 
			WHERE inventory_order_id = '".$inventory_order_id."'
			";
			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo "Quotation has been created";
			}
		}
	}
	
	if($_POST['btn_action'] == 'fetch_SGST_CGST')
	{
		$product_details = fetch_product_details($_POST["product_id"], $connect);
		$output['SGST']=$product_details['SGST'];
		$output['CGST']=$product_details['CGST'];
		$output['product_unit'] = $product_details["product_unit"];
		$output['unit_conversion']=$product_details['unit_conversion'];
		echo json_encode($output);
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM qinventory_order
		
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
			$output['inventory_order_date'] = $row['inventory_order_date'];
		}
		$sub_query = "
		SELECT * FROM qinventory_order_product 
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
			$unit_cost = round(($sub_row["price"]),0);
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
		$output['product_details'] = $product_details;	
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
			FROM qinventory_order_product
			INNER JOIN product ON product.product_id = qinventory_order_product.product_id
			WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
			order by qinventory_order_product.product_id ASC
			";
			$stock_details = $connect->query($stock_details_query);
			$sdresult = $stock_details->fetchALL(PDO::FETCH_ASSOC);
			$rows = $stock_details->rowCount();
			if($rows>0)
			{
				foreach($sdresult as $row)
				{
					$product_details = fetch_product_details($row["product_id"], $connect);
					$unit_conversion = $row["unit_conversion"];
					$sales_uom=$row['sale_uom'];
					$category_name = strtolower($product_details['category_name']);
					if($category_name=='chips' && $sales_uom == 'Dozens')
						$total_pieces = round(($row["quantity"]/$unit_conversion),2);
					else
						$total_pieces = $row["quantity"];
			//		echo "product id : " . $row["product_id"] . " , Pieces :" . $total_pieces . "<br/>";
					$delete_query1 = "
							DELETE FROM qinventory_order_product
							WHERE inventory_order_id = '".$_POST["inventory_order_id"]."' and product_id = '".$row["product_id"]."'
							";
					$statement1 = $connect->prepare($delete_query1);
					$statement1->execute();
					$delete_result1 = $statement1->fetchAll();
					if(isset($delete_result1))
					{
							//	echo " and Inventory details deleted for Product";	
								$flag=1;
					}
				}
			}
			
			//add code to insert new sales details here
			$query = "
			UPDATE qinventory_order 
			SET 
				customer_id = :customer_id, 
				inventory_order_date = :inventory_order_date, 
				inventory_order_total = :inventory_order_total, 
				inventory_order_status = :inventory_order_status,
				user_id  = :entered_by, 
				inventory_order_created_date = :date_of_entry
			WHERE inventory_order_id = :inventory_order_id
			";
			$flag=0;
			
			$statement = $connect->prepare($query);
			$statement->execute(
				array(
					':customer_id'					=>	$_POST['customer_id'],
					':inventory_order_date'			=>	$_POST['inventory_order_date'],
					':inventory_order_total'		=>	$_POST['cbill_amount'],
					':inventory_order_status'		=>	'active',
					':entered_by'					=>	$_SESSION["user_id"],
					':date_of_entry'				=>	date("Y-m-d"),
					':inventory_order_id'			=>	$_POST['inventory_order_id']					
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
					$base_price = $_POST["unit_cost"][$count];
					$quantity =	$_POST["quantity"][$count];
			
					$uom = $_POST['product_unit'][$count];
					$category_name = $product_details['category_name'];
					if($category_name=='chips' && $uom == 'Dozens')
						$total_items_in_pieces =round(($quantity/$product_details['unit_conversion']),2);
					else
						$total_items_in_pieces =$quantity;
					
					$total_amount = $total_amount + ($base_price * $quantity);
					
					
					$product_id = $_POST["product_id"][$count];
					$sub_query = "
					INSERT INTO qinventory_order_product (inventory_order_id, product_id, price, quantity, sale_uom) 
					VALUES (:inventory_order_id, :product_id, :price, :quantity, :sale_uom)
					";				
					$statement = $connect->prepare($sub_query);
					$statement->execute(
						array(
							':inventory_order_id'	=>	$inventory_order_id,
							':product_id'			=>	$product_id,
							':price'				=>	$base_price,
							':quantity'				=>	$quantity,
							':sale_uom'				=>	$uom
						)
					);
				}
				echo "Quotation Details updated";
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
		UPDATE qinventory_order 
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
			echo 'Quotation Order status change to ' . $status;
		}
	}
}

?>
