<?php

/**
 * periexei tis rithmiseis tis vashs pou pernei apo to dbsettings.ini
 * tis epistrefei spasmenes se pedia
 */

class settingsDb 
{
    private $dbIpPort;
    private $dbUser;
    private $dbPass;
    private $dbTable;
    
    public function __construct() 
    {
         $ini_array = \parse_ini_file("dbsettings.ini");
         
         //spaw ton pinaka se pedia gia kaluterh anagnwsimotita.
         $this->dbIpPort = $ini_array["dbIpPort"];
         $this->dbUser = $ini_array["dbUser"];
         $this->dbPass = $ini_array["dbPass"];
         $this->dbTable = $ini_array["dbTable"];
         
    }
    
    public function getDbIpPort() {
        return $this->dbIpPort;
    }

    public function getDbUser() {
        return $this->dbUser;
    }

    public function getDbPass() {
        return $this->dbPass;
    }

    public function getDbTable() {
        return $this->dbTable;
    }
}

?>
