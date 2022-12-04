<?php

//update_chips_stock.php

include('database_connection.php');
$query = "
	select * from
	stock_details, product, supplier_details,category
	where stock_details.product_id = product.product_id and product.supplier_id = supplier_details.supplier_id
	and product.category_id=category.category_id
	and category.category_id = 3
	order by(stock_details.product_id)
";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();

echo $filtered_rows . "</br>";
$i=0;
$today = date("Y-m-d");

foreach($result as $row)
{
		$sales=0;
		$sales_amount =0;
		echo $row['product_id'] . " =>";
		$sales_query = 'select * from inventory_order_product, inventory_order
						  where inventory_order_product.inventory_order_id=inventory_order.inventory_order_id
						  and inventory_order_product.product_id="'.$row['product_id'].'"
						  and inventory_order.inventory_order_date <= "'.$today.'"
						  ';
		$statement2 = $connect->prepare($sales_query);
		$statement2->execute();
		$result2 = $statement2->fetchAll();
		$filtered_rows2 = $statement2->rowCount();
		if($filtered_rows2>0)
		{
			foreach($result2 as $row2)
			{
				if($row2['sale_uom']!="Dozens")
					$sales += $row2['quantity'];
				else
				{
			/*		$p_query = 'select * from product
								where product_id="'.$row['product_id'].'"
								';
					$statement3 = $connect->prepare($p_query);
					$statement3->execute();
					$result3 = $statement3->fetchAll();
					$filtered_rows3 = $statement3->rowCount();
					if($filtered_rows3>0)
					{
						foreach ($result3 as $row3)
						{	*/
							$sales += round(($row2['quantity']/$row['unit_conversion']),2);
							
							
					//	}	
				//	}
				}
				$sales_amount += round(($row2['quantity'] * ($row2['price']+$row2['tax'])),2);
			}
			$u_query = "
						update stock_details
						SET
							total_sales_quantity = '".$sales."',
							stock_available = round((total_purchase_quantity - '".$sales."'),2)
						where
							product_id = '".$row['product_id']."'
						";
			$statement1 = $connect->prepare($u_query);
			$statement1->execute();
			$result = $statement1->fetchAll();
			echo $sales . "</br>";
		}
}

?>
