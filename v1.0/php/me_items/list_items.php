<?php

  class Item{
    // Declaring fields.
    private $name;
    private $price;
    private $description;
    private $dateadded;
    private $productsold;
    private $productid;
    private $BuyerID;
    private $isBid;
    private $SellerID;
    private $isSoldList;
    // setting up constructor
    public function __construct($isBid,$productid,$name,$price,$description,$dateadded,$productsold,$BuyerID,$SellerID,$isSoldList) {
      // Initialising fields.
      require_once('php/connect.php');
      $this->name = $name;
      $this->price = $price;
      $this->description = $description;
      $this->dateadded = $dateadded;
      $this->productsold = $productsold;
      $this->productid = (int)$productid;
      $this->BuyerID = $BuyerID;
      $this->isBid = $isBid;
      $this->SellerID = $SellerID;
      $this->isSoldList = $isSoldList;

    }

    // Function to display the item list.
    public function toHtml(){
      require_once('php/connect.php');
      require_once('php/generalFunctions.php');
      // Checks if the item is a sell now!
      if($this->isBid == 0){
        // Default values.
        $itemsold = "Unsold";
        $color = "blue";
        // CHecks if the product sold is declared
        if($this->productsold){
          // checks if the buyerID NULL
          if($this->BuyerID == NULL){
          // Changes the color to red.
          $color = "red";
          // This is a listing shown when the product has been removed from the market place.
          echo "<div class = 'items_added'>
            <hr>
            <h3>". $this->name ."</h3>
            <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
            <p> Date Added : " . $this->dateadded."</p><br>
          <p>Description: ". $this->description ."</p><br>
            <p id = 'statusofitem_' style =' color : " .$color.";'> You deleted this product! </p>
          </div>";

        }
        // This is when the item is sold properly.
        else{
          $purchaser = 0;
          global $connect;
          // Getting the purchasers username
          $purchaser = getUsername($this->BuyerID);
          $itemsold = "Sold";
          $color = "red";

          echo "<div class = 'items_added'>
            <hr>
            <h3>". $this->name ."</h3>
            <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
            <p> Date Added : " . $this->dateadded."</p><br>
            <p>Description: ". $this->description ."</p><br>
            <p id = 'statusofitem_' style =' color : " .$color.";'> " .$itemsold. " to " .$purchaser." </p>
          </div>";
        }
        }else{
        echo "<div class = 'items_added'>
          <hr>
          <h3>". $this->name ."</h3>
          <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
          <p> Date Added : " . $this->dateadded."</p><br>
        <p>Description: ". $this->description ."</p><br>
          <p id = 'statusofitem_' style =' color : " .$color.";'> " .$itemsold. " </p>
          <form method = 'post' action = 'php/me_items/list_items.php'>
          <input type='hidden' name='product_id' value=" .$this->productid." />
            <input type = 'submit' class = 'btn btn-danger' value = 'Stop Sale' name = 'submit'>
            </form>
        </div>";
      }
    }




    else{
      $itemsold = "Unsold";
      $color = "blue";
      if($this->productsold){
        if($this->BuyerID == NULL){
          require_once('php/connect.php');
          global $connect;
          $biddableProduct = NULL;
          $query = @mysqli_query($connect,'SELECT product_id,biddable FROM products WHERE BINARY product_id = '.$this->productid);
          while($row = mysqli_fetch_array($query)){
            $biddableProduct = (int)$row['biddable'];
          }
        if ($biddableProduct == 1){
          $itemsold = "Sold";
          $color = "red";
          echo "<div class = 'items_added'>
            <hr>
            <h3>". $this->name ."</h3>
            <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
            <p> Date Added : " . $this->dateadded."</p><br>
            <p>Description: ". $this->description ."</p><br>
            <p id = 'statusofitem_' style =' color : " .$color.";'> Auction has ended! </p>
          </div>";
      }


      }else{
        $purchaser = 0;
        global $connect;
        $query = @mysqli_query($connect,'SELECT user_id,username FROM person WHERE BINARY user_id = '.$this->BuyerID);
          while($row = mysqli_fetch_array($query)){
            $purchaser = $row['username'];

          }
      $itemsold = "Sold";
      $color = "red";
      if($this->isSoldList == 1){
        require_once('php/generalFunctions.php');
        echo "<div class = 'items_added'>
          <hr>
          <h3>". $this->name ."</h3>
          <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
          <p> Date Added : " . $this->dateadded."</p><br>
          <p>Description: ". $this->description ."</p><br>
          <p id = 'statusofitem_' style =' color : " .$color.";'> " .$itemsold. " from " .getUsername($this->SellerID)." </p>
          </div>";
      }else{


      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p>Description: ". $this->description ."</p><br>
        <p id = 'statusofitem_' style =' color : " .$color.";'> " .$itemsold. " to " .getUsername($this->BuyerID)." </p>
        </div>";
      }
    }
      }else{

        require_once('php/connect.php');
        require_once('php/generalFunctions.php');
        $bidValue = "";
        global $connect;
        $joiningQuery = @mysqli_query($connect,'SELECT products.product_id, products.product_name,products.product_price,products.product_desc,products.date_set,products.product_sold,products.SellerID,bids.bidValue,bids.product_id FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = '.$this->productid.' ORDER BY bids.bidValue DESC;');
        if($joiningQuery){
          $row = mysqli_fetch_array($joiningQuery);
          if($row['bidValue']){
            $bidValue =  $row['bidValue'];
          }else{
            $bidValue = "NO CURRENT BID";
          }
        }

      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>";
        if(typeOfProduct($this->productid) == 1){
        echo"
        <h3 class = 'pricing' style = 'font-size:15px;'>£" . $bidValue."</h3>";
      }
        echo"
        <p> Date Added : " . $this->dateadded."</p><br>
      <p>Description: ". $this->description ."</p><br>
        <p id = 'statusofitem_' style =' color : " .$color.";'> " .$itemsold. " </p>";
        // Checking if the product is not approved
        $query = mysqli_query($connect,"SELECT approval,product_id FROM products WHERE product_id = $this->productid");
        $row = mysqli_fetch_array($query);
        $approval = $row['approval'];
        // If the product is approved there is a end auction button. Else no button is shown
        if($approval != 0){
          echo"
        <form method = 'post' action = 'php/me_items/list_items.php'>
        <input type = 'hidden' name = 'bidProduct'>
        <input type='hidden' name='product_id' value=" .$this->productid." />
          <input type = 'submit' class = 'btn btn-danger' value = 'End Auction' name = 'submit'>
          </form>";
      }
      echo "</div>";
      }
    }
  }
  }
  // Checking if the POST value is set.
  if(isset($_POST['product_id'])){
    // Storing data we need in variables.
    session_start();
    $userid = $_SESSION['id'];
    $productid =  (int)$_POST['product_id'];
    // Requiring php scripts.
    require_once('../connect.php');
    require('../generalFunctions.php');
    // If this is a biddable Product!

    if(isset($_POST['bidProduct'])){
      require_once('../connect.php');
      global $connect;
      // Gets the highest bidder.
      $highestBidder = getHighestBidder($productid);
      if($highestBidder == 0){
        $query = "UPDATE products SET BuyerID = NULL, product_sold = 1  WHERE product_id = $productid";
        if(!mysqli_query($connect,$query)){
          echo mysqli_error($connect);
        }
      }else{
        $query = "UPDATE products SET BuyerID = $highestBidder, product_sold = 1  WHERE product_id = $productid";
        if(!mysqli_query($connect,$query)){
          echo mysqli_error($connect);
        }
      }
      // Updates the product to sold.

      // We must give the money if there is one to the seller.
      $highestBidValue = (int)getHighestBidValue($productid);
      $productSeller = (int)getOwner($productid);
      addMoney($highestBidValue,$productSeller);
    }else{
    require_once('../connect.php');

    $productid =  $_POST['product_id'];
    $query = "UPDATE products SET BuyerID = NULL, product_sold = 1 WHERE product_id = $productid";
    if(!mysqli_query($connect,$query)){
      echo mysqli_error($connect);
    }


  }
  header("Location: {$_SERVER['HTTP_REFERER']}");

  }

?>
