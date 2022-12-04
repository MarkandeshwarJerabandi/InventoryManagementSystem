<?php
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='gps';
$strdescription='';
	$strerror='';
$foldername ="D:\GPSS\Backup";
if(isset($_POST["backup"]))
{
	
	
	function backup_tables($host,$user,$pass,$name,$foldername,$tables = '*')
	{
		include 'connect.php';
	    
		$db = new connectdb();
		$mysqli = $db->connect();
		
		mysqli_select_db($mysqli,$name);
		
		//get all of the tables
		if($tables == '*')
		{
			$tables = array();
			$result = $mysqli->query('SHOW TABLES');
			while($row = mysqli_fetch_row($result))
			{
				$tables[] = $row[0];
			}
		}
		else
		{
			$tables = is_array($tables) ? $tables : explode(',',$tables);
		}
		$return1='';
		//cycle through
		foreach($tables as $table)
		{
			$result = $mysqli->query('SELECT * FROM '.$table);
			$num_fields = mysqli_num_fields($result);
		//	echo $num_fields;
			$return1 .= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row($mysqli->query('SHOW CREATE TABLE '.$table));
			$return1 .= "\n\n".$row2[1].";\n\n";
			
			for ($i = 0; $i < $num_fields; $i++) 
			{
				while($row = mysqli_fetch_row($result))
				{
					$return1 .= 'INSERT INTO '.$table.' VALUES(';
					for($j=0; $j < $num_fields; $j++) 
					{
						$row[$j] = addslashes($row[$j]);
						$row[$j] = preg_replace("/\n/","\\n",$row[$j]);
						if (isset($row[$j])) { $return1 .= '"'.$row[$j].'"' ; } else { $return1 .= '""'; }
						if ($j < ($num_fields-1)) { $return1 .= ','; }
					}
					$return1 .= ");\n";
				}
			}
			$return1 .="\n\n\n";
		}
		
		//save file
		$filename = $foldername.'\db-backup-'.date('d-m-Y-h-i-s').'.sql';
		$handle = fopen($filename,'w+'); 	// change as per system
		fwrite($handle,$return1);
		
		fclose($handle);
		
	}
	backup_tables($dbhost,$dbuser,$dbpass,$dbname,$foldername);
	$strerror = false;
	$strdescription .= "Backup Created in the folder: " . $foldername;
	//$strdescription .= "Backup Created in the folder";
   // echo "entered";
	/* backup the db OR just a table */
	
	
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    

</head>

<body>

    <div id="wrapper">

      
      

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header"><i class="fa fa-users fa-fw"></i> Create Backup of Database </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <?php
                    if ($strerror == true && $strdescription!=""){
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h4 style="color:#C9302C;"><?=$strdescription?></h4>
                </div>        
                <?php
                    } else if ($strerror == false && $strdescription!=""){
                ?>
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h4 style="color:#449D44;"><?=$strdescription?></h4>
                </div>        
                <?php
                    }
                ?>
                <div class="col-lg-8 col-md-12 col-sm-12 col-xs-12">
                    <form role="form" method="post" action="index.php">
                        <div class="btn-group inline">
                                <center>
                                <button type="submit" name="backup" value="Click to Create Backup" class="btn btn-lg btn-success" style="margin-top: 20px;">Create Backup</button>
								
								
                                </center>
                            </div>
                    </form>
					
					 
				
                </div>
            </div>
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- jQuery -->
    <script src="../../jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../../metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../../dist/js/sb-admin-2.js"></script>

</body>

</html>
