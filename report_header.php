<?php
//header.php
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Reports</title>
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>		
		<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">

			<nav class="navbar navbar-inverse">
				<div class="container-fluid">
					<div class="navbar-header">
						<a href="index.php" class="navbar-brand">Home</a>
					</div>
					<ul class="nav navbar-nav">
					<?php
					if($_SESSION['type'] == 'master')
					{
					?>
						<li><a href="sales_report.php">Sales Report</a></li>
					<!--	<li><a href="dpurchase_report.php">Detailed Purchase Report</a></li>	-->
						<li><a href="dsales_report.php">Detailed Sales Report</a></li>
						<li><a href="stock_report.php">Stock Available Report</a></li>
						<li><a href="productwise_report.php">Product Wise Stock Report</a></li>
						<li><a href="customer_payment_report.php">Customer Payment Due Report</a></li>
					<!--	<li><a href="customer_list.php">Customers List</a></li>
						<li><a href="supplier_list.php">Suppliers List</a></li>
						<li><a href="products_list.php">Products List</a></li>		-->
					<?php
					}
					?>
						
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span> <?php echo $_SESSION["user_name"]; ?></a>
							<ul class="dropdown-menu">
								<li><a href="profile.php">Profile</a></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>

				</div>
			</nav>
		