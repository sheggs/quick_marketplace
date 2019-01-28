<?php
require_once('connect.php');
include('generalFunctions.php');
// Setting default values.
$error = false;
// Checking if the form stuff are not empty.
$allData = array('username','email','password','fn','ln');
foreach($allData as $data){
  if(empty(trim($_POST[$data]))){
    $error = true;
  }
}
// Checking if there is an error so the person is redirected.
if($error == true){
  header ("Location: ../login.php?error=in");
}
// Checks if these values are duplicates.
else{
  $duplicate = false;
  $resp = @mysqli_query($connect,'SELECT email,username FROM person');
  while($row = mysqli_fetch_array($resp)){
    if(($row['email'] == $_POST['email']) || ($row['username'] == $_POST['username']) ){
      $duplicate = true;
    }
  }


// If there are duplicates an error is displayed
if($duplicate){
  header('Location: ../login.php?error=duplicate');
}else{
  // Here we are trimming the data.
  $firstname = trim($_POST['fn']);
  $lastname = trim($_POST['ln']);
  $email = trim($_POST['email']);
  $username = trim($_POST['username']);
  $password = trim($_POST['password']);
  // To check for illegal characters
  if(checkIllegalChar($firstname) || checkIllegalChar($email) || checkIllegalChar($lastname) || checkIllegalChar($username) || checkIllegalChar($password)){
    header("Location: ../login.php?error=injection?");
  }
  else{
    // Registering the person by inserting data into mySQL
    mysqli_query($connect,"INSERT INTO person (user_id,ban,admin,email,password,username,first_name,last_name,account_balance,date_of_creation) VALUES (NULL,0,0,\"$email\",\"$password\",\"$username\",\"$firstname\",\"$lastname\",0,NOW())");

  // Querying for email and user id
  $resp = @mysqli_query($connect,'SELECT email,user_id FROM person');
  // SEtting default value
  $user_id = 0;
  // Loops through rows.
  while($row = mysqli_fetch_array($resp)){
    if($row['email'] == $_POST['email']){
      $user_id = $row['user_id'];
    }
  }
  // Inserts data into user section.
    $query = "INSERT INTO user (id,user_id) VALUES (NULL,$user_id)";
    $stmt = mysqli_prepare($connect,$query);

    // Executes data.
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($connect);
    session_start();

    // Storing user ID to the user_id.
    $_SESSION['id'] = $user_id;
    // Storing the last active time.
    $_SESSION['LAST_ACTIVE'] = time();

    // Redirecting to the me.php page.
      header("Location: /me.php");

  }
}
}



?>
