<?php
    session_start();
    require_once '../processes/db_connection.php';
    date_default_timezone_set('Asia/Manila');

    if (!isset($_SESSION['username'])) {
        header('Location: ../index.php');
        exit();
    } else {
        $pageName = "Homepage";
        $timestamp = date("Y-m-d H:i:s");
        $stmt0 = $conn->prepare("UPDATE users SET last_visited = ?, last_login = ? WHERE username = ?");
        $stmt0->bind_param('sss', $pageName, $timestamp, $_SESSION['username']);
        $stmt0->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="../styles/main.css">
</head>
<body>

  <div id="headerSection"><?php include '../components/header.php'; ?></div>
  
  <div id="loadingMessage" style="display: none;"><img src="../img/loading.gif" width="150px" height="150px"/></div>

  <div id="content"><?php include '../components/home.php'; ?></div>

</body>
</html>

<script>
  function toHome(){
    $('#loadingMessage').show();
    $('#content').hide();
    $.ajax({
      url: '../components/home.php',
      type: 'GET',
      success: function(response) {
        $('#content').show();
        $('#content').html(response);
        $('#loadingMessage').hide();
      },
      error: function(xhr, status, error) {
        console.log("what is this: "+error);
      }
    });
  }

  function updateHeader(){
    $.ajax({
      url: '../components/header.php',
      type: 'GET',
      success: function(response) {
        $('#headerSection').html(response);
      },
      error: function(xhr, status, error) {
        console.log("what is this: "+error);
      }
    });
  }
</script>