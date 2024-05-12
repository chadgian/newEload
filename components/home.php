<section class="home-section m-3">
  <h3 class="title pb-1">Incoming Transactions</h3>
  <div id="incoming-transactions" class="home-transactions">

    <?php
      include "../processes/db_connection.php";
      date_default_timezone_set('Asia/Manila');
      $today = date('Y-m-d');

      $inTransactionStmt = $conn->prepare("SELECT avails.*, transactions.* FROM transactions JOIN avails ON transactions.avail_id = avails.avail_id WHERE DATE(deadline) <= ? AND transaction_status = 'not_loaded' ORDER BY time_availed");
      $inTransactionStmt->bind_param("s", $today);
      
      try {
        if($inTransactionStmt->execute()){
          $inTransactionResult = $inTransactionStmt->get_result();

          if($inTransactionResult->num_rows > 0){
            while($inTransactionData = $inTransactionResult->fetch_assoc()){
              $transactionID = $inTransactionData['transaction_id'];
              $name = $inTransactionData['availer_name'];
              $number = $inTransactionData['phone_number'];
              $time = date("h:i A", strtotime($inTransactionData['time_availed']));
              $dateEnds = $inTransactionData['date_ends'];
              $deadline = $inTransactionData['deadline'];

              $deadline == $today ? $daysLeft = round((strtotime($dateEnds) - strtotime($today))/(60*60*24)) : $daysLeft = round((strtotime($today) - strtotime($inTransactionData['deadline']))/(60*60*24));
         
              $daysLeft < 2 ? $days = "day" : $days = "days";

              $deadline == $today ? $passed = "left" : $passed = "passed";

              $daysLeft == 0 ? $daysLeftPhrase = "Last day" : $daysLeftPhrase = "$daysLeft $days $passed" ; 

              echo "
                <div id='transaction'>
                  <div class='transaction-body' onClick='copyNumber($number)' id='transaction-$transactionID'>
                    <div><h4>$name</h4><span>$number</span></div>
                    <div><span>$time</span><span>$daysLeftPhrase</span></div>
                  </div>
                  <div class='transaction-action' onClick='doneTransaction($transactionID)'>
                    <img src='../img/check-circle.svg'>
                  </div>
                </div>
              ";
            }
          } else {
            echo "<i class='text-center'>All done</i>";
          }
        }
      } catch (\Throwable $th) {
        echo "$th";
      }
    ?>
  </div>

  <h3 class="title pb-1">Done Transactions</h3>
  <div id="done-transactions" class="home-transactions">
    
    <?php
      include "../processes/db_connection.php";
      date_default_timezone_set('Asia/Manila');
      $today = date('Y-m-d');

      $outTransactionStmt = $conn->prepare("SELECT avails.*, transactions.* FROM transactions JOIN avails ON transactions.avail_id = avails.avail_id WHERE DATE(deadline) = ? AND transaction_status = 'loaded' ORDER BY time_availed DESC");
      $outTransactionStmt->bind_param("s", $today);
      
      try {
        if($outTransactionStmt->execute()){
          $outTransactionResult = $outTransactionStmt->get_result();

          if($outTransactionResult->num_rows > 0){
            while($outTransactionData = $outTransactionResult->fetch_assoc()){
              $transactionID = $outTransactionData['transaction_id'];
              $name = $outTransactionData['availer_name'];
              $number = $outTransactionData['phone_number'];
              $time = date("h:i A", strtotime($outTransactionData['time_availed']));
              $dateEnds = $outTransactionData['date_ends'];
              $deadline = $outTransactionData['deadline'];

              $deadline == $today ? $daysLeft = round((strtotime($dateEnds) - strtotime($today))/(60*60*24)) : $daysLeft = round((strtotime($today) - strtotime($outTransactionData['deadline']))/(60*60*24));
         
              $daysLeft < 2 ? $days = "day" : $days = "days";

              $deadline == $today ? $passed = "left" : $passed = "passed";

              $daysLeft == 0 ? $daysLeftPhrase = "Last day" : $daysLeftPhrase = "$daysLeft $days $passed" ; 

              echo "
                <div id='transaction'>
                  <div class='transaction-body' id='transaction-$transactionID'>
                    <div><h4>$name</h4><span>$number</span></div>
                    <div><span>$time</span><span>$daysLeftPhrase</span></div>
                  </div>
                  <div class='transaction-action'>
                    <img src='../img/check-circle-fill.svg'>
                  </div>
                </div>
              ";
            }
          } else {
          }
        }
      } catch (\Throwable $th) {
        echo "$th";
      }
    ?>

  </div>
</section>

<div id="floating-menu">
  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-patch-plus-fill" viewBox="0 0 16 16" onClick="addTransactionPage()">
    <path d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01zM8.5 6v1.5H10a.5.5 0 0 1 0 1H8.5V10a.5.5 0 0 1-1 0V8.5H6a.5.5 0 0 1 0-1h1.5V6a.5.5 0 0 1 1 0"/>
  </svg>
</div>

<script>
  var ongoingTransaction = false;

  function copyNumber(number){
  var tempInput =document.createElement('input');
  number = "0"+number;

  tempInput.setAttribute("value", number);

  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");

  document.body.removeChild(tempInput);
  }

  async function doneTransaction(id){
    if (ongoingTransaction === false){
      ongoingTransaction = true;
      try {
        const transactionCard = document.getElementById("transaction-"+id);
        transactionCard.style.filter = "grayscale(100%)";
        const updateTransactionOption = {
          method: 'POST',
          headers: {
              'Content-Type': 'application/json'
          },
          body: JSON.stringify({transactionID: id})
        }

        const fetchUpdateTransaction = await fetch('../processes/updateTransaction.php', updateTransactionOption);
        const statusUpdateTransaction = await fetchUpdateTransaction.text();
      
        if (statusUpdateTransaction === "Done"){
          $.ajax({
            url: '../components/home.php',
            type: 'GET',
            success: function(response) {
              $('#content').html(response);
              updateHeader();
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        } else {
          alert ("transaction error: "+statusUpdateTransaction);
        }
        ongoingTransaction = false;
      } catch (error) {
        alert ("try catch error: "+error);
        ongoingTransaction = false;
      }
    }
  }

  function addTransactionPage(){
    $('#content').hide();
    $('#loadingMessage').show();
    $.ajax({
      url: '../components/addTransaction.php',
      type: 'GET',
      success: function(response) {
        $('#content').show();
        $('#content').html(response);
        $('#loadingMessage').hide();
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }
</script>