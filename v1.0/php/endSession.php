<?php
// Emptying the session and destroying it!.
  session_start();
  $_SESSION = array();
  session_destroy();
  // Checking if its due to inactivity
  if(isset($_GET['inactive'])){
    // Redirects to have a custom message.
    header("Location: /login.php?inactive=1");
  }else{
    // Goes to logout page.
  header("Location: /login.php");
  }
?>
