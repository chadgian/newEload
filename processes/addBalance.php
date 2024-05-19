<?php
  include 'db_connection.php';

  $amount = json_decode(file_get_contents('php://input'), true)['addAmount'];

  $addBalanceStmt = $conn->prepare("UPDATE info SET current_load = current_load + ?");
  $addBalanceStmt->bind_param("s", $amount);

  if ($addBalanceStmt->execute()){
    echo "done";
  } else {
    echo $addBalanceStmt->error;
  }

?>