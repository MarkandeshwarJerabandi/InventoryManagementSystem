<?php

//purchase_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	
	
	
	if($_POST['btn_action'] == 'recalculate')
	{
			$SGST_tax =0;
			$CGST_tax =0;
			$total_amount=0;
			$output=array();
			// for($count = 0; $count<count($_POST["product_id"]); $count++)
			// {
				// $product_details = fetch_product_details($_POST["product_id"][$count], $connect);
				// $total_items_in_pieces = 0;
				// $SGST = $product_details['SGST'];
				// $CGST = $product_details['CGST'];
				// $base_price = $_POST["unit_cost"][$count];
				// $quantity =	$_POST["quantity"][$count];
				// $SGST_tax += round(($base_price * $SGST)/100,2);
				// $CGST_tax += round(($base_price * $CGST)/100,2);
				// $total_amount += round(($base_price * $quantity),2);
			// }
			// $grand_total = $total_amount + $SGST_tax + $CGST_tax;
			$output['bill_amount'] = '1';
			echo json_encode($output);
	}
}

?>