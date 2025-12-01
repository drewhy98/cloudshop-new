<?php
// Primary Write Database Connection (Azure SQL Primary)
$serverName = "tcp:drewcardiffmet.database.windows.net,1433";
$connectionOptions = [
    "Database" => "myDatabase",
    "Uid" => "myadmin",
    "PWD" => "Abcdefgh0!",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];

$conn_write = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn_write) {
    die("Write DB connection failed: " . print_r(sqlsrv_errors(), true));
}
?>
