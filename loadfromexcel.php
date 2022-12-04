<html>
<body>
<form name="import" method="post" enctype="multipart/form-data">
    	<input type="file" name="file" /><br />
        <input type="submit" name="submit" value="Submit" />
</form>


<?php
ini_set("display_errors",1);
require_once 'excel_reader2.php';
require_once 'db.php';

$file = $_FILES['file']['tmp_name'];

$data = new Spreadsheet_Excel_Reader($file);

echo "Total Sheets in this xls file: ".count($data->sheets)."<br /><br />";

$html="<table border='1'>";
for($i=0;$i<count($data->sheets);$i++) // Loop to get all sheets in a file.
{	
	if(count($data->sheets[$i][cells])>0) // checking sheet not empty
	{
		echo "Sheet $i:<br /><br />Total rows in sheet $i  ".count($data->sheets[$i][cells])."<br />";
		for($j=3;$j<=count($data->sheets[$i][cells]);$j++) // loop used to get each row of the sheet
		{ 
			$html.="<tr>";
			for($k=1;$k<=count($data->sheets[$i][cells][$j]);$k++) // This loop is created to get data in a table format.
			{
				$html.="<td>";
				$html.=$data->sheets[$i][cells][$j][$k];
				$html.="</td>";
			}
			$data->sheets[$i][cells][$j][1];
			$ID = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][1]);
			$stock_available = mysqli_real_escape_string($connection,$data->sheets[$i][cells][$j][16]);
			
			
			$query = "UPDATE stock_details SET 
						total_purchase_quantity = '".$stock_available."',
						stock_available = '".$stock_available."'
						where product_id='".$ID."'
					";
			
			$insertq=mysqli_query($connection,$query);
			if($insertq)
				$msg="Row UPDATED";
			else
					$msg="Error UPDATING";
			
			$html.="</tr>";
		}
	}
	
}

$html.="</table>";
echo $html;
echo $msg;
?>

</body>

</html>