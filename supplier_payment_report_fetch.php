<?php

//stock_report_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	select * from
	stock_details, product, supplier_details,category
	where stock_details.product_id = product.product_id and product.supplier_id = supplier_details.supplier_id and product.category_id=category.category_id
";

// if(isset($_POST['from_date'],$_POST['to_date']) && $_POST['from_date']!='' && $_POST['to_date']!='')
// {
	// $query .= '
				// and (purchase_invoice.date_of_purchase between "'.$_POST['from_date'].'" and "'.$_POST['to_date'].'")
	// ';
// }

   if(isset($_POST["search"]["value"]))
   {
	   $query .= 'and (product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
	   $query .= 'or supplier_details.firm_name LIKE "%'.$_POST["search"]["value"].'%" )';
   }
   
//$query .= 'group by(purchase_invoice.purchase_id)';



 if(isset($_POST["stock"]))
 {
	 $query .= 'ORDER BY '.$_POST['stock']['0']['column'].' '.$_POST['stock']['0']['dir'].' ';
 }
 else
 {
	 $query .= 'order by(stock_details.product_id)';
 }

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
$i=0;
foreach($result as $row)
{
	
	$i++;
	$sub_array = array();
	$sub_array[] = $i;
	$sub_array[] = $row['product_name'] . '<br / >' . $row['category_name'];
	$sub_array[] = $row['firm_name'];
	$sub_array[] = $row['total_purchase_quantity'];
	$sub_array[] = $row['total_sales_quantity'];
	$sub_array[] = $row['stock_available'];

	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("select * from
	stock_details, product, supplier_details,category
	where stock_details.product_id = product.product_id and product.supplier_id = supplier_details.supplier_id and product.category_id=category.category_id");
	$statement->execute();
	//print $statement->rowCount();
	return $statement->rowCount();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);	

echo json_encode($output);

?>