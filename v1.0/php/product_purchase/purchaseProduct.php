<?php
require_once('../connect.php');
require_once('../generalFunctions.php');
// Checking if the POST variables exist
  if(isset($_POST['productid']) && isset($_POST['userid'])){
    // Checking if there is a client loggedin
    session_start();
    if(isset($_SESSION['id'])){
      //Checking if the client is asking to buy the product
      if($_SESSION['id'] == $_POST['userid']){
        // Getting data about the purchase.
        $balance = 0;
        $user_id = $_POST['userid'];
        $product_price = 10000;
        // Getting the users account balance.
        $query = @mysqli_query($connect,'SELECT user_id,account_balance FROM person WHERE BINARY user_id ='.$_SESSION['id']);
        // Getting the first row.
        $row = mysqli_fetch_array($query);
        // Getting the account balance.
        $balance = $row['account_balance'];
        // Getting data about the product.
        $query = @mysqli_query($connect,'SELECT product_id,product_price,SellerID FROM products WHERE BINARY product_id ='.$_POST['productid']);
        // Getting the first row.
        $row = mysqli_fetch_array($query);
        // Getting the product price.
        $product_price = (int)$row['product_price'];
        //Getting the SELLERS id.
        $sellerID = (int)$row['SellerID'];
        // Checking if the balance of the user is equal to or greater than the users balance.
        if($balance >= $product_price){
          // Subtracting money from the users account.
        subtractMoney($product_price,$user_id);
        // Adding Money to the sellers account.
        addMoney($product_price,$sellerID);
        // SEtting the product as sold.
        if(!mysqli_query($connect,'UPDATE products SET BuyerID = ' . $_SESSION['id'].', product_sold = 1 WHERE BINARY product_id = '.$_POST['productid'])){
          echo mysqli_error($connect);
        }
        header("Location: /home.php");
      }else{
        // Going to display a error saying that you cannot afford.
        header("Location: /home.php?poor=true");
      }
      }
    }

  }
 ?>
