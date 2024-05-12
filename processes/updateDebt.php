<?php
  include 'db_connection.php';

  $amount = json_decode(file_get_contents('php://input'), true)['amount'];

  $contactID = json_decode(file_get_contents('php://input'), true)['id'];

  $updateDebtStmt = $conn->prepare("UPDATE contacts SET debt = debt-$amount WHERE contact_id = $contactID");

  if ($updateDebtStmt->execute()){
    echo "done";
  } else {
    echo $updateDebtStmt->error;
  }

?>