<?php
  session_start();
  // Checking if you are logged in.
  if(isset($_SESSION['id'])){
    // Storing POST data.
    $user_id = $_SESSION['id'];
  require_once('connect.php');
  $error = false;
  $allRequiredData = array('product_name','price','desc');
  // Checking if form is completed.
  foreach($allRequiredData as $data){
    if(empty($_POST[$data])){
      $error = true;
    }
  }
  // If your form is empty.
  if($error || ($_POST['price'] == 0)){
   header("Location: ../me.php?error=error");
  }else{
    // Storing the POST data in variables.
    $product_name = $_POST['product_name'];
    $price = (int)$_POST['price'];
    $desc = $_POST['desc'];
    // If the price of the product is 0 or less.
    if($price <= 0){
      header("Location: ../me.php");
    }else{
      // Inserting data into products table.
      $query = "INSERT INTO products (product_id,biddable,approval,SellerID,BuyerID,product_name, product_price,product_desc,product_sold, date_set, date_sold)
       VALUES (NULL,1,0,$user_id,NULL,?,?,?,0,NOW(),NULL)";
      $stmt = mysqli_prepare($connect,$query);
      mysqli_stmt_bind_param($stmt,'sis',$product_name,$price, $desc);
      mysqli_stmt_execute($stmt);
      header("Location: {$_SERVER['HTTP_REFERER']}");


    }
  }
}
?>
