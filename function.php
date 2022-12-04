<?php
//function.php


/*function fill_brand_list($connect, $category_id)
{
	$query = "SELECT * FROM brand 
	WHERE brand_status = 'active' 
	AND category_id = '".$category_id."'
	ORDER BY brand_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Brand</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["brand_id"].'">'.$row["brand_name"].'</option>';
	}
	return $output;
} */

function fill_supplier_list($connect)
{
	$query = "SELECT * FROM supplier_details 
	WHERE supplier_status = 'active' 
	ORDER BY firm_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Supplier</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["supplier_id"].'">'.$row["firm_name"].'</option>';
	}
	return $output;
}

function fill_customer_list($connect)
{
	$query = "SELECT * FROM customer_details 
	WHERE customer_status = 'active' 
	ORDER BY customer_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Customer</option>';
//	$output .= '<option value="cashbill">Cash Bill</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["customer_id"].'">'.$row["customer_name"].'</option>';
	}
	$output .= '<option value="other">Other/CashBill</option>';
	return $output;
}

function fill_sgst()
{
	
	$output = 0;
	
	return $output;
}

function get_user_name($connect, $user_id)
{
	$query = "
	SELECT user_name FROM user_details WHERE user_id = '".$user_id."'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['user_name'];
	}
}

function fill_product_list($connect)
{
	$query = "
	SELECT * FROM product 
	WHERE product_status = 'active' 
	ORDER BY product_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	$output = '<option value="">Select Product</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["product_id"].'">'.$row["product_name"].' </option>';
	}
	return $output;
}

function fill_product_HSG_list($connect)
{
	$query = "
	SELECT * FROM product 
	WHERE product_status = 'active' 
	ORDER BY product_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	$output = '<option value="">Select Product</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["product_id"].'">'.$row["product_name"].' </option>';
	}
	return $output;
}

function fill_season_list()
{
	$output = '';
	$output .= '<option value="">Select Season Year</option>';
	$output .= '<option value="2020-21">2020-21</option>';
    $output .= '<option value="2021-22">2021-22</option>';
	$output .= '<option value="2022-23">2022-23</option>';
	$output .= '<option value="2023-24">2023-24</option>';
	$output .= '<option value="2024-25">2024-25</option>';
	$output .= '<option value="2025-26">2025-26</option>';
	$output .= '<option value="2026-27">2026-27</option>';
	$output .= '<option value="2027-28">2027-28</option>';
	$output .= '<option value="2028-29">2028-29</option>';
	$output .= '<option value="2029-30">2029-30</option>';
	return $output;
}

function fill_sugarcane_list()
{
	$output = '';
	$output .= '<option value="">Select Sugar Cane Variety</option>';
	$output .= '<option value="671">671</option>';
    $output .= '<option value="86032">86032</option>';
	$output .= '<option value="8005">8005</option>';
	$output .= '<option value="10001">10001</option>';
	$output .= '<option value="M86">M86</option>';
	return $output;
}

function fill_product_units()
{
	$output = '';
	$output .= '<option value="">Select UoM</option>';
	$output .= '<option value="Bags">Bags</option>';
    $output .= '<option value="Box">Box</option>';
	$output .= '<option value="Dozens">Dozens</option>';
	$output .= '<option value="Kg">Kg</option>';
	$output .= '<option value="Liters">Liters</option>';
	$output .= '<option value="Packet">Packet</option>';
	$output .= '<option value="Pieces">Pieces</option>';
	$output .= '<option value="Rolls">Tons</option>';
	return $output;
}

