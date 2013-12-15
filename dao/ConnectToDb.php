<?php


require 'SettingsDb.php';

class connectToDb 
{
    function connect()
    {
        
        $DbSettings = new SettingsDb();
        $Mysqli = new mysqli($DbSettings->getDbIpPort(),$DbSettings->getDbUser(),$DbSettings->getDbPass(),$DbSettings->getDbTable());
        if ($Mysqli->connect_errno)
        {
            printf("Connection attempt failed : %s\n", $Mysqli->connect_error);
        }
        return $Mysqli;
    }
}

?>
