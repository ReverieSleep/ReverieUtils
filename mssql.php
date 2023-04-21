<?php
//mssql
$serverName = "192.168.11.230";
$connectionOptions = array(
    "Database" => "P21",
    "Uid" => "jitterbit",
    "PWD" => "Dr3@mSupr3m3123"
);
//Establishes the connection
$mssqlConnection = sqlsrv_connect( $serverName, $connectionOptions );
if( $mssqlConnection === false ) {
    die(print_r(sqlsrv_errors()));
}
?>