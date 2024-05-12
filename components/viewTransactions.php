<section class="view-transaction-section m-3">
  <h3 class="title pb-1">View Transactions</h3>

  <div class="p-1  mb-3" style="width: 100%;">
    <div style="width: 100%;">
      <select name="dates" id="dates" class="view-transactions-dates" onChange="changeDate()">
        <?php 
          include '../processes/db_connection.php';
          date_default_timezone_set('Asia/Manila');
          $today = new DateTime();
          $beginning = new DateTime('2024-03-03');

          $daysSince = $today->diff($beginning);

          for ($i= -$daysSince->format("%a")+1;$i<0;$i++){
            $transactionDate = $today->modify( "-1 day" )->format('Y-m-d');
            echo '<option value="'.$transactionDate.'">'.date('M d, Y', strtotime($transactionDate)).'</option>';
          }
        ?>
      </select>
    </div>
  </div>

  <div id="loadingMessage2" style="display: none;"><img src="../img/loading.gif" width="150px" height="150px"/></div>

  <div id="prevTransactions"></div>
</section>

<div id="floating-menu">
  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16" onClick="toHome()">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
  </svg>
</div>

<script>
  function changeDate(){
    const dateSelect = document.getElementById("dates");
    var selectedDate = dateSelect.value;
    updateTransactions(selectedDate)
  }

  async function updateTransactions(date){
    const loadingMessage = document.getElementById("loadingMessage2");
    loadingMessage.style.display = "block";
    const displayTransaction = document.getElementById("prevTransactions");
    displayTransaction.innerHTML = "";
    try {
      const fetchTransactionsOption = {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({date: date})
      }

      const fetchTransaction = await fetch('../processes/fetchPreviousTransactions.php', fetchTransactionsOption);
      const transactionResult = await fetchTransaction.text();


      if (transactionResult !== "error"){
        loadingMessage.style.display = "none";
        displayTransaction.innerHTML = transactionResult;
      } else {
        alert("Error.");
      }
      

    } catch (error) {
      
    }
  }

  changeDate();
</script>