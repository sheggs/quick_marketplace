<?php
include('../generalFunctions.php');
include_once('../connect.php');
// Checks if the POST value for product_remove is set.
if(isset($_POST['product_remove'])){
  // Storing the product id inside the
  $product_id = (int)$_POST['product_remove'];
  global $connect;


  // Checking if the product is a instant sell product.
  if( typeOfProduct($product_id) == 0){
    mysqli_query($connect,"DELETE FROM bids WHERE product_id = ".$product_id);
    mysqli_query($connect,"DELETE FROM biddableproducts WHERE product_id = ".$product_id);
    mysqli_query($connect,'DELETE FROM products WHERE product_id ='. $product_id);
  }
  // This executs when the product is a biddable product.
  else{
    mysqli_query($connect,"DELETE FROM buynowproducts WHERE product_id = ".$product_id);
    mysqli_query($connect,'DELETE FROM products WHERE product_id ='. $product_id);
  }
   header("Location: {$_SERVER['HTTP_REFERER']}");

}
 ?>
