<section class="debts-section m-3">
  <h3 class="title pb-1 mb-3">Debts <?php
    include '../processes/db_connection.php';

    $debtSumStmt = $conn->prepare("SELECT SUM(debt) AS total FROM contacts");
    if ($debtSumStmt->execute()){
      $total = $debtSumStmt->get_result();
      while($row = $total->fetch_assoc()){
        $totalSum = $row['total'];
        echo "(Php $totalSum.00)";
      }
    }
  ?> </h3>
  <div class='debts-list' id="debtList"></div>
</section>

<div id="floating-menu">
  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16" onClick="toHome()">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
  </svg>
</div>

<script>
  function updateDebtList(){
    $.ajax({
      url: '../processes/fetchDebts.php',
      type: 'GET',
      success: function(response) {
        $('#debtList').html(response);
      },
      error: function(xhr, status, error) {
        console.error(error);
      }
    });
  }
  var ongoingPayment = false;
  async function updateDebt(id){
    if(ongoingPayment == false){
      ongoingPayment = true;
      try {
        const amount = document.getElementById("amount-"+id).value;

        document.getElementById("btn-"+id).disabled = true;

        const updateDebtOption = {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({amount: amount, id: id})
        }

        const fetchUpdateDebt = await fetch('../processes/updateDebt.php', updateDebtOption);
        const updateDebtResult = await fetchUpdateDebt.text();

        if(updateDebtResult == "done"){
          document.getElementById("close-btn-"+id).click();
          document.getElementById("btn-"+id).disabled = false;
          ongoingPayment = false;
          $.ajax({
            url: '../processes/fetchDebts.php',
            type: 'GET',
            success: function(response) {
              $('#debtList').html(response);
            },
            error: function(xhr, status, error) {
              console.error(error);
            }
          });
        } else {
          alert(updateDebtResult);
          ongoingPayment = false;
        }

      } catch (error) {
        
      }
    } else {

    }
  }

  updateDebtList();
</script>