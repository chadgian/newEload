<?php
    session_start();
    $transactionID = json_decode(file_get_contents('php://input'), true)['transactionID'];
	date_default_timezone_set('Asia/Manila');
    include 'db_connection.php';
	$timeNow = date("H:i:s");
    $update = 'loaded';
    $loadedBy = $_SESSION['username'];
    $stmt = $conn->prepare("UPDATE transactions SET transaction_status = ?, loaded_by = ?, time_updated = ? WHERE transaction_id = ?");
    $stmt->bind_param("ssss", $update, $loadedBy, $timeNow, $transactionID);
    
    if($stmt->execute()){

        $stmt1 = $conn->prepare("UPDATE info SET current_load = current_load - 9.12 WHERE info_id = 1");
        
        if($stmt1->execute()){
            echo "Done";
        } else {
            echo $stmt1->error;
        }

        
    } else {
        echo $stmt->error;
    }

?>