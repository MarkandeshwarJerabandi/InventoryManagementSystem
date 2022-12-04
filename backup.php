<?php
   
   $dbhost='localhost';
   $dbuser='skg';
   $dbpass='datab@5e';
   $dbname='skg';
   
   $backup_file = $dbname . date("Y-m-d-H-i-s") . '.gz';
   $command = "E:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump -h $dbhost -u $dbuser -p $dbpass ". "test_db | gzip > $backup_file";
   
   if(system($command))
	   echo "backup created";
   else
	   echo "unable to create backup";
?>