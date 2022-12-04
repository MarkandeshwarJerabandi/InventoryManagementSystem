<?php

class connectdb
{
    var $host='localhost';
    var $username='skg';
    var $password='datab@5e';
    var $database='skg';
    var $myconn;
    
    public function connect()
    {

        $conn = mysqli_connect($this->host,$this->username,$this->password,$this->database);

        if (mysqli_connect_errno())
        {
            die ("Cannot connect to the database");
        }

        else
        {

            $this->myconn = $conn;

        }

        return $this->myconn;

    }
    public function close()
    {
        mysqli_close($this->myconn);
    }
}

?>
