<?php

class Mysql
{
    public $conn, $db, $logs;
    
    public function __construct($host, $user, $password, $database)
    {
        $this->db = $database;
        $this->conn = mysqli_connect($host, $user, $password, $database);
    }
    
    public function query($q)
    {
        $this->logs[] = $q;
        
        return mysqli_query($this->conn, $q);
    }
    
    public function getAffectedRows()
    {        
        return mysqli_affected_rows($this->conn); 
    }

    public function select($q)
    {
        $result = $this->query($q);
        
        $records = array();
        
        while($row = mysqli_fetch_assoc($result))
        {
            $records[] = $row;
        }
        
        return $records;
    }
    
    public function transactionBegin()
    {
        $this->query("SET AUTOCOMMIT=0");
        $this->query("START TRANSACTION");
    }
    
    public function transactionCommit()
    {
        $this->query("COMMIT");
    }
    
    public function transactionRollback()
    {
        $this->query("ROLLBACK");
    }
}