function fill_product_units_selected($sale_uom)
{
	$output = '';
	$output .= '<option value="">Select UoM</option>';
	$output .= '<option value="Bags" <?php if($sale_uom == "Bags") echo "selected"; else "";?>>Bags</option>';
    $output .= '<option value="Bottles" <?php if($sale_uom == "Bottles") echo "selected"; else "";?>>Bottles</option>';
    $output .= '<option value="Box" <?php if($sale_uom == "Box") echo "selected"; else ""; ?>>Box</option>';
	$output .= '<option value="Dozens" <?php  if($sale_uom == "Dozens") echo "selected"; else ""; ?>>Dozens</option>';
	$output .= '<option value="Feet" <?php if($sale_uom == "Feet") echo "selected"; else "";?>>Feet</option>';
	$output .= '<option value="Gallon" <?php if($sale_uom == "Gallon") echo "selected"; else ""; ?>>Gallon</option>';
	$output .= '<option value="Grams" <?php  if($sale_uom == "Grams") echo "selected"; else ""; ?>>Grams</option>';
	$output .= '<option value="Inch" <?php  if($sale_uom == "Inch") echo "selected"; else ""; ?>>Inch</option>';
	$output .= '<option value="Kg" <?php  if($sale_uom == "Kg") echo "selected"; else ""; ?>>Kg</option>';
	$output .= '<option value="Liters" <?php  if($sale_uom == "Liters") echo "selected"; else ""; ?>>Liters</option>';
	$output .= '<option value="Meter" <?php  if($sale_uom == "Meter") echo "selected"; else ""; ?>>Meter</option>';
	$output .= '<option value="Nos" <?php  if($sale_uom == "Nos") echo "selected"; else ""; ?>>Nos</option>';
	$output .= '<option value="Packet" <?php  if($sale_uom == "Packet") echo "selected"; else ""; ?>>Packet</option>';
	$output .= '<option value="Pieces" <?php  if($sale_uom == "Pieces") echo "selected"; else ""; ?>>Pieces</option>';
	$output .= '<option value="Rolls" <?php  if($sale_uom == "Rolls") echo "selected"; else ""; ?>>Rolls</option>';
	return $output;
}

function fill_customer_using_payment_id($connect,$payment_id)
{
	//echo $payment_id;
	$query = "
	SELECT * FROM customer_payment 
	INNER JOIN customer_id ON customer_details.customer_id = customer_payment.customer_id
	WHERE payment_id = '".$payment_id."' 
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	$customer_id = $result["customer_id"];
	$customer_name = $result["customer_name"];
	echo $customer_id; 
	echo $customer_name;
	return $result;
}

function fetch_product_details($product_id, $connect)
{
	$query = "
	SELECT * FROM product
	WHERE product_id = '".$product_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['product_name'] = $row["product_name"];
		$output['SGST'] = $row["SGST"];
		$output['CGST'] = $row["CGST"];
		$output['product_unit'] = $row["product_unit"];
		$output['HSN_code'] = $row["HSN_code"];
		$output['size'] = $row["size"];
		$output['grade'] = $row["grade"];
	}
	return $output;
}

