<html>
<header>

<h1>MarketPlace</h1>
</header>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

<link rel="stylesheet" href="/css/homecss.css" type="text/css">
<div style="max-width:20vw;margin: 30px auto;background-color:white;  box-shadow: 0px 0px 10px black;">
  <h1 style = "text-align:center;">Table errors</h1><hr>
<?php
  require_once('php/connect.php');
  global $connect;
  // Array of all the tables.
  $arrayOfTable = array('admin','adminlog','adminranks','biddableproducts','bids','buynowproducts','historylog','person','products','user');
  // Loops through array.
  foreach($arrayOfTable as $random){
    // Queryies through all the tables and tries to get all the information. If it doesnt exist error is displayed.
    if(!@mysqli_query($connect,"SELECT * FROM ".$random)){
      echo "<p style = 'color:red;'><b>Table does not exist: ".$random."</b></p>";
    }
  }
 ?>
</div>
</html>
