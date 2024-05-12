<?php
  include 'db_connection.php';
  $date = json_decode(file_get_contents('php://input'), true)['date'];

  $transactionsStmt = $conn->prepare("SELECT avails.*, transactions.* FROM transactions JOIN avails ON transactions.avail_id = avails.avail_id WHERE deadline = ? AND transaction_status = 'loaded' ORDER BY time_updated DESC");
  $transactionsStmt->bind_param("s", $date);

  if($transactionsStmt->execute()){
    $transactionsResult = $transactionsStmt->get_result();

    if($transactionsResult->num_rows > 0){
      while ($row = $transactionsResult->fetch_assoc()){
        $name = $row['availer_name'];
        $time = $row['time_updated'];
        $loadedBy = $row['loaded_by'];

        echo "
          <div class='prevTransaction'>
            <div><h4 class='text-center'>$name</h4></div>
            <div style='display: flex; flex-direction: column; align-items: end;'>
              <div><small>$time</small></div>
              <div style='text-align: end;'><small>Loaded by: $loadedBy</small></div>
            </div>
          </div>
        ";
      }
    } else {
      echo "
        <i style='text-align: center;'>No transactions for this day.</i>
      ";
    }
  }
?>