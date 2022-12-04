<?php
//header("Content-type: text/css; charset: UTF-8");
//view_sales.php
$order_id=$_GET['order_id'];
$invoice_id = $_GET['invoice_id'];
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

	
		$output .= '
					<div class="modal-body">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th colspan="7" style="text-align:center;">TAX INVOICE<pre style="text-align:right;">Original</pre></th>
					</tr>
					</table>
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="salesbill" class="table table-bordered table-striped">
						<tr>
							<td colspan="2" rowspan="4">
								<strong style="font-size:25px;">'.$result1['company_name'].'</strong><br/>
								'.$result1['address'].'<br/>
								Phone:'.$result1['contact_no'].'/'.$result1['alt_contact_no'].'<br/>
								GSTIN:'.$result1['GSTIN'].'<br/>
								Pan No:		<br/><br/>				
							</td>
							<td colspan="2" style="font-size:16px;">Invoice No:	<strong>'.$row['invoice_no'].'</strong></td>
							<td colspan="2" style="font-size:16px;">Dated:<strong>'.$row['inventory_order_date'].'</strong></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size:16px;">Order No:<strong>'.$row['order_no'].'</strong></td>
							<td colspan="2" style="font-size:16px;">Order Date:	<strong>'.$row['order_date'].'</strong></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size:14px;">Truck No:	<strong>'.$row['truck_no'].'</strong></td>
							<td colspan="2" style="font-size:14px;">Mode/Terms of Payment<br/><b style="text-align:right;">'.$row['payment_status'].'</b></td>
						</tr>
						<tr>
							<td colspan="2" style="font-size:14px;">Dispatch Document No:<strong>'.$row['dispatch_no'].'</strong></td>
							<td colspan="2" style="font-size:14px;">Dated:<strong>'.$row['inventory_order_date'].'</strong></td>
						</tr>						
						<tr>
							<td colspan="2" rowspan="3">
							<strong>Buyer</strong><br/>
							     <span id="customer_name" style="font-size:25px;"><strong>'.$row['firm_name'].'<strong></span><br/>
							     '.$row['customer_name'].'<br/>
								 '.$row['address'].'-'.$row['zipcode'].'<br/>
							     GSTN:'.$row['GSTIN'].'<br/>
							     Mobile No:'.$row['contact_no'].'
							</td>
							<td colspan="2" style="font-size:14px;">Dispatch Through:<strong>'.$row['dispatch_through'].'</strong></td>
							<td colspan="2" style="font-size:14px;">Delivery Station:<strong>'.$row['place'].'</strong></td>
						</tr>						
						<tr>
							<td colspan="4" align="left" style="font-size:14px;">
							<strong>Delivery Address</strong><br/>
							     '.$row['delivery_address'].'
							</td>
						</tr>
						<tr>
							<td colspan="4" align="left" style="font-size:14px;">Broker:<strong>'.$row['broker'].'</strong></td>
						</tr>
						</table>
						<table width="100%" border="1" cellpadding="5" cellspacing="0">
							<caption>Sale Order Details</caption>
							<tr align="center">
								<th rowspan="1" style="font-size:10px;" width="5%">SrNo</th>
								<th colspan="3"rowspan="1" style="font-size:10px;" width="29%">Description of Goods</th>
								<th rowspan="1" style="font-size:10px;" width="8%">HSN Code</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Quantity</th>
								<th rowspan="1" style="font-size:10px;" width="8%">Unit of Measurement</th>
								<th colspan="1" style="font-size:10px;" width="10%">Rate</th>
								<th colspan="1" style="font-size:10px;" width="10%">GST Rate</th>
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
						$Total_Amount=0;
						$total_quantity = 0;
						$total_weight = 0;
						$pdiscount = $row['pdiscount'];
						$assisable_value = array();
						$aHSN_code = array();
						$tax_HSN_code = array();
						$taxvalue_HSN_code = array();
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
							$GST_rate=$SGST+$CGST;
							$HSN_code = $product_data['HSN_code'];
							$aHSN_code[$count] = $HSN_code;
							//$size = $product_data['size'];
							$uom = $sub_row["sale_uom"];
							//$grade = $product_data['grade'];
							if($uom == "Box")
								$size = 18;
							else
								$size =1;
							
							
							//$x = number_format(((($sub_row["price"]+$sub_row["tax"])/(100+$SGST+$CGST))*100),2);	// correction to be done
							$actual_amount = $sub_row["quantity"] * $size * $sub_row["price"]; //$x;
							if(array_key_exists($HSN_code,$assisable_value))
								$assisable_value[$HSN_code] += $actual_amount;
							else
								$assisable_value[$HSN_code] = $actual_amount;
							if(!array_key_exists($HSN_code,$tax_HSN_code))
								$tax_HSN_code[$HSN_code] = array($SGST,$CGST);
							
							$SGST_amount = ($actual_amount * $SGST)/100; //number_format((($sub_row["quantity"] * $sub_row["tax"])/2),2); //($actual_amount * $SGST)/100;
							$CGST_amount = ($actual_amount * $SGST)/100; //number_format((($sub_row["quantity"] * $sub_row["tax"])/2),2);
							//$GST = $SGST_amount+$CGST_amount;
							$tax_amount = $SGST_amount + $CGST_amount;
							$total_SGST = $total_SGST+$SGST_amount;
							$total_CGST = $total_CGST+$CGST_amount;
							//if(array_key_exists($HSN_code,$taxvalue_HSN_code))
								$taxvalue_HSN_code[$HSN_code] = array($total_SGST,$total_CGST);
							//else
								//$taxvalue_HSN_code[$HSN_code] = array($total_SGST,$total_CGST);
							
							//$Total_Amount = $actual_amount + $tax_amount; //number_format(($actual_amount + $tax_amount),2); 
							$Total_Amount += $actual_amount+$total_CGST+$total_SGST;
							$total_quantity += $sub_row["quantity"];
							$total_weight += $size * $sub_row["quantity"];
							//$total_product_amount = round(($actual_amount + $SGST_amount + $CGST_amount),2);
							
							
							//$total_actual_amount = round(($total_actual_amount + $actual_amount),2);
							$total_actual_amount = round(($total_actual_amount + $actual_amount),2);
							
							//$total_tax_amount = round(($total_tax_amount + $tax_amount),2);
							//$total = round(($total + $total_product_amount),2);
							
						
						
						$output .=	'<tr align="center" style="font-size:14px;">
										<td>'.$count.'</td>
										<td colspan="3" align="justify">'.$product_data["product_name"].'</td>
										<td>'.$HSN_code.'</td>
										<td>'.$sub_row["quantity"].'</td>
										<td>'.$uom.'</td>
										<td>'.number_format($sub_row["price"],2).'</td>
										<td>'.$GST_rate.'%</td>
										<td align="right">'.number_format($actual_amount,2).'</td>
									</tr>';
						}
						$discount_amount = ($pdiscount * ($total_actual_amount+$total_CGST+$total_SGST))/100;
						$sub_total = $total_actual_amount+$total_CGST+$total_SGST - $discount_amount;
						//$total_tax_amount = ($sub_total * ($SGST+$CGST))/100;
						//print($total_tax_amount);
						$grand_total = round(($sub_total + $total_tax_amount),2);
			$output .=	'
						<tr>
							<th colspan="4"></th>
							<th align="right">Total</th>
							<th align="center">'.$total_quantity.'</th>
							<th align="center"></th>
							<th colspan="2" align="right">Total</th>
							<th align="right">'.number_format($total_actual_amount,2).'</th>
						</tr>
						<tr>
							<td rowspan="2" colspan="5" style="font-size:14px;"><strong>Other Charges</strong></td>
							<td colspan="5" align="right" style="font-size:15px;">Other Charges:<b><span align="right">'.$discount_amount.'</span></b><br/>
							CGST TAX: <b><span align="right">'.$total_CGST.'</span></b><br/>
							SGST TAX: <b><span align="right">'.$total_SGST.'</span></b>
							</td>
						</tr>
						<tr>
							<th colspan="4" align="right" style="font-size:15px;">NET Amount</th>
							<th colspan="1" align="right">'.number_format($grand_total,2).'</th>
						</tr>
						<tr>
							<td rowspan="1" colspan="5" style="font-size:14px;">Amount in Words:<strong>'.convertToIndianCurrency($grand_total).'</strong></td>
							<td colspan="5" rowspan="2">
								<table width="100%" border="1">
									<tr align="center" style="font-size:12px;">
										<th>HSN Code</th>
										<th>TAX Discription</th>
										<th>Assessable Value</th>
										<th>CGST Value</th>
										<th>SGST Value</th>
									</tr>';
									//for($i=1;$i<=count($aHSN_code);$i++)
									foreach($assisable_value as $key => $value)
									{
										$output.='<tr style="font-size:12px;">
										<th align="center">'.$key.'</th>
										<th align="center" style="font-size:12px;">SGST '.$tax_HSN_code[$key][0].'% + CGST '.$tax_HSN_code[$key][1].'%</th>
										<th align="center">'.$value.'</th>
										<th align="center">'.$taxvalue_HSN_code[$key][0].'</th>
										<th align="center">'.$taxvalue_HSN_code[$key][1].'</th>
									</tr>';
									}
									
								$output .='</table>
							</td>
						</tr>
						
						<tr>
							<td rowspan="1" colspan="5" style="font-size:14px;"><strong>Our Bankers</strong><br/>
							Bank Name:<strong>'.$result1["bank_name"].'</strong><br/>
							Account Name:<strong>'.$result1["name_of_the_account"] .'</strong><br/>
							Account Number:<strong>'.$result1["account_no"] .'</strong>	<br/>
							IFSC Code:<strong>'.$result1["IFSC"] .'</strong>	<br/>
							Branch:<strong>'.$result1["branch"] .'</strong>	<br/><br/>
							
							</td>
						</tr>
						
						<tr >
							<th colspan="5" style="font-size:12px;" align="justify">Terms <br/>
							1) Goods Once Supplied Will Not Be Taken Back Or Exchanged<br/>
							2) Payment Should be by NEFT/RTGS/Cheque<br/>
							3) Interest 12% p.a will be charged if payment is not made before due date<br/>
							4) Transportation is extra as per actual paid by the purchaser<br/>
							5) This is subject to Vijaypur Jurisdiction</th>
							<th colspan="5" align="right" style="font-size:15px;">For M/s Shree Mahalaxmi Jaggery<br/>
							<br/>
							<br/>
							<br/>
							Authorized Signatory</th>
							
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