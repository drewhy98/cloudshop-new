<?php
// ----------------------------------------------------
// Test Replication Lag using REAL shopusers table
// ----------------------------------------------------

// PRIMARY database
$primaryServer = "tcp:drewcardiffmet.database.windows.net,1433";

// REPLICA database
$replicaServer = "tcp:drewcardiffmet-replica.database.windows.net,1433";

$connectionOptions = array(
    "Database" => "myDatabase",
    "Uid" => "myadmin",
    "PWD" => "Abcdefgh0!",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
);

// Connect to PRIMARY
$primary = sqlsrv_connect($primaryServer, $connectionOptions);
if (!$primary) {
    die("Primary connection failed: " . print_r(sqlsrv_errors(), true));
}

// Create unique user
$firstName = "Replication";
$lastName = "Test";
$uniqueKey = uniqid();
$name = "$firstName $lastName $uniqueKey";
$email = "replication.test.$uniqueKey@example.com";
$password = password_hash("Replication123!", PASSWORD_DEFAULT);

$insertTime = microtime(true);

// Insert into PRIMARY
$sqlInsert = "INSERT INTO shopusers (name, email, password, created_at)
              VALUES (?, ?, ?, SYSDATETIME())";

$stmtInsert = sqlsrv_query($primary, $sqlInsert, array($name, $email, $password));

if (!$stmtInsert) {
    die("Insert failed: " . print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmtInsert);
sqlsrv_close($primary);

// Connect to REPLICA
$replica = sqlsrv_connect($replicaServer, $connectionOptions);
if (!$replica) {
    die("Replica connection failed: " . print_r(sqlsrv_errors(), true));
}

// Check REPLICA until user appears
$maxWaitMs = 8000;      // 8 sec max wait
$intervalMs = 100;      // check every 0.1 sec
$elapsed = 0;
$found = false;

while ($elapsed < $maxWaitMs) {

    $sqlCheck = "SELECT created_at FROM shopusers WHERE email = ?";
    $stmtCheck = sqlsrv_query($replica, $sqlCheck, array($email));

    if ($stmtCheck && ($row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC))) {
        $found = true;
        break;
    }

    usleep($intervalMs * 1000);
    $elapsed += $intervalMs;
}

$replicationLagMs = (microtime(true) - $insertTime) * 1000;

$statusColor = $found ? "#4CAF50" : "#FF0000";
$statusText  = $found ? "Replication Synced" : "Replica Did NOT Receive the Insert";

?>
<!DOCTYPE html>
<html>
<head>
<title>Replication Lag Test</title>
<style>
    body { font-family: Arial; padding: 20px; background: #f3f3f3; }
    .box {
        background: white; padding: 20px; border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;
    }
    .status {
        font-size: 24px; font-weight: bold;
        padding: 10px; border-radius: 5px; color: white;
        margin-top: 15px;
    }
    .btn { background: #4CAF50; color: white;
        padding: 10px 20px; border-radius: 5px; text-decoration: none; }
</style>
</head>
<body>

<div class="box">
    <h2>Replication Lag Test</h2>

    <p><strong>Test Email:</strong> <?php echo $email; ?></p>
    <p><strong>Replication Lag:</strong> <?php echo number_format($replicationLagMs, 2); ?> ms</p>

    <div class="status" style="background: <?php echo $statusColor; ?>">
        <?php echo $statusText; ?>
    </div>

    <br><br>
    <a href="index.php" class="btn">Back to Home</a>
</div>

</body>
</html>
<?php sqlsrv_close($replica); ?>
