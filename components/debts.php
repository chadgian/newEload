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
  <div>
    <button class="btn btn-dark mb-3" style="width: 100%;" data-bs-toggle='modal' data-bs-target='#addDebt'>Add Debt</button>
  </div>
  <div id="loadingMessage" style="display: none;"><img src="../img/loading.gif" width="150px" height="150px"/></div>
  <div class='debts-list' id="debtList"></div>
</section>

<div id="floating-menu">
  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16" onClick="toHome()">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
  </svg>
</div>

<div class='modal fade' id='addDebt' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1' aria-labelledby='staticBackdropLabel' aria-hidden='true'>
  <div class='modal-dialog modal-dialog-centered'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h1 class='modal-title fs-5' id='staticBackdropLabel'>Add Debt</h1>
        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close' id='addDebt-close-btn'></button>
      </div>
      <div class='modal-body'>
        <label for="debtContactList">Select contact list:</label>
        <select name="debtContactList" id="debtContactList" style="width: 100%; padding: 5px; text-align: center; margin-bottom: 10px;">
          <?php
            $debtContactStmt = $conn->prepare("SELECT * FROM contacts WHERE debt = 0 ORDER BY contact_name ASC");
            if($debtContactStmt->execute()){
              $debtContactResult = $debtContactStmt->get_result();
              while($debtContactData = $debtContactResult->fetch_assoc()){
                $debtContactName = $debtContactData['contact_name'];
                $debtContactNumber = $debtContactData['contact_number'];
                $debtContactID = $debtContactData['contact_id'];
                echo "<option value='$debtContactID'>$debtContactName - $debtContactNumber</option>";
              }
            }
          ?>
        </select>
        <label for="addDebtInput">Enter debt amount:</label>
        <input type='number' id='addDebtInput' class="text-center">
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
        <button type='button' class='btn btn-primary' onClick='addDebt()' id='btn-add-debt'>Add</button>
      </div>
    </div>
  </div>
</div>

<script>
  function updateDebtList(){
    $('#loadingMessage').show();
    $('#debtList').hide();
    $.ajax({
      url: '../processes/fetchDebts.php',
      type: 'GET',
      success: function(response) {
        $('#debtList').show();
        $('#debtList').html(response);
        $('#loadingMessage').hide();
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
        };

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
          document.getElementById("close-btn-"+id).click();
          document.getElementById("btn-"+id).disabled = false;
          ongoingPayment = false;
          alert(updateDebtResult);
        }

      } catch (error) {
        
      }
    } else {

    }
  }
  
  async function addDebt(){
    document.getElementById("btn-add-debt").disabled = true;

    try {
      const id = document.getElementById("debtContactList").value;
      const debtAmount = document.getElementById("addDebtInput").value;

      const addDebtOption = {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({contactID: id, addDebtAmount: debtAmount})
      };

      const fetchAddDebt = await fetch('../processes/addDebt.php', addDebtOption);
      const addDebtResult = await fetchAddDebt.text();

      if (addDebtResult == "done"){
        document.getElementById("addDebt-close-btn").click();
        document.getElementById("btn-add-debt").disabled = false;
        $.ajax({
          url: '../components/debts.php',
          type: 'GET',
          success: function(response) {
            $('#content').html(response);
          },
          error: function(xhr, status, error) {
            console.error(error);
          }
        });

      } else {
        document.getElementById("addDebt-close-btn").click();
        document.getElementById("btn-add-debt").disabled = false;
        alert(addDebtResult);
      }
    } catch (error) {
      console.log("addDebt() error: "+error);
    }
  }

  updateDebtList();
</script>