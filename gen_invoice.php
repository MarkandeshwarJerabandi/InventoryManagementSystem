
<?php
//view_sales.php
//$order_id=252;
include('database_connection.php');
include('function.php');
require_once 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;
class Pdf extends Dompdf{
	public function __construct(){
		parent::__construct();
	}
}
if(isset($order_id))
{
	
	$output = '';

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
			':inventory_order_id'       =>  $order_id	// $_GET["order_id"]
		)
	);
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$bill_type = $row['bill_type'];

	
		$output .= '<div class="modal-body">
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="salesbill" class="table table-bordered table-striped">
						<tr>
							<td colspan="9">
								<img src="invoice_header1.png" alt="Header Image" width="725" height="175"></img>
							</td>
						</tr>	
						<tr>
							<th colspan="4" style="border-margin-left:10px;font-size:20px;">Invoice No:'.$row['inventory_order_id'].'</th>
							<th colspan="5" align="right" style="border-margin-right:20px;font-size:20px;">Invoice Date:'.$row['inventory_order_date'].'</th>
						</tr>
						<tr>
							<td colspan="5">
							To,<br/>
							     <span id="customer_name" style="font-size:15px;"><strong>'.$row['firm_name'].'<strong></span><br/>
							     Contact Person:'.$row['customer_name'].'<br/>
								 '.$row['address'].'<br/>
							     '.$row['place'].'-'.$row['zipcode'].'<br/>
							     GSTN:'.$row['GSTIN'].'<br/>
							     Mobile No:'.$row['contact_no'].'<br/>
							</td>
							<td colspan="4" align="left">
							Supplier Address,<br/>
							     <span id="supplier_name" style="font-size:15px;"><strong>Shri Venkateshwar Textiles<strong></span><br/>
							     Near Ishwar Temple,Subhas Road<br/>
							     Turnel Peth<br/>
								 Betageri-Gadag-582102<br/>
							     GSTN:29BVJPJ0500P1ZD<br/>
							     Mobile No:9986025215<br/>
							</td>
						</tr>
						</table>
						<table width="100%" border="1" cellpadding="5" cellspacing="0">
							<caption>Sale Order Details</caption>
							<tr align="center">
								<th rowspan="1" style="font-size:10px;" width="5%">SrNo</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Code</th>
								<th rowspan="1" style="font-size:10px;" width="29%">Description</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Size</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Grade</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Quantity</th>
								<th colspan="1" style="font-size:10px;" width="10%">Unit</th>
								<th colspan="1" style="font-size:10px;" width="10%">Rate</th>
								<th rowspan="1" style="font-size:10px;" width="10%">Amount</th>
							</tr>';
						$pstatement = $connect->prepare("
							SELECT * FROM inventory_order_product 
							WHERE inventory_order_id = :inventory_order_id
						");
						$pstatement->execute(
							array(
								':inventory_order_id'       => $order_id
							)
						);
						$product_result = $pstatement->fetchAll();
						$count = 0;
						$total = 0;
						$total_actual_amount = 0;
						$total_tax_amount = 0;
						$total_SGST =0;
						$total_CGST =0;
						$pdiscount = $row['pdiscount'];
						foreach($product_result as $sub_row)
						{
							//echo "im here";
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
							$HSN_code = $product_data['HSN_code'];
							$size = $product_data['size'];
							$grade = $product_data['grade'];
							
							$x = number_format(((($sub_row["price"]+$sub_row["tax"])/(100+$SGST+$CGST))*100),2);	// correction to be done
							$actual_amount = $sub_row["quantity"] * $sub_row["price"]; //$x;
							$SGST_amount = ($actual_amount * $SGST)/100; //number_format((($sub_row["quantity"] * $sub_row["tax"])/2),2); //($actual_amount * $SGST)/100;
							$CGST_amount = ($actual_amount * $SGST)/100; //number_format((($sub_row["quantity"] * $sub_row["tax"])/2),2);
							$tax_amount = $SGST_amount + $CGST_amount;
							$total_SGST += $SGST_amount;
							$total_CGST += $CGST_amount;
							//$total_product_amount = round(($actual_amount + $SGST_amount + $CGST_amount),2);
							$total_actual_amount = round(($total_actual_amount + $actual_amount),2);
							//$total_tax_amount = round(($total_tax_amount + $tax_amount),2);
							//$total = round(($total + $total_product_amount),2);
							
						
						
						$output .=	'<tr align="center">
										<td>'.$count.'</td>
										<td>'.$HSN_code.'</td>
										<td align="justify">'.$product_data["product_name"]." ".$product_data["category_name"].'</td>
										<td>'.$size.'</td>
										<td>'.$grade.'</td>
										<td>'.$sub_row["quantity"].'</td>
										<td>'.$sub_row["sale_uom"].'</td>
										<td>'.$sub_row["price"].'</td>
										<td align="right">'.$actual_amount.'</td>
									</tr>';
						}
						$discount_amount = round((($pdiscount * $total_actual_amount)/100),2);
						$sub_total = $total_actual_amount - $discount_amount;
						$total_tax_amount = round((($sub_total * ($SGST+$CGST))/100),2);
						$grand_total = round(($sub_total + $total_tax_amount),0);
			$output .=	'
						<tr>
							<th rowspan="2" colspan="5" style="font-size:14px;">Amount in Words: </th>
							<th colspan="3" align="right" style="font-size:15px;">Discount :'.$pdiscount.'%</th>
							<th colspan="1" align="right">'.$discount_amount.'</th>
						</tr>
						<tr>
							
							<th colspan="3" align="right" style="font-size:15px;">Sub Total</th>
							<th colspan="1" align="right">'.$sub_total.'</th>
						</tr>
						<tr>
							<td rowspan="2" colspan="5" style="font-size:14px;"><strong>Bank Address and Details</strong><br/>
							Bank Name:<strong>'.$result1["bank_name"].'</strong><br/>
							Account Name:<strong>'.$result1["name_of_the_account"] .'</strong><br/>
							Account Number:<strong>'.$result1["account_no"] .'</strong>	<br/>
							IFSC Code:<strong>'.$result1["IFSC"] .'</strong>	<br/>
							Branch:<strong>'.$result1["branch"] .'</strong>	<br/>
							
							</td>
							<th colspan="3" align="right" style="font-size:15px;">Tax '.($SGST+$CGST).'%</th>
							<th colspan="1" align="right">'.$total_tax_amount.'</th>
						</tr>
						<tr>
							
							<th colspan="3" align="right" style="font-size:15px;">Grand Total</th>
							<th colspan="1" align="right">'.$grand_total.'</th>
						</tr>
						
						<tr >
							<th colspan="5" style="font-size:12px;">Conditions <br/>
							1) Goods Once Supplied Will Not Be Taken Back Or Exchanged<br/>
							2) Our Responsibility Ceases on Delivery At Factory</th>
							<th colspan="4" align="left" style="font-size:15px;">For,</th>
							
						</tr>
					</table>
				</div>';
	}			
	$pdf = new Pdf('P','mm','A4');
	$file_name = 'Invoice-'.$row["inventory_order_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}
?>