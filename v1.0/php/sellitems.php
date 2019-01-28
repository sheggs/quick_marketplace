<?php
  session_start();
  // Getting required php scripts
  require_once('connect.php');
  global $connect;
  // Checks if the user is logged in
  if(isset($_SESSION['id'])){
  // Storing data inside variables.
  $user_id = $_SESSION['id'];
  $error = false;
  $allRequiredData = array('product_name','price','desc');
  // Checking if all POST variables are set.
  foreach($allRequiredData as $data){
    if(empty(trim($_POST[$data]))){
      $error = true;
    }
  }
  // Checks if there is a POST Variable missing.
  if($error){
    header("Location: ../me.php?error=error");
  }else{
    // Storing the POST data.
    $product_name = $_POST['product_name'];
    $price = (int)$_POST['price'];
    $desc = $_POST['desc'];
    // Checking if you have enought money.
    if($price <= 0){
      header("Location: ../me.php?error=error");
    }else{
      require_once('connect.php');
      global $connect;
      // Inserting the data into the products table.
      $query = "INSERT INTO products (product_id,biddable,approval,SellerID,BuyerID,product_name, product_price,product_desc,product_sold, date_set, date_sold)
       VALUES (NULL,0,0,$user_id,NULL,?,?,?,0,NOW(),NULL)";
      $stmt = mysqli_prepare($connect,$query);
      mysqli_stmt_bind_param($stmt,'sis',$product_name,$price, $desc);
      mysqli_stmt_execute($stmt);
      // Go back a page.
      header("Location: {$_SERVER['HTTP_REFERER']}");

    }
  }
}
?>
