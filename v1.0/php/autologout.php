<?php
  session_start();

  // Current Session duration.
  if(isset($_SESSION['LAST_ACTIVE'])){
    // Storing the current session duration.
    $sessionDuration = time() - $_SESSION['LAST_ACTIVE'];
    // If the time is greater than 3 hour.
      if($sessionDuration > 10800){
        // Redirect to logout
       header("Location: php/endSession.php?inactive=1");
      }

  }


 ?>
