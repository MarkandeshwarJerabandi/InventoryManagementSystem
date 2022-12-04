<h2>Import Excel File into MySQL Database using PHP</h2>
    
    <div class="outer-container">
        <form action="excel_to_mysql.php" method="post"
            name="frmExcelImport" id="frmExcelImport" enctype="multipart/form-data">
            <div>
                <label>Choose Excel
                    File</label> <input type="file" name="file"
                    id="file" accept=".xls,.xlsx">
                <button type="submit" id="submit" name="import"
                    class="btn-submit">Import</button>
        
            </div>
        
        </form>
        
    </div>
    <div id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
    
         
<?php
	$conn = mysqli_connect("localhost","root","","ps");
    $sqlSelect = "SELECT * FROM product";
    $result = mysqli_query($conn, $sqlSelect);

	
if (mysqli_num_rows($result) > 0)
{
?>
        
    <table class='tutorial-table'>
        <thead>
            <tr>
                <th>Name</th>
                <th>Product Name</th>
				<th>Initial Stock Quantity</th>
				<th>As on Date</th>

            </tr>
        </thead>
<?php
    while ($row = mysqli_fetch_array($result)) {
?>                  
        <tbody>
        <tr>
            <td><?php  echo $row['product_id']; ?></td>
			<td><?php  echo $row['product_name']; ?></td>
            <td><?php  echo $row['init_stock_quantity']; ?></td>
			<td><?php  echo $row['as_on_date']; ?></td>
        </tr>
<?php
    }
?>
        </tbody>
    </table>
<?php 
} 
?>