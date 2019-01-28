<?php
require_once('connect.php');


session_start();
//$resp = @mysqli_query($connect,'SELECT email FROM userinfo');
// Checks if already logged in.
if(isset($_SESSION['id'])){
  // Go to me.php
  header("Location: me.php");
}else{
  // Logs the user in
$useridentification = '';
// Sets default values for the variables.
$error = false;
$loggedin = false;
// Checking if these post values are not empty.
$allData = array('email','password');
foreach($allData as $data){
  if(empty($_POST[$data])){
    $error = true;
  }
}
// If the error exits the person is not logged in.
if($error == true){
  header("Location: /login.php?error=in");
}else{
  // Gets the persons user id and logs them in.
  $resp = @mysqli_query($connect,'SELECT email,password,user_id FROM person');
  while($row = mysqli_fetch_array($resp)){
    if($row['email'] == $_POST['email']){
      if($row['password'] == $_POST['password']){
        $loggedin = true;
        $useridentification = $row['user_id'];
      }
    }
  }
}


if($loggedin){
  $_SESSION['id'] = $useridentification;
  $_SESSION['LAST_ACTIVE'] = time();
  header("Location: /me.php");

}else{

        header("Location: /login.php?error=accountnotfound");

}
}



?>
