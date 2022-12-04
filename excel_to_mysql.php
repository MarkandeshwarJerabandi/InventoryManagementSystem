<?php
$conn = mysqli_connect("localhost","root","","ps");
require_once('vendor/php-excel-reader/excel_reader2.php');
require_once('vendor/SpreadsheetReader.php');

if (isset($_POST["import"]))
{
       
  $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
  
  if(in_array($_FILES["file"]["type"],$allowedFileType)){

        $targetPath = '/'.$_FILES['file']['name'];
        move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
        
        $Reader = new SpreadsheetReader($targetPath);
        
        $sheetCount = count($Reader->sheets());
        
        for($i=0;$i<$sheetCount;$i++)
        {
            $Reader->ChangeSheet($i);
            
            foreach ($Reader as $Row)
            {
          
                $product_id = "";
                if(isset($Row[0])) {
                    $product_id = mysqli_real_escape_string($conn,$Row[0]);
                }
                
                $init_quantity = "";
                if(isset($Row[3])) {
                    $init_quantity = mysqli_real_escape_string($conn,$Row[3]);
                }
				
				$as_on_date = "";
                if(isset($Row[4])) {
                    $as_on_date = mysqli_real_escape_string($conn,$Row[4]);
                }
                
                if (!empty($product_id) || !empty($init_quantity) || !empty($as_on_date) ) {
                    $query = "
								UPDATE product SET
									init_stock_quantity = '".$init_quantity."',
									as_on_date = '".$as_on_date."'
								where 
									product_id = '".$product_id."'
							";
                    $result = mysqli_query($conn, $query);
                
                    if (! empty($result)) {
                        $type = "success";
                        $message = "Excel Data UPDATED in the Database";
                    } else {
                        $type = "error";
                        $message = "Problem in UPDATING Excel Data";
                    }
                }
             }
        
         }
  }
  else
  { 
        $type = "error";
        $message = "Invalid File Type. Upload Excel File.";
  }
}
?>