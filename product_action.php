<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_supplier')
	{
		echo fill_supplier_list($connect);
	}
	
	if($_POST['btn_action'] == 'fill_sgst')
	{
		echo "0";
	}
	if($_POST['btn_action'] == 'fetch_category_name')
	{
		$query = "select *from category where category_id = '".$_POST['category_id']."'";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		$output['category_name'] = $result['category_name'];
		echo json_encode($output);
		
	}
	if($_POST['btn_action'] == 'Add')
	{
		
		$query = "
		INSERT INTO product (product_name, product_unit,tax_status, SGST, CGST, min_stock_quantity, init_stock_quantity,as_on_date, 
		entered_by, product_status,HSN_code,size) 
		VALUES (:product_name, :product_unit,:tax_status, :SGST, :CGST, :min_stock_quantity, :init_stock_quantity,:as_on_date,
		:entered_by, :product_status,:HSN_code,:size)
		";
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_name'			=>	$_POST['product_name'],
				':product_unit'			=>	$_POST['product_unit'],
				':tax_status'			=>	$_POST['tax_status'],
				':SGST'					=>	$_POST['SGST'],
				':CGST'					=>	$_POST['CGST'],
				':min_stock_quantity'	=>	$_POST['min_stock_quantity'],
				':init_stock_quantity'	=>	$_POST['init_stock_quantity'],
				':as_on_date'			=>	$_POST['as_on_date'],
				':entered_by'			=>	$_SESSION["user_id"],
				':product_status'		=>	'active',
				':HSN_code'				=>	$_POST['HSN_code'],
				':size'					=>	$_POST['size'],
			)
		);
		$result = $statement->fetchAll();
		$statement2 = $connect->query("SELECT LAST_INSERT_ID()");
		$product_id = $statement2->fetchColumn();
		if(isset($product_id))
		{
			echo 'Product and ';
			$query1 = "
			INSERT INTO stock_details (product_id, total_purchase_quantity, total_sales_quantity, stock_available) 
			VALUES (:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available)
			";
			$statement1 = $connect->prepare($query1);
			$statement1->execute(
				array(
					':product_id'						=>	$product_id,
					':total_purchase_quantity'			=>	$_POST['init_stock_quantity'],
					':total_sales_quantity'				=>	0,
					':stock_available'					=>	$_POST['init_stock_quantity']
				)
			);
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Stock Details Added';
			}
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM product
		INNER JOIN category ON category.category_id = product.category_id
		INNER JOIN supplier_details ON supplier_details.supplier_id = product.supplier_id
		INNER JOIN user_details ON user_details.user_id = product.entered_by
		WHERE product.product_id = '".$_POST["product_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Product ID</td>
				<td>'.$row["product_id"].'</td>
			</tr>
			<tr>
				<td>Product Name</td>
				<td>'.$row["product_name"].'</td>
			</tr>
			<tr>
				<td>HSN Code</td>
				<td>'.$row["HSN_code"].'</td>
			</tr>
			<tr>
				<td>Size</td>
				<td>'.$row["size"].'</td>
			</tr>
		
			<tr>
				<td>Unit of Measurement</td>
				<td>'.$row["product_unit"].'</td>
			</tr>
			<tr>
				<td>Tax Status</td>
				<td>'.$row["tax_status"].'</td>
			</tr>
			<tr>
				<td>SGST</td>
				<td>'.$row["SGST"].'</td>
			</tr>
			<tr>
				<td>CGST</td>
				<td>'.$row["CGST"].'</td>
			</tr>
			<tr>
				<td>Minimum Stock Quantity</td>
				<td>'.$row["min_stock_quantity"].'</td>
			</tr>
			<tr>
				<td>Initial Stock Quantity</td>
				<td>'.$row["init_stock_quantity"].'</td>
			</tr>
			<tr>
				<td>Date of Initial Stock Quantity</td>
				<td>'.$row["as_on_date"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.$row["user_name"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM product WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'	=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
		//	$output['category_id'] = $row['category_id'];
			$output['product_name'] = $row['product_name'];
		//	$output['supplier_id'] = $row['supplier_id'];
			$output["supplier_select_box"] = fill_supplier_list($connect);
			$output['HSN_code'] = $row['HSN_code'];
			$output['size'] = $row['size'];
		//	$output['grade'] = strtoupper($row['grade']);
			$output['product_unit'] = $row['product_unit'];
		//	$output['unit_conversion'] = $row['unit_conversion'];
			$output['tax_status'] = $row['tax_status'];
			$output['SGST'] = $row['SGST'];
			$output['CGST'] = $row['CGST'];
			$output['min_stock_quantity'] = $row['min_stock_quantity'];
			$output['init_stock_quantity'] = $row['init_stock_quantity'];
			$output['as_on_date'] = $row['as_on_date'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		if(isset($_POST['unit_conversion']))
			$unit_conversion = $_POST['unit_conversion'];
		else
			$unit_conversion = 0;
		$product_id = $_POST['product_id'];
		$q = "select init_stock_quantity from product where product_id = '".$product_id."'";
			$statement = $connect->prepare($q);
			$statement->execute();
			$result = $statement->fetch(PDO::FETCH_ASSOC);
		$old_init_stock_quantity = $result["init_stock_quantity"];
		$query = "

		UPDATE product 
		set 
		product_name = :product_name,
		product_unit = :product_unit,
		HSN_code = :HSN_code,
		size = :size,
		tax_status = :tax_status, 
		SGST = :SGST, 
		CGST = :CGST, 
		min_stock_quantity = :min_stock_quantity,
		init_stock_quantity = :init_stock_quantity,
		as_on_date = :as_on_date 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'			=>	$_POST['product_id'],
				
				':product_name'			=>	$_POST['product_name'],
				
				':product_unit'			=>	$_POST['product_unit'],
				':HSN_code'				=>	$_POST['HSN_code'],
				':size'					=>	$_POST['size'],
				//':grade'				=>	$_POST['grade'],
				
				':tax_status'			=>	$_POST['tax_status'],
				':SGST'					=>	$_POST['SGST'],
				':CGST'					=>	$_POST['CGST'],
				':min_stock_quantity'	=>	$_POST['min_stock_quantity'],
				':init_stock_quantity'	=>	$_POST['init_stock_quantity'],
				':as_on_date'			=>	$_POST['as_on_date']
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Details Edited';
			$q1 = "select product_id from stock_details where product_id = '".$product_id."'";
			$statement2 = $connect->prepare($q1);
			$result2  = $statement2->execute();
			$result3 = $statement2->fetchAll(PDO::FETCH_COLUMN,0);
			if($result3)
			{
				$query1 = "
				UPDATE stock_details SET
				product_id = :product_id,
				total_purchase_quantity = total_purchase_quantity - $old_init_stock_quantity + :total_purchase_quantity,
				total_sales_quantity = total_sales_quantity - :total_sales_quantity,
				stock_available = stock_available - $old_init_stock_quantity + :stock_available
				where product_id = :product_id
				";
				$statement1 = $connect->prepare($query1);
				$result1  = $statement1->execute(
					array(
						':product_id'						=>	$product_id,
						':total_purchase_quantity'			=>	$_POST['init_stock_quantity'],
						':total_sales_quantity'				=>	0,
						':stock_available'					=>	$_POST['init_stock_quantity']
					)
				);
			//	$result = $statement->fetchAll();
				if(isset($result1))
				{
					echo 'Stock Details updated';
				}
			}
			else
			{
				$query1 = "
				INSERT INTO stock_details (product_id, total_purchase_quantity, total_sales_quantity, stock_available) 
				VALUES (:product_id, :total_purchase_quantity, :total_sales_quantity, :stock_available)
				";
				$statement1 = $connect->prepare($query1);
				$statement1->execute(
					array(
						':product_id'						=>	$product_id,
						':total_purchase_quantity'			=>	$_POST['init_stock_quantity'],
						':total_sales_quantity'				=>	0,
						':stock_available'					=>	$_POST['init_stock_quantity']
					)
				);
				$result = $statement->fetchAll();
				if(isset($result))
				{
					echo 'Stock Details Added';
				}
			}
			
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		$query = "
		UPDATE product 
		SET product_status = :product_status 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_status'	=>	$status,
				':product_id'		=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product status change to ' . $status;
		}
	}
}


?>