function available_product_quantity($connect, $product_id)
{
	$product_data = fetch_product_details($product_id, $connect);
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.product_id = '".$product_id."' AND
	inventory_order.inventory_order_status = 'active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['quantity']) - intval($total);
	if($available_quantity == 0)
	{
		$update_query = "
		UPDATE product SET 
		product_status = 'inactive' 
		WHERE product_id = '".$product_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
	return $available_quantity;
}

function count_total_user($connect)
{
	$query = "
	SELECT * FROM user_details WHERE user_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_display_products($connect)
{
	$query = "
	SELECT sum(unit_display) as unit_display FROM display_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return number_format($result['unit_display'],2);
}

function count_total_display_products_amount($connect)
{
	$query = "
	SELECT sum(total_display_amount) as total_display_amount FROM display_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return number_format($result['total_display_amount'],2);
}

function count_total_customers($connect)
{
	$query = "
	SELECT * FROM customer_details WHERE customer_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}
function count_total_outlets($connect)
{
	$query = "
	SELECT * FROM outlet WHERE outlet_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}
function count_total_suppliers($connect)
{
	$query = "
	SELECT * FROM supplier_details WHERE supplier_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_products($connect)
{
	$query = "
	SELECT * FROM product WHERE product_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}
function count_total_outlet_products($connect)
{
	$query = "
	SELECT * FROM ostock_details";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}


function count_total_brand($connect)
{
	$query = "
	SELECT * FROM brand WHERE brand_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_product($connect)
{
	$query = "
	SELECT * FROM product WHERE product_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_purchases($connect)
{
	$query = "
	SELECT sum(total_purchase_quantity) as total_purchase_quantity FROM stock_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return number_format($result['total_purchase_quantity'],2);
}
function count_total_sales($connect)
{
	$query = "
	SELECT sum(total_sales_quantity) as total_sales_quantity FROM stock_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return number_format($result['total_sales_quantity'],2);
}
function count_total_outlet_sales($connect)
{
	$query = "
	SELECT sum(total_sales_quantity) as total_sales_quantity FROM ostock_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	return number_format($result['total_sales_quantity'],2);
}

function count_total_stock($connect)
{
	$query = "
	SELECT sum(stock_available) as stock_available FROM stock_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	$unit_display = count_total_display_products($connect);
	return number_format(($result['stock_available']-$unit_display),2);
}

function count_total_outlet_stock($connect)
{
	$query = "
	SELECT sum(stock_available) as stock_available FROM ostock_details
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetch(PDO::FETCH_ASSOC);
	
	$unit_display = count_total_display_products($connect);
	return number_format(($result['stock_available']-$unit_display),2);
}

function get_min_stock_details($connect)
{
	$query = '
	SELECT * 
	FROM stock_details 
	INNER JOIN product ON product.product_id = stock_details.product_id
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$rowcount = $statement->rowCount();
	$msg = '';
	if($rowcount>0)
	{
		$msg .= 'Dear Owner, Kindly Place the Order for the following!!!<br/>';
		foreach($result as $row)
		{
			if($row["stock_available"] < $row["min_stock_quantity"])
			{		
				$msg .= $row['product_name'] . ' =>  ' . $row['stock_available'] . '<br />';
			}
			else
				$msg .= '';
		}
	}
	
	return $msg;
}
function get_product_wise_total_stock($connect)
{
	$query = '
	SELECT * 
	FROM stock_details 
	INNER JOIN product ON product.product_id = stock_details.product_id
	
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>Product Name</th>
				<th>Total Purchase Count</th>
				<th>Total Sales Count</th>
				<th>Total Display Count</th>
				<th>Total Stock Available</th>
				<th>Remarks</th>
			</tr>
	';

	$total_purchase = 0;
		$total_sales = 0;
		$total_stock = 0;
	$ototal_purchase = 0;
		$ototal_sales = 0;
		$ototal_stock = 0;
		$ounit_display = 0;
	$current_stock=0;
	foreach($result as $row)
	{
		$query1 = "
		SELECT unit_display FROM display_details where product_id = '".$row['product_id']."'
		";
		$statement1 = $connect->prepare($query1);
		$statement1->execute();
		if($result1 = $statement1->fetch(PDO::FETCH_ASSOC))
			$unit_display = $result1['unit_display'];
		else
			$unit_display = 0;
		
		$total_purchase =   $row["total_purchase_quantity"];
		$total_sales = $row["total_sales_quantity"];
		$total_stock =   $row["stock_available"] - $unit_display;
		
		$ototal_purchase +=   $row["total_purchase_quantity"];
		$ototal_sales += $row["total_sales_quantity"];
		$ototal_stock +=   $row["stock_available"] - $unit_display;
		$ounit_display += $unit_display;
		$current_stock = $row["stock_available"] - $unit_display;
		
		if($current_stock > $row["min_stock_quantity"])
		{
			$output .= ' <tr>';
		}
		else
		{
			$output .= ' <tr style="color:red;">';
		}
		$output .= ' <td align="center">'.$row['product_name'].'</td>
		<td align="right">'.$total_purchase.'</td>
		<td align="right">'.$total_sales.'</td>
		<td align="right">'.$unit_display.'</td>
		<td align="right">'.$total_stock.'</td>';
		if($row["stock_available"] > $row["min_stock_quantity"])
		{
			$output .= ' <td align="right"></td>';
		}
		else
			$output .= ' <td align="right">Stock is Less Than Minimum! Kindly Place the Order!!!</td>';
		$output .= '</tr>';

				
	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b>'.number_format($ototal_purchase,2).'</b></td>
		<td align="right"><b>'.number_format($ototal_sales,2).'</b></td>
		<td align="right"><b>'.number_format($ounit_display,2).'</b></td>
		<td align="right"><b>'.number_format($ototal_stock,2).'</b></td>
	</tr></table></div>
	';
	return $output;
}
function get_product_wise_total_outlet_stock($connect)
{
	$query = '
	SELECT * 
	FROM ostock_details 
	INNER JOIN product ON product.product_id = stock_details.product_id
	
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>Product Name</th>
				<th>Total Stock Allocation</th>
				<th>Total Sales Count</th>
				<th>Total Display Count</th>
				<th>Total Stock Available</th>
				<th>Remarks</th>
			</tr>
	';

	$total_purchase = 0;
		$total_sales = 0;
		$total_stock = 0;
	$ototal_purchase = 0;
		$ototal_sales = 0;
		$ototal_stock = 0;
		$ounit_display = 0;
	$current_stock=0;
	foreach($result as $row)
	{
		$query1 = "
		SELECT unit_display FROM display_details where product_id = '".$row['product_id']."'
		";
		$statement1 = $connect->prepare($query1);
		$statement1->execute();
		if($result1 = $statement1->fetch(PDO::FETCH_ASSOC))
			$unit_display = $result1['unit_display'];
		else
			$unit_display = 0;
		
		$total_purchase =   $row["total_purchase_quantity"];
		$total_sales = $row["total_sales_quantity"];
		$total_stock =   $row["stock_available"] - $unit_display;
		
		$ototal_purchase +=   $row["total_purchase_quantity"];
		$ototal_sales += $row["total_sales_quantity"];
		$ototal_stock +=   $row["stock_available"] - $unit_display;
		$ounit_display += $unit_display;
		$current_stock = $row["stock_available"] - $unit_display;
		
		if($current_stock > $row["min_stock_quantity"])
		{
			$output .= ' <tr>';
		}
		else
		{
			$output .= ' <tr style="color:red;">';
		}
		$output .= ' <td align="center">'.$row['product_name'].'</td>
		<td align="right">'.$total_purchase.'</td>
		<td align="right">'.$total_sales.'</td>
		<td align="right">'.$unit_display.'</td>
		<td align="right">'.$total_stock.'</td>';
		if($row["stock_available"] > $row["min_stock_quantity"])
		{
			$output .= ' <td align="right"></td>';
		}
		else
			$output .= ' <td align="right">Stock is Less Than Minimum! Kindly Place the Order!!!</td>';
		$output .= '</tr>';

				
	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b>'.number_format($ototal_purchase,2).'</b></td>
		<td align="right"><b>'.number_format($ototal_sales,2).'</b></td>
		<td align="right"><b>'.number_format($ounit_display,2).'</b></td>
		<td align="right"><b>'.number_format($ototal_stock,2).'</b></td>
	</tr></table></div>
	';
	return $output;
}

function count_total_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
	WHERE inventory_order_status='active'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_cash_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
	WHERE payment_status = 'cash' 
	AND inventory_order_status='active'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_credit_order_value($connect)
{
	$query = "
	SELECT sum(inventory_order_total) as total_order_value FROM inventory_order WHERE payment_status = 'credit' AND inventory_order_status='active'
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function get_user_wise_total_order($connect)
{
	$query = '
	SELECT sum(inventory_order.inventory_order_total) as order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, 
	user_details.user_name 
	FROM inventory_order 
	INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
	WHERE inventory_order.inventory_order_status = "active" GROUP BY inventory_order.user_id
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>User Name</th>
				<th>Total Order Value</th>
				<th>Total Cash Order</th>
				<th>Total Credit Order</th>
			</tr>
	';

	$total_order = 0;
	$total_cash_order = 0;
	$total_credit_order = 0;
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['user_name'].'</td>
			<td align="right">$ '.$row["order_total"].'</td>
			<td align="right">$ '.$row["cash_order_total"].'</td>
			<td align="right">$ '.$row["credit_order_total"].'</td>
		</tr>
		';

		$total_order = $total_order + $row["order_total"];
		$total_cash_order = $total_cash_order + $row["cash_order_total"];
		$total_credit_order = $total_credit_order + $row["credit_order_total"];
	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b>$ '.$total_order.'</b></td>
		<td align="right"><b>$ '.$total_cash_order.'</b></td>
		<td align="right"><b>$ '.$total_credit_order.'</b></td>
	</tr></table></div>
	';
	return $output;
}

?>