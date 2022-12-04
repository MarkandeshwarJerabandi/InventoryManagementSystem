<?php
	include('barcode128.php'); // include php barcode 128 class
	// design our barcode display
	echo '<div style="border:1px double #333; padding:5px;margin:5px auto;width:100%;">';
	if(isset($_POST['barcode']))
		echo bar128(stripslashes($_POST['barcode']),$_POST['prdname'],$_POST['price']);
	else
		echo '';
	echo '</div>';
?>
<html>
<head>
	<title>PHP Barcode Generator</title>
</head>
<body>
<fieldset>
	<legend>Detail Informations</legend>
		<form action="createbarcode.php" method="post"> <!-- Create Post method to createbarcode.php files -->
			<b>Enter Your Product Name</b><input type="text" name="prdname"/>
			<b>Enter Your Code </b><input type="text" name="barcode"/>
			<b>Enter Price of Product </b><input type="text" name="price"/>
			
			<input type="submit" value="Create Barcode" />
		</form>
</fieldset>
</body>
</html>