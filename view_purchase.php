
<?php
//header("Content-type: text/css; charset: UTF-8");
//view_sales.php
$purchase_id=$_GET['purchase_id'];
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
if(isset($purchase_id))
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
		$paise = ($decimal) ? "And  " . ($words[$decimal - $decimal%10]) ." " .($words[$decimal%10])  : '';
		return ($Rupees ? 'Rupees ' . $Rupees : '') . $paise . " Paise Only";
	}
	$output = '';

	$statement1 = $connect->prepare("
		SELECT * FROM company_profile 
		LIMIT 1
	");
	$statement1->execute();
	$result1 = $statement1->fetch(PDO::FETCH_ASSOC);
	
	$statement = $connect->prepare("
		SELECT *
		FROM purchase_invoice 
		INNER JOIN supplier_details ON supplier_details.supplier_id = purchase_invoice.supplier_id
		INNER JOIN purchase_details ON purchase_details.purchase_id = purchase_invoice.purchase_id
		WHERE purchase_invoice.purchase_id = :purchase_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':purchase_id'       =>  $purchase_id	// $_GET["order_id"]
		)
	) or die("error in query" . print_r($statement->errorInfo()));
	$result = $statement->fetchAll();
	
	foreach($result as $row)
	{
		
		$output .= '
					<link rel="stylesheet" href="css/datepicker.css">
					<script src="js/bootstrap-datepicker1.js"></script>
					<link rel="stylesheet" href="css/bootstrap-select.min.css">
					<script src="js/bootstrap-select.min.js"></script>

					<script>
					</script>

					<style type="text/css">
					@media screen and (min-width: 768px) {
						.modal-dialog {
						  width: 700px; /* New width for default modal */
						}
						.modal-sm {
						  width: 350px; /* New width for small modal */
						}
					}
					@media screen and (min-width: 992px) {
						.modal-lg {
						  width: 950px; /* New width for large modal */
						}
					}
					</style>
					<div class="modal-body">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th colspan="7" style="text-align:center;">PURCHASE INVOICE<pre style="text-align:right;font-size:10px;"">Original</pre></th>
					</tr>
					</table>
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="purchasebill" class="table table-bordered table-striped">
						<tr style="text-align:center;">
							<td colspan="10" style="font-size:12px;">
								<strong style="font-size:25px;">'.$result1['company_name'].'</strong><br/>
								'.$result1['address'].'
							</td>
						</tr>
						<tr>
							<th style="text-align:left;" style="font-size:8px;" colspan="10">
								<div class="row">
									<div class="form-group">
										<pre>  Phone:'.$result1['contact_no'].'/'.$result1['alt_contact_no'].'
											           GSTIN:'.$result1['GSTIN'].'</pre>
									</div>
								</div>
							</th>
						</tr>
						<tr style="font-size:13px;">
							<td colspan="1" align="center">
										Season Year:
							</td>
							<th colspan="1" align="center">
								
											'.$row['season_year'].'
							</th>
							<td colspan="1" align="center">
										Sugar Cane Variety:
							</td>
							<th colspan="1" align="center">
								
											'.$row['sugar_cane_variety'].'
							</th>
							<td colspan="1" align="center">
										Invoice Number:
							</td>
							<th colspan="1" align="center">
								
											'.$row['invoice_cash_bill_no'].'
							</th>
							<td colspan="2" align="center">
										Date of Purchase:
							</td>
							<th colspan="2" align="center">
								
											'.$row['date_of_purchase'].'
							</th>
						</tr>
						<tr style="font-size:14px;">
							<td colspan="2" align="center">
										Farmer Name:
							</td>
							<th colspan="2" align="center">
								
											'.$row['firm_name'].'
							</th>
							<td colspan="2" align="center">
										Harvester Code and Name:
							</td>
							<th colspan="4" align="center">
								
											'.$row['harvester_name'].'
							</th>
						</tr>
						<tr style="font-size:14px;">
							<td colspan="2" align="center">
										Vehicle Owner Name:
							</td>
							<th colspan="2" align="center">
								
											'.$row['vehicle_owner_name'].'
							</th>
							<td colspan="2" align="center">
										Vehicle No:
							</td>
							<th colspan="4" align="center">
								
											'.$row['vehicle_no'].'
							</th>
						</tr>
					</table>
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="purchasebill" class="table table-bordered table-striped">
						<tr align="center" style="font-size:16px;">
							<th colspan="8">VEHICLE LOADED DETAILS</th>
						</tr>
						<tr style="text-align:center;font-size:14px;">
							
							<th colspan="3">WEIGHT</th>
							<th colspan="5">In TONS</th>
							
						</tr>
						<tr style="text-align:center;font-size:12px;">
							
							<td colspan="3">LOADED</td>
							<th colspan="5">'.number_format($row['loaded_weight'],2).'</th>
							
						</tr>
    					<tr style="text-align:center;font-size:12px;">
							
							<td colspan="3">EMPTY</td>
							<th colspan="5">'.number_format($row['empty_weight'],2).'</th>
							
						</tr>
						<tr style="text-align:center;font-size:12px;">
							
							<td colspan="3">GROSS WEIGHT</td>
							<th colspan="5">'.number_format($row['gross_weight'],2).'</th>
							
						</tr>
						<tr style="text-align:center;font-size:12px;">
							
							<td colspan="3">DEDUCTION</td>
							<th colspan="5">'.number_format($row['deduction'],2).'</th>
							
						</tr>
						<tr style="text-align:center;font-size:12px;">
							
							<td colspan="3">NET WEIGHT</td>
							<th colspan="5">'.number_format($row['net_weight'],2).'</th>
							
						</tr>
					</table>
					<table width="100%" border="1" cellpadding="0" cellspacing="0" id="purchasebill" class="table table-bordered table-striped">
					<tr style="font-size:12px;">
							<td colspan="1" align="center">
										Rate per Ton:
							</td>
							<th colspan="1" align="center" style="font-size:14px;">
								
											'.number_format($row['rate_per_ton'],2).'
							</th>
							<td colspan="1" align="center">
										Total Bill Amount:
							</td>
							<th colspan="1" align="center" style="font-size:14px;">
								
											'.number_format($row['bill_amount'],2).'
							</th>
							<td colspan="1" align="center" >
										Advance Paid:
							</td>
							<th colspan="1" align="center" style="font-size:14px;">
								
											'.number_format($row['advance_paid'],2).'
							</th>
							<td colspan="2" align="center">
										Balance Amount:
							</td>
							<th colspan="2" align="center" style="font-size:14px;">
								
											'.number_format($row['balance_amount'],2).'
							</th>
						</tr>
						<tr  rowspan="2" align="center">
							<th colspan="4"><br/><br/>Loaded Vehicle Signed By</th>
							<th colspan="6"><br/><br/>EMPTY Vehicle Signed By</th>
						</tr>
					
					</table>
				</div>';
	}
	
	
	$pdf = new Pdf('P','mm','A4');
	$file_name = 'PurchaseInvoice-'.$purchase_id.'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}
?>