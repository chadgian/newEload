<?php

  // $username = json_decode(file_get_contents('php://input'), true)['username'];
  // $password = json_decode(file_get_contents('php://input'), true)['password'];
  include 'db_connection.php';

  $username = $_POST['username'];
  $password = $_POST['password'];

  $loginStmt = $conn->prepare("SELECT * FROM users WHERE username = ? and password = ?");
  $loginStmt->bind_param("ss", $username, $password);
  
  if ($loginStmt->execute()){
    $loginResult = $loginStmt->get_result();
    if ($loginResult->num_rows > 0){
      $user = $loginResult->fetch_assoc();

      session_start();
      $_SESSION['username'] = $user['username'];
      $_SESSION['password'] = $user['password'];

      // return "login-success";
      header('Location: ../pages/main.php');
      exit();

    } else {
      // return "user-not-found";
      header('Location: ../index.php?err=1');
      exit();
    }
  } else {  
    // return "error-execute";
    header('Location: ../index.php?err=2');
    exit();
  }

?>