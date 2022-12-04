<?php

//purchase_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	
	
	if($_POST['btn_action'] == 'Add')
	{
		
		$query = "
		INSERT INTO stock_entry_details (entered_by) 
							  VALUES (:entered_by)
		";
		$flag=0;
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':entered_by'					=>	$_SESSION["user_id"]
			)
		)or die("Error in Stock Entry Query" . print_r($statement->errorInfo()));
		
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
				INSERT INTO stock_entry_history (stock_entry_id,product_id,quantity,unit_of_measurement)
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
				) or die("Error in Stock_entry_history query" . print_r($statement->errorInfo()));
				
				$stock_details_query = "SELECT total_purchase_quantity, stock_available from stock_details where product_id = '".$product_id."'";
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
									UPDATE stock_details 
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
									INSERT into stock_details(product_id, total_purchase_quantity, total_sales_quantity, stock_available)
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
					$flag=1;
				}
			}
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM stock_entry_details
		WHERE stock_entry_id = :stock_entry_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':stock_entry_id'	=>	$_POST["stock_entry_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['date_of_entry'] = $row['date_of_entry'];
		}
		$sub_query = "
		SELECT * FROM stock_entry_history
		WHERE stock_entry_id = '".$_POST["stock_entry_id"]."'
		";
		$statement = $connect->prepare($sub_query);
		$statement->execute();
		$sub_result = $statement->fetchAll();
		$product_details = '';
		$count = 0;
		foreach($sub_result as $sub_row)
		{
			if($count==0)
				$count='';
			$unit_of_measurement = $sub_row['unit_of_measurement'];
		//	if($sale_uom == 'Bags' || $sale_uom == 'Box' || $sale_uom == 'Packet' || $sale_uom == 'Dozens' || $sale_uom == 'Nos')
		//	{
		//		$product_det = fetch_product_details($sub_row['product_id'], $connect);
		//		$quantity = $sub_row["quantity"]/$product_det['unit_conversion'];
		//	}
		//	else
		//		$quantity = $sub_row["quantity"];
			$quantity = $sub_row["quantity"];
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
						<input type="hidden" name="hidden_product_id[]" class="form-control hidden_product_id" id="hidden_product_id'.$count.'" value="'.$sub_row["product_id"].'" />
					</div>
					<div class="col-md-3">
						<input type="text" name="quantity[]" id="quantity'.$count.'" class="form-control quantity" value="'.$quantity.'" required />
					</div>
					<div class="col-md-4">
						<select name="product_unit[]" id="product_unit'.$count.'" class="form-control selectpicker product_unit" data-live-search="true" required>
						';
							
							$units = Array("Bags","Bottles","Box","Dozens", "Feet", "Gallon", "Grams", "Inch", "Kg", "Liters", "Meter", "Nos", "Packet", "Pieces", "Rolls");
							foreach($units as $uom)
							{
								$product_details .= '<option value="'.$uom.'"';
								if($unit_of_measurement == $uom) 
									$product_details .= ' selected>';
								else 
									$product_details .= '>';
								$product_details .= $uom .'</option>';
							}
						
						$product_details .= '</select><input type="hidden" name="hidden_product_unit[]" id="hidden_product_unit'.$count.'" />
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
		//	echo $count;
		}
		$output['product_details'] = $product_details;
		$output['old_count_hidden'] = $count;
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'fetch_SGST_CGST')
	{
		$product_details = fetch_product_details($_POST["product_id"], $connect);
		$output['SGST']=$product_details['SGST'];
		$output['CGST']=$product_details['CGST'];
		$output['product_unit'] = $product_details["product_unit"];
		$output['unit_conversion']=$product_details['unit_conversion'];
		echo json_encode($output);		
		
	//	c = count($_POST["quantity"]);
	//	echo '<script type="text/javascript">alert(c);</script>';
		
/*		$product_details = fetch_product_details($_POST["product_id"][0], $connect);
		$output['SGST_tax']=$product_details['SGST'];
		$output['CGST_tax']=$product_details['CGST'];
		$output['bill_amount'] = $product_details["SGST"]*$product_details["CGST"];
		$output['total_amount']=1121;
		echo json_encode($output);	*/
	}

	if($_POST['btn_action'] == 'Edit')
	{
		if(isset($_POST["stock_entry_id"]))
			$count = count($_POST["product_id"]);
		else
			$count=0;
		if($count>0)
		{
			$flag=0;
			//echo "Stock: " . $_POST["purchase_id"] . " " . "<br />";
			$stock_details_query = "
			SELECT *
			FROM stock_entry_history
			WHERE stock_entry_id = '".$_POST["stock_entry_id"]."'
			order by stock_entry_history.stock_entry_id ASC
			";
			$stock_details = $connect->query($stock_details_query);
			$sdresult = $stock_details->fetchALL(PDO::FETCH_ASSOC);
			$rows = $stock_details->rowCount();
			if($rows>0)
			{
				foreach($sdresult as $row)
				{
					$product_details = fetch_product_details($row["product_id"], $connect);
					$product_name = $product_details['product_name'];
					$product_unit = $product_details['product_unit'];
					$size = $product_details['size'];
				/*	$unit_conversion = $row["unit_conversion"];	*/
					$unit_of_measurement=$row['unit_of_measurement'];					
					$quantity = $row["quantity"];
					if($product_unit != $unit_of_measurement && $unit_of_measurement=="Pieces" && ($product_name=="jaggery" or $product_name=="Jaggery"))
					{
						$pieces_into_box = round($quantity/$size,2);
					}
					else
						$pieces_into_box = $quantity;
			//		echo "product id : " . $row["product_id"] . " , Pieces :" . $total_pieces . "<br/>";
						$stock_update_query1 = "
						UPDATE stock_details
						SET 
							total_purchase_quantity = total_purchase_quantity - :total_purchase_quantity,	
							stock_available = stock_available - :stock_available
						WHERE product_id = :product_id
						";
						$stock_statement1 = $connect->prepare($stock_update_query1);
						$stock_statement1->execute(
							array(
								
								':total_purchase_quantity'		=>	$pieces_into_box, //$quantity,
								':stock_available'				=>	$pieces_into_box, //$quantity,
								':product_id'					=>	$row["product_id"]
							)
						);
						$stock_result1 = $stock_statement1->fetchAll();
						if(isset($stock_result1))
						{
						//	echo "Stock updated for edited Product";
							$delete_query1 = "
							DELETE FROM stock_entry_history
							WHERE stock_entry_id = '".$_POST["stock_entry_id"]."' and product_id = '".$row["product_id"]."'
							";
							$statement1 = $connect->prepare($delete_query1);
							$statement1->execute();
							$delete_result1 = $statement1->fetchAll();
							if(isset($delete_result1))
							{
						//		echo " and Purchase details deleted for Product";	
								$flag=1;
							}
						}
				}
			}
			//add code to insert new purchase details here
			
			$query = "
			UPDATE stock_entry_details 
			SET 
				entered_by  = :entered_by, 
			WHERE stock_entry_id = :stock_entry_id
			";
			$flag=0;
			
			$statement = $connect->prepare($query);
			$statement->execute(
				array(
					':entered_by'					=>	$_SESSION["user_id"],
					':stock_entry_id'				=>	$_POST['stock_entry_id']
					
				)
			);
			$result = $statement->fetchAll();
		//	$statement = $connect->query("SELECT LAST_UPDATE_ID()");
		//	$purchase_id = $statement->fetchColumn();
			$stock_entry_id = $_POST['stock_entry_id'];
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
						INSERT INTO stock_entry_history (stock_entry_id,product_id,quantity,unit_of_measurement)
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
						) or die("Error in Stock_entry_history query" . print_r($statement->errorInfo()));
						
						$stock_details_query = "SELECT total_purchase_quantity, stock_available from stock_details where product_id = '".$product_id."'";
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
							//if($product_unit != $unit_of_measurement && ($product_name=="jaggery" or $product_name=="Jaggery"))
								
							/* convert into pieces into box for jaggery or Jaggery and store in stock */
							
							$total_purchase_quantity = $total_purchase_quantity + $pieces_into_box;
					//		echo " = new stock :" . $total_purchase_quantity . "<br/>";
							$stock_available = $stock_available + $pieces_into_box;
							$update_sdquery = "
											UPDATE stock_details 
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
											INSERT into stock_details(product_id, total_purchase_quantity, total_sales_quantity, stock_available)
											VALUES(:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available) 
											";
							$sdinsert = $connect->prepare($insert_sdquery);
							$sdinsert->execute(
								array(
									':product_id'				=>	$_POST["product_id"][$count],
									':total_purchase_quantity'	=>	$pieces_into_box, //$quantity,
									':total_sales_quantity'		=>	0,
									':stock_available'			=>	$pieces_into_box, $quantity
								)
							);
							$flag=1;
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
		UPDATE purchase_invoice 
		SET purchase_status = :purchase_status 
		WHERE purchase_id = :purchase_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':purchase_status'	=>	$status,
				':purchase_id'		=>	$_POST["purchase_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Purchase status change to ' . $status;
		}
	}
}

?>