<?php
//login.php



//	include 'crypto.php';
	$strdescription="";
	$message = '';
/*	ob_start();
	system('ipconfig /all');
	$mycom=ob_get_contents(); 
	ob_clean(); 
	$findme = "Physical";
	$t = exec('getmac');
	
	$pm = strpos($mycom, $findme); 
	$textm=substr($mycom,($pm+36),17);
	$crypt = new crypto();
	$text2 = $crypt->cypher($textm);
	$text2 = $crypt->cypher($t);
	$text1="UyHwq6VU7rHfFY+cSeqwnv+rLYANAwAKxDGvcMee6k0fACh0jX7ihUumXvXGSRqU9FYloLlfgczcdtLM5q+Wsg==";
	if($text2==$text1)
	{
*/		
		include('database_connection.php');

		if(isset($_SESSION['type']))
		{
			header("location:index.php");
		}

		

		if(isset($_POST["login"]))
		{
			$query = "
			SELECT * FROM user_details 
				WHERE user_email = :user_email
			";
			$statement = $connect->prepare($query);
			$statement->execute(
				array(
						'user_email'	=>	$_POST["user_email"]
					)
			);
			$count = $statement->rowCount();
			if($count > 0)
			{
				$result = $statement->fetchAll();
				foreach($result as $row)
				{
					if($row['user_status'] == 'Active')
					{
						if(password_verify($_POST["user_password"], $row["user_password"]))
						{
						
							$_SESSION['type'] = $row['user_type'];
							$_SESSION['user_id'] = $row['user_id'];
							$_SESSION['user_name'] = $row['user_name'];
							header("location:index.php");
						}
						else
						{
							$message = "<label>Wrong Password</label>";
						}
					}
					else
					{
						$message = "<label>Your account is disabled, Contact Master</label>";
					}
				}
			}
			else
			{
				$message = "<label>Wrong Email Address</labe>";
			}
		}
	//}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Purchase, Sales and Stock Management System</title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<br />
		<div class="container">
			<h2 align="center">Purchase, Sales and Stock Management System</h2>
			<br />
			<div class="panel panel-default">
				<div class="panel-heading">Login</div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group">
							<label>User Email</label>
							<input type="text" name="user_email" class="form-control" required />
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" name="user_password" class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>