<section class="add-transaction-section m-3">
  <h3 class="title pb-1">Add Transaction</h3>

  <div class="d-flex flex-column gap-3 p-1 justify-content-center align-items-center">
    <div>
      <label for="contactList">Contact:</label>
      <select class="form-select form-select-sm" aria-label="Small select example" name="contactList" id="contactList">
        <option value="0">New Contact</option>
        <?php
          include '../processes/db_connection.php';
          date_default_timezone_set('Asia/Manila');

          $contactStmt = $conn->prepare("SELECT * FROM contacts ORDER BY contact_name");
          $contactStmt->execute();
          $result = $contactStmt->get_result();
          if ($result->num_rows > 0){
            while($data = $result->fetch_assoc()){
              $contactNumber = $data['contact_number'];
              $contactName = $data['contact_name'];
              $contact_id = $data['contact_id'];
              echo "<option value='$contact_id'>$contactName - $contactNumber</option>";
            }
          } else {
            echo  "<i>No Contacts Yet</i>";
          }
        ?>
      </select>
    </div>
    <div id="contact-name">
      <label for="">Name:</label>
      <input class="form-control" type="text" name="name" id="name" placeholder="e.g. Juan">
    </div>
    <div id="contact-number">
      <label for="phoneNumber">Phone Number:</label>
      <input class="form-control" type="number" name="phoneNumber" id="phoneNumber" placeholder="e.g. 09915496598">
    </div>
    <div class="oneLineInput">
      <label for="time">Time:</label>
      <input style="text-align: center;" class="form-control" type="time" name="time" id="time" value="<?php echo date('H:i'); ?>">
    </div>
    <div class="oneLineInput">
      <label for="daysNumber">Days:</label>
      <select style="text-align: center;" class="form-select" name="daysNumber" id="daysNumber">
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
      </select>
    </div>
    <div class="oneLineInput">
      <label for="dateStarted">Date:</label>
      <input style="text-align: center;" class="form-control" type="date" name="dateStarted" id="dateStarted" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <div class="paymentStatRadio">
      <div>
        <input type="radio" id="paid" name="paymentStatus" checked>
        <label for="paid" style="border-radius: 10px 0px 0px 10px">Paid</label>
      </div>
      <div>
        <input type="radio" id="unpaid" name="paymentStatus">
        <label for="unpaid" style="border-radius: 0px 10px 10px 0px">Unpaid</label>
      </div>
    </div>
    <button id="addTransactionBtn" class="addTransactionBtn" onClick="addTransaction()">Add</button>
  </div>

</section>

<div id="floating-menu">
  <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor" class="bi bi-house-fill" viewBox="0 0 16 16" onClick="toHome()">
    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
  </svg>
</div>

<script>
  var adding = false;

  function btnAppearance(appearance){
    const addBtn = document.getElementById("addTransactionBtn");
    
    if(appearance == "show"){
      addBtn.style.transform = "scale(0)";
      addBtn.style.display = "none";
    } else {
      addBtn.style.transform = "scale(1)";
      addBtn.style.display = "block";
    }
  }

  async function addTransaction(){
    if (adding == false) {
      btnAppearance("hide");
      const btn = document.getElementById("addTransactionBtn");
      btn.disabled = true;
      btn.style.filer = "grayscale(100%";
      adding = true;

      const id = document.getElementById("contactList").value;
      const time = document.getElementById("time").value;
      const days = document.getElementById("daysNumber").value;
      const date = document.getElementById("dateStarted").value;
      const paymentStatus = document.getElementsByName("paymentStatus");
      var paymentStatusValue = null;

      for (let i=0; i < paymentStatus.length; i++){
        if(paymentStatus[i].checked){
          paymentStatusValue = paymentStatus[i].id;
        }
      }

      // alert("id: "+id+" - time: "+time+" - days: "+days+" - date: "+date+" - payment: "+paymentStatusValue);

      if( id == "0"){
        const name = document.getElementById("name").value;
        const number = document.getElementById("phoneNumber").value;

        var data = null;

        if(!name || !number || !days || !date || !paymentStatusValue || !time){
          alert ("Please fill all data!");
          adding = false;
          return false;
        } else {
          data = {
            contactID: id,
            name: name,
            number: number,
            time: time,
            days: days,
            date: date,
            paymentStat: paymentStatusValue
          }
        }

      } else {
        if(!days || !date || !paymentStatusValue || !time){
          alert ("Please fill all data!");
          adding = false;
          return false;
        } else {
          data = {
            contactID: id,
            time: time,
            days: days,
            date: date,
            paymentStat: paymentStatusValue
          }
        }
      }

      try {
        const addTransOption = {
          method : "POST",
          headers: {"Content-Type": "application/json"},
          body: JSON.stringify(data)
        }

        const fetchAddTrans = await fetch('../processes/addTransactionProcess.php', addTransOption);
        const addTransStatus = await fetchAddTrans.text();

        if (addTransStatus == "done"){
          btnAppearance("show");
          $('#content').hide();
          $('#loadingMessage').show();
          $.ajax({
            url: '../components/home.php',
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
          adding = false;
        } else {
          btnAppearance("show");
          alert(addTransStatus);
          adding = false;
        }
      } catch (error) {
        btnAppearance("show");
        alert("js error: "+error);
      }
    }
  }
  
  document.getElementById("contactList").addEventListener("change", function(){
    console.log("Change event triggered");
    var selectedContact = this.value;
    console.log("Selected contact:", selectedContact);

    var contactName = document.getElementById("contact-name");
    var contactNumber = document.getElementById("contact-number");

    if (selectedContact == "0"){
        console.log("Option 0 selected");
        contactName.style.display = "block";
        contactNumber.style.display = "block";
    } else {
        console.log("Other option selected");
        contactName.style.display = "none";
        contactNumber.style.display = "none"; 
    }
  });
</script>