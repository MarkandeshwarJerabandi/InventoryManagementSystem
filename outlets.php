<?php
//reports.php
include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
	header("location:login.php");
}

include('outlet_header.php');

?>
<script>

</script>


	<div class="row">
	<?php
	if($_SESSION['type'] == 'master')
	{
	?>
	
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Outlets</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_outlets($connect); ?></h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Products Count</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_outlet_products($connect); ?></h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Sales Till Today</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_outlet_sales($connect); ?></h1>
			</div>
		</div>
	</div>
	
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Stock Available as on Today</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo number_format(count_total_outlet_stock($connect),2); ?></h1>
			</div>
		</div>
	</div>
	
	<?php
	}
	?>
	<!--	<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Order Value</strong></div>
				<div class="panel-body" align="center">
					<h1>$<?php //echo count_total_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Cash Order Value</strong></div>
				<div class="panel-body" align="center">
					<h1>$<?php //echo count_total_cash_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Credit Order Value</strong></div>
				<div class="panel-body" align="center">
					<h1>$<?php //echo count_total_credit_order_value($connect); ?></h1>
				</div>
			</div>
		</div>	-->
		<hr />
		<?php
		if($_SESSION['type'] == 'master')
		{
		?>
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Product Wise Total Stock Available </strong></div>
				<div class="panel-body" align="center">
					<?php echo get_product_wise_total_outlet_stock($connect); ?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>

<?php
include("footer.php");
?>
<script>
$(document).ready(function(){
		//$('#alert_action').html('<div class="alert alert-success">'+<?php echo get_min_stock_details($connect);?>+'</div>');
		//alert(<?php echo get_min_stock_details($connect);?>);
});

</script>