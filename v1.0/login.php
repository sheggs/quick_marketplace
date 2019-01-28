<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <link rel="stylesheet" href="css/main.css" type="text/css">
  <title>Document</title>
</head>
<body>
  <header>
    <h1>MarketPlace</h1>
    <a href = "home.php" class = 'btn btn-danger'> Home </a>
  </header>
  <?php
  // Here we are displaying custom error messages.
    if(isset($_GET['error'])){
      if($_GET['error'] == "in"){
      echo '<div class="alert alert-danger" role="alert">
      <span class="badge badge-danger">ERROR</span>Incorrect information when signing up or logging in!
      </div>';
    }
    elseif ($_GET['error'] == "accountnotfound") {
      echo '<div class="alert alert-danger" role="alert">
      <span class="badge badge-danger">ERROR</span> Acount not found!
      </div>';
    }
    elseif($_GET['error'] == "injection?"){
      echo '<div class="alert alert-danger" role="alert">
      <span class="badge badge-danger">ERROR</span>Invalid characters
      </div>';
    }  elseif($_GET['error'] == "duplicate"){
        echo '<div class="alert alert-danger" role="alert">
        <span class="badge badge-danger">ERROR</span>Data already exists.
        </div>';
      }

    else{
      echo '<div class="alert alert-danger" role="alert">
      <span class="badge badge-danger">ERROR</span>Not logged in!
      </div>';
    }

    }  elseif(isset($_GET['inactive'])){
        echo '<div class="alert alert-danger" role="alert">
        <span class="badge badge-danger">ERROR</span>Auto Logged out!
        </div>';
      }
session_start();
    if(isset($_SESSION['id'])){
    header("Location: /me.php");
    }
   ?>
  <div class="wrapper">
  <form action="php/signin.php" method = "post" class = "signinForm">
    <h1>Login to MarketPlace</h1>
    <div class="form-group data_input">
      <label for="exampleInputEmail1">Email address</label>
      <input type="email" class="form-control" id="exampleInputEmail1" name = "email" aria-describedby="emailHelp" placeholder="Enter email">
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
  </div>
  <div class="form-group data_input">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name = "password">
    </div>
        <input class = "btn btn-primary" type="submit"><br>

  </form>
  <form action="php/signup.php" method="post" class="information">
    <h1>Sign Up</h1>
    <div class="form-group signup_data">
      <label for="exampleInputEmail1">User Name </label>

      <input type="text" class="form-control" placeholder="Username" name = "username">
      <label for="exampleInputEmail1">Email address</label>
      <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email" name = "email">
      <small id="emailHelp" class="form-text text-muted">Please make sure thise is correct.</small>
  </div>
  <div class="form-group signup_data">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" name = "password">
      <label for="exampleInputEmail1">First Name</label>

      <input type="text" class="form-control" placeholder="First name" name = 'fn'>
      <label for="exampleInputEmail1">Last Name</label>

      <input type="text" class="form-control" placeholder="Last name" name = 'ln'>

    </div><br>

        <input class = "btn btn-danger" type="submit"><br>

  </form>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>
