<?php
//view_sales.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
	
	<link rel="stylesheet" type="text/css" href="css/datatables.min.css"/>
	<script type="text/javascript" src="js/jquery-3.3.1.js"></script>
	<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
	<script type="text/javascript" src="js/pdfmake.min.js"></script>
	<script type="text/javascript" src="js/vfs_fonts.js"></script>
	<script type="text/javascript" src="js/datatables.min.js"></script>
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
<script type="text/css">
  #salesbill {
    display: block;
  }
}
</script>
<?php
//view_sales.php
if(isset($_GET['order_id']))
{
//	require_once 'pdf.php';
//	include('database_connection.php');
//	include('function.php');
/*	if(!isset($_SESSION['type']))
	{
		header('location:login.php');
	}	*/
	$output = '';
/*	$statement = $connect->prepare("
		SELECT * FROM inventory_order 
		INNER JOIN customer_details ON customer_details.customer_id = inventory_order.customer_id
		WHERE inventory_order_id = :inventory_order_id
		LIMIT 1
	");	*/
	$statement1 = $connect->prepare("
		SELECT * FROM company_profile 
		LIMIT 1
	");
	$statement1->execute();
	$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
	
	$statement = $connect->prepare("
		SELECT * FROM inventory_order 
		INNER JOIN customer_details ON customer_details.customer_id = inventory_order.customer_id
		WHERE inventory_order_id = :inventory_order_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':inventory_order_id'       =>  $_GET["order_id"]
		)
	);
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$bill_type = $row['bill_type'];
//		$output .= '
?>		
	<div class ="print">
		<button class="Button Button--outline" onclick="printDiv()">Print</button>
	</div>
	
	<div class="modal-body">
		<table width="100%" border="1" cellpadding="5" cellspacing="0" id="salesbill" class="table table-bordered table-striped">
			<tr >
				<td colspan="3" width="100%" align="center">
					<table width="100%" border="0" cellpadding="5">
						<tr>
							<td width="45%">
							<?php	echo "GSTIN: " . $result1["GSTIN"]; ?>
							</td>
							<td width="25%">
							<?php echo "	!!Shree!! ";	?>
							</td>
							<td width="30%" align="right">
							<?php	echo "Cell : " .$result1["contact_no"]; ?>
							</td>
						</tr>
					</table>
					<?php echo "	Subject to " . $result1["place"] .  "Jurisidiction<br />"; ?>
					<b style="font-size:25px;" align="center"> <?php echo $result1["company_name"] . "</b><br />" ;
						echo "Distributors For: <b> " .$result1["distributor"] . "<br/>" ;
						echo "<b>" . $result1["address"]. "</b><br />";	?>
			</tr>
			<tr>
				<td colspan="3" align="center" style="font-size:18px"><b>Invoice</b></td>
			</tr>
			<tr>
				<td colspan="3">
				<table width="100%" cellpadding="5">
					<tr>
						<td width="65%">
						<?php	
						echo "To,<br/>";
						echo "	<b>Customer (BILL TO)<br />";
						echo	"Name : ".$row["customer_name"]."</b><br />";
						echo	"Address : ";
						echo	$row["firm_name"] . "<br />";
						echo	$row["address"] . "<br />";
						echo 	"Zipcode: " . $row["zipcode"] . "<br />";
						echo 	"contact Number: " . $row["contact_no"]. "<br />";
						echo 	"GSTIN:" . $row["GSTIN"]. "<br />";
						?>	
						</td>

						<td width="35%">
						<?php	
							echo "<b>Invoid No and Date<br />";
							echo "Invoice No. : " . $row["inventory_order_id"]."<br />";
							echo "Invoice Date : " . $row["inventory_order_date"]."</b><br />";
						?>
						</td>
					</tr>
				</table>
				<br />
				<table width="100%" border="1" cellpadding="5" cellspacing="0">
					<tr align="center">
						<th rowspan="2">Sr No.</th>
						<th rowspan="2">Product</th>
						<th rowspan="2">Quantity</th>
						<th rowspan="2">Price</th>
						<th rowspan="2">Actual Amt.</th>
						<th colspan="2" style="font-size:11px;" align="center">SGST(%)</th>
						<th colspan="2" style="font-size:11px;" align="center">CGST(%)</th>
						<th rowspan="2">Total</th>
					</tr>
					<tr>
						<th align="center">Rate</th>
						<th align="center">Amt.</th>
						<th align="center">Rate</th>
						<th align="center">Amt.</th>
					</tr>
		<?php
		$statement = $connect->prepare("
			SELECT * FROM inventory_order_product 
			WHERE inventory_order_id = :inventory_order_id
		");
		$statement->execute(
			array(
				':inventory_order_id'       =>  $_GET["order_id"]
			)
		);
		$product_result = $statement->fetchAll();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		$total_tax_amount = 0;
		$total_SGST =0;
		$total_CGST =0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$product_data = fetch_product_details($sub_row['product_id'], $connect);
			if($bill_type == 'withGST')
			{
				$SGST = $product_data['SGST'];
				$CGST = $product_data['CGST'];
			}
			else
			{
				$SGST = 0;
				$CGST = 0;
			}
				
			
			$x = number_format(((($sub_row["price"]+$sub_row["tax"])/(100+$SGST+$CGST))*100),2);
			$actual_amount = $sub_row["quantity"] * $x;
			$SGST_amount = ($actual_amount * $SGST)/100;
			$CGST_amount = ($actual_amount * $CGST)/100;
			$tax_amount = $SGST_amount + $CGST_amount;
			$total_SGST += $SGST_amount;
			$total_CGST += $CGST_amount;
			$total_product_amount = $actual_amount + $tax_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total_tax_amount = $total_tax_amount + $tax_amount;
			$total = $total + $total_product_amount;	
		?>
		
				<tr align="center">
					<td><?php echo "$count";?></td>
					<td><?php echo $product_data['product_name'] . " " . $product_data['category_name'] . "</td>"; ?>
					<td><?php echo $sub_row["quantity"] . "</td>"; ?>
					<td aling="right"><?php echo "$x" . "</td>";?>
					<td align="right"><?php echo number_format($actual_amount, 2) . "</td>"?>
					<td><?php echo "$SGST". "%</td>";?>
					<td align="right"><?php echo number_format($SGST_amount, 2) . "</td>";?>
					<td><?php echo "$CGST" . "%</td>";?>
					<td align="right"><?php echo number_format($CGST_amount, 2) ."</td>";?>
					<td align="right"><?php echo number_format($total_product_amount, 2) . "</td>";?>
				</tr>
		<?php
		} 
		?>
		
		<tr>
			<td align="right" colspan="4"><b>Total</b></td>
			<td align="right"><b><?php echo number_format($total_actual_amount, 2). "</b></td>" ;?>
			<td>&nbsp;</td>
			<td align="right"><b><?php echo number_format($total_SGST, 2) ."</b></td>" ;?>
			<td>&nbsp;</td>
			<td align="right"><b><?php echo number_format($total_CGST, 2) ."</b></td>";?>
			<td align="right"><b><?php echo number_format($total, 2)."</b></td>";?>
		</tr>
			
						</table>
						
					</td>
				</tr>
				
						
						<tr>
							<td colspan="1">Company Account Details </td>
							<td colspan="2" width="100%" align="right" style="font-size:12px;">
						<?php echo	"Name of Account: " . $result1["name_of_the_account"] . "<br />"; ?>
						<?php echo	"Account Number: " . $result1["account_no"] . "<br />";?>
						<?php echo	"Bank Name: " . $result1["bank_name"] . "<br />";?>
						<?php echo	"Branch: " . $result1["branch"] . "<br />"; ?>
						<?php echo	"IFSC Code:" . $result1["IFSC"] . "<br />" ;?>
							</td>
						</tr>
						<tr>
							<td colspan="3" width="100%" align="left" style="font-size:16px;"><br/>Goods Received in good condition</td>
						</tr>
						<tr >
							<td colspan="1"   width="40%" align="left" style="font-size:18px;"><br/><br/>Customer's Signature</td>
							<td  colspan="2" width="60%" align="right" style="font-size:18px;"><br/><br/>For, <?php echo $result1["for"] ."</td>"?>
						</tr>
			</table>
	</div>
	<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
		<?php
	}
//	$pdf = new Pdf();
//	$file_name = 'Invoice-'.$row["inventory_order_id"].'.pdf';
//	$pdf->loadHtml($output);
//	$pdf->render();
//	$pdf->stream($file_name, array("Attachment" => false));
}

?>
<script type="text/javascript">

	
    
	function printDiv() {
         window.frames["print_frame"].document.body.innerHTML = document.getElementById("salesbill").innerHTML;
         window.frames["print_frame"].window.focus();
         window.frames["print_frame"].window.print();
    }	
	
	
			
	$(document).ready(function(){
	
		 
			var purchasedataTable = $('#salesbill').DataTable({
			"processing":true,
			"serverSide":true,
			"searching":true,
			"footer":true,
			"dom":'lBfrtrip',
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
			]
			});
    });
</script>