<?php
/*call the FPDF library*/
require('fpdf/fpdf.php');

class PDF_Rotate extends FPDF
{
var $angle=0;

function Rotate($angle, $x = -1, $y = -1) {
    if ($x == -1)
        $x = $this->x;
    if ($y == -1)
        $y = $this->y;
    if ($this->angle != 0)
        $this->_out('Q');
    $this->angle = $angle;
    if ($angle != 0) {
        $angle*=M_PI / 180;
        $c = cos($angle);
        $s = sin($angle);
        $cx = $x * $this->k;
        $cy = ($this->h - $y) * $this->k;
        $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
    }
 }

function _endpage() {
    if ($this->angle != 0) {
        $this->angle = 0;
        $this->_out('Q');
    }
    parent::_endpage();
}
}

class PDF extends PDF_Rotate
{
	function Header() {
    //Put the watermark
    //$this->Image('http://chart.googleapis.com/chart?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World',40,100,100,0,'PNG');
	$this->Image('logo.jpg',50,80,100,0,'JPG');
    $this->SetFont('Arial', 'B', 50);
    $this->SetTextColor(255, 192, 203);
    $this->RotatedText(35, 190, 'Shri Venkateshwar Textiles', 45);
}

function RotatedText($x, $y, $txt, $angle) {
    //Text rotated around its origin
    $this->Rotate($angle, $x, $y);
    $this->Text($x, $y, $txt);
    $this->Rotate(0);
 }
 
 const DPI = 96;
    const MM_IN_INCH = 25.4;
    const A4_HEIGHT = 297;
    const A4_WIDTH = 210;
    // tweak these values (in pixels)
    const MAX_WIDTH = 600;
    const MAX_HEIGHT = 500;

    function pixelsToMM($val) {
        return $val * self::MM_IN_INCH / self::DPI;
    }

    function resizeToFit($imgFilename) {
        list($width, $height) = getimagesize($imgFilename);

        $widthScale = self::MAX_WIDTH / $width;
        $heightScale = self::MAX_HEIGHT / $height;

        $scale = min($widthScale, $heightScale);

        return array(
            round($this->pixelsToMM($scale * $width)),
            round($this->pixelsToMM($scale * $height))
        );
    }

    function centreImage($img) {
        list($width, $height) = $this->resizeToFit($img);

        // you will probably want to swap the width/height
        // around depending on the page's orientation
        /*$this->Image(
            $img, (self::A4_HEIGHT - $width) / 2,
            (self::A4_WIDTH - $height) / 2,
            $width,
            $height
        );*/
		$this->Image(
            $img, 0,
            0,
            $width+52,
            $height
        );
    }

 
}
	
/*A4 width : 219mm*/

$pdf = new PDF('P','mm','A4');

$pdf->AddPage();
/*output the result*/
//$pdf->Image('invoiceheader.PNG',10,0,75,0,'PNG');
list($w, $h) = $pdf->resizeToFit('invoiceheader.PNG');
//echo $w;
$pdf->Cell($w,$h,$pdf->centreImage("invoice_header1.PNG"),0,0);;


/*set font to arial, bold, 14pt*/
$pdf->SetFont('Arial','B',20);

/*Cell(width , height , text , border , end line , [align] )*/


$pdf->SetFont('Arial','B',12);
$pdf->Cell(0 ,0,'Invoice No:',1,0);
$pdf->Cell(0 ,0,'',0,0);
$pdf->Cell(0 ,0,'Invoice Date:',1,1);
$pdf->Line(0, 62, 210-0, 62);

$pdf->SetFont('Arial','B',15);
$pdf->Cell(71 ,5,'To,',0,0);
$pdf->Cell(59 ,5,'',0,0);
$pdf->Cell(59 ,5,'Supplier Details:',0,1);
//$pdf->Line(0, 62, 210-0, 62);





$pdf->Cell(50 ,10,'',0,1);

$pdf->SetFont('Arial','B',10); 
/*Heading Of the table*/
/*
$pdf->Cell(10 ,6,'Sl',1,0,'C');
$pdf->Cell(80 ,6,'Description',1,0,'C');
$pdf->Cell(23 ,6,'Qty',1,0,'C');
$pdf->Cell(30 ,6,'Unit Price',1,0,'C');
$pdf->Cell(20 ,6,'Sales Tax',1,0,'C');
$pdf->Cell(25 ,6,'Total',1,1,'C');/*end of line*/
/*Heading Of the table end*/
/*
$pdf->SetFont('Arial','',10);
    for ($i = 0; $i <= 10; $i++) {
		$pdf->Cell(10 ,6,$i,1,0);
		$pdf->Cell(80 ,6,'HP Laptop',1,0);
		$pdf->Cell(23 ,6,'1',1,0,'R');
		$pdf->Cell(30 ,6,'15000.00',1,0,'R');
		$pdf->Cell(20 ,6,'100.00',1,0,'R');
		$pdf->Cell(25 ,6,'15100.00',1,1,'R');
	}
		

$pdf->Cell(118 ,6,'',0,0);
$pdf->Cell(25 ,6,'Subtotal',0,0);
$pdf->Cell(45 ,6,'151000.00',1,1,'R');

*/
$pdf->Output();

?>