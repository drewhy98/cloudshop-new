<?php
// Secondary Read-Only Replica
$replicaServer = "tcp:drewcardiffmet-replica.database.windows.net,1433";
$replicaOptions = [
    "Database" => "myDatabase",
    "Uid" => "myadmin",
    "PWD" => "Abcdefgh0!",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];

// Primary DB as fallback
$primaryServer = "tcp:drewcardiffmet.database.windows.net,1433";
$primaryOptions = [
    "Database" => "myDatabase",
    "Uid" => "myadmin",
    "PWD" => "Abcdefgh0!",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];

// Try connecting to replica first
$conn_read = sqlsrv_connect($replicaServer, $replicaOptions);

if (!$conn_read) {
    // Fallback to primary if replica fails
    $conn_read = sqlsrv_connect($primaryServer, $primaryOptions);
    if ($conn_read) {
        error_log("Replica DB unavailable. Falling back to primary DB for reads.");
    } else {
        die("Read DB connection failed: " . print_r(sqlsrv_errors(), true));
    }
}
?>
