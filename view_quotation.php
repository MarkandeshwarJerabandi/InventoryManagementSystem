<?php
//view_sales.php
$order_id=$_GET['order_id'];
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
	function convertToIndianCurrency($number) {
		$no = round($number);
		$decimal = round($number - ($no = floor($number)), 2) * 100;    
		$digits_length = strlen($no);    
		$i = 0;
		$str = array();
		$words = array(
			0 => '',
			1 => 'One',
			2 => 'Two',
			3 => 'Three',
			4 => 'Four',
			5 => 'Five',
			6 => 'Six',
			7 => 'Seven',
			8 => 'Eight',
			9 => 'Nine',
			10 => 'Ten',
			11 => 'Eleven',
			12 => 'Twelve',
			13 => 'Thirteen',
			14 => 'Fourteen',
			15 => 'Fifteen',
			16 => 'Sixteen',
			17 => 'Seventeen',
			18 => 'Eighteen',
			19 => 'Nineteen',
			20 => 'Twenty',
			30 => 'Thirty',
			40 => 'Forty',
			50 => 'Fifty',
			60 => 'Sixty',
			70 => 'Seventy',
			80 => 'Eighty',
			90 => 'Ninety');
		$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
		while ($i < $digits_length) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += $divider == 10 ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;            
				$str [] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural;
			} else {
				$str [] = null;
			}  
		}
		
		$Rupees = implode(' ', array_reverse($str));
		$paise = ($decimal) ? "And Paise " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10])  : '';
		return ($Rupees ? 'Rupees ' . $Rupees : '') . $paise . " Only";
	}
	$output = '';

	$statement1 = $connect->prepare("
		SELECT * FROM company_profile 
		LIMIT 1
	");
	$statement1->execute();
	$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
	
	$statement = $connect->prepare("
		SELECT * FROM qinventory_order 
		INNER JOIN customer_details ON customer_details.customer_id = qinventory_order.customer_id
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
		
		$output .= '<div class="modal-body">
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="salesbill" class="table table-bordered table-striped">
						<tr>
							<td colspan="9">
								<img src="quotation_header1.png" alt="Header Image" width="725" height="175"></img>
							</td>
						</tr>	
						<tr>
							<th colspan="4" style="border-margin-left:10px;font-size:20px;">Quotation No:'.$row['inventory_order_id'].'</th>
							<th colspan="5" align="right" style="border-margin-right:20px;font-size:20px;">Quotation Date:'.$row['inventory_order_date'].'</th>
						</tr>
						<tr>
							<td colspan="5">
							Details of Receiver(Buyer Detail),<br/>
							     <span id="customer_name" style="font-size:15px;"><strong>'.$row['firm_name'].'<strong></span><br/>
							     Contact Person:'.$row['customer_name'].'<br/>
								 '.$row['address'].'-'.$row['zipcode'].'<br/>
							     GSTN:'.$row['GSTIN'].'<br/>
							     Mobile No:'.$row['contact_no'].'<br/>
							</td>
							<td colspan="4" align="left">
							Details of Consignee(Shipped Details),<br/>
							     <span id="supplier_name" style="font-size:15px;">'.$row["place"].'</span>
							</td>
						</tr>
						</table>
						<table width="100%" border="1" cellpadding="5" cellspacing="0">
							<caption>Quotation Details</caption>
							<tr align="center">
								<th rowspan="1" style="font-size:10px;" width="5%">SrNo</th>
								<th rowspan="1" style="font-size:10px;" width="8%">HSN Code</th>
								<th rowspan="1" style="font-size:10px;" width="29%">Description</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Size</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Grade</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Quantity</th>
								<th colspan="1" style="font-size:10px;" width="10%">Unit</th>
								<th colspan="1" style="font-size:10px;" width="10%">Rate</th>
								<th rowspan="1" style="font-size:10px;" width="10%">Amount</th>
							</tr>';
						$pstatement = $connect->prepare("
							SELECT * FROM qinventory_order_product 
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
						$total_quantity = 0;
						foreach($product_result as $sub_row)
						{
							//echo "im here";
							$count = $count + 1;
							$product_data = fetch_product_details($sub_row['product_id'], $connect);
							
							$HSN_code = $product_data['HSN_code'];
							$size = $product_data['size'];
							$grade = $product_data['grade'];
							
							//$x = number_format(((($sub_row["price"]+$sub_row["tax"])/(100+$SGST+$CGST))*100),2);	// correction to be done
							$actual_amount = $sub_row["quantity"] * $sub_row["price"]; //$x;
							$total_quantity += $sub_row["quantity"];
							$total_actual_amount = round(($total_actual_amount + $actual_amount),2);
						$output .=	'<tr align="center" style="font-size:14px;">
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
						$sub_total = $total_actual_amount;
						$grand_total = round($sub_total,0);
			$output .=	'
						<tr>
							<th align="center" colspan="5">Total</th>
							<th align="center">'.$total_quantity.'</th>
							<th colspan="2"></th>
							<th align="right">'.$total_actual_amount.'</th>
						</tr>
						
						<tr>
							<td rowspan="1" colspan="5" style="font-size:14px;">Amount in Words:<strong>'.convertToIndianCurrency($grand_total).'</strong></td>
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
							<th colspan="3" align="right" style="font-size:15px;"></th>
							<th colspan="1" align="right"></th>
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