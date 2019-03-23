<?php
    function getDBConnection(){
        $DB_SERVER = 'SERVER';
        $DB_USERNAME= 'USERNAME';
        $DB_PASSWORD = 'PASSWORD';
        $DB_DATABASE = 'DBNAME';
        $DB_CONNECTION = new PDO("mysql:host=$DB_SERVER;dbname=$DB_DATABASE;charset=utf8mb4", $DB_USERNAME, $DB_PASSWORD);
        $DB_CONNECTION->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $DB_CONNECTION;
    }
?>