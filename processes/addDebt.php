<?php
  include 'db_connection.php';

  $contactID = json_decode(file_get_contents('php://input'), true)['contactID'];
  $debtAmount = json_decode(file_get_contents('php://input'), true)['addDebtAmount'];

  $addDebtStmt = $conn->prepare("UPDATE contacts SET debt = debt+$debtAmount WHERE contact_id = $contactID");

  if ($addDebtStmt->execute()){
    echo "done";
  } else {
    echo $updateDebtStmt->error;
  }
?>