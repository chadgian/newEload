<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log In</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <style>
    body {
      font-family: Helvetica, Arial, sans-serif;
      height: 100vh;
      background-color: #E3F7F6;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      padding: 0;
      background-size: 100%;
    }

    button {
      background-color: #282746;
      color: white;
      width: 7rem;
      border-radius: 2rem;
      margin-top: 1rem;
      border: none;
    }

    input {
      background-color: #B8E9E6;
      border: 1.5px solid #282746;
      border-radius: 5px;
    }
  </style>
</head>

  <body>
    <div>
      <form action="processes/login_process.php" method="POST" class="form-control d-flex flex-column gap-3 p-3 justify-content-center align-items-center"
        style="background-color: #E3F7F6; border: none; width: 100%;">
        <div class="text-center m-0 p-0">
          <h1 class="m-0 p-0">LOGIN</h1>
          <?php
            if (isset($_GET['err'])) {
              $error = $_GET['err'];
              if ($error == 1){
                echo "<p style='color: red' class='m-0 p-0'><small>Incorrect username or password.</small></p>";
              } else if ($error == 2) {
                echo "<p style='color: red' class='m-0 p-0'><small>Login error.</small></p>";
              }
            }
          ?>
        </div>
        
        <div class="d-flex flex-column">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required>
        </div>
        <div class="d-flex flex-column">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <button id="loginBtn" onClick="loginProcess()">Login</button>
      </form>
    </div>
  </body>
</html>