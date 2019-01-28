<?php
  // Requiring php scripts that are needed.
  require_once('php/connect.php');
  require_once('php/generalFunctions.php');
  class BuyNow{
    // Declaring fields.
    private $name;
    private $price;
    private $description;
    private $productid;
    private $user_id;
    private $userBalance;
    private $seller_name;
    private $seller_id;
    // Setting up constructor
    public function __construct($productid,$user_id) {
      // Initalising Fields.
      global $connect;
      $this->productid = $productid;
      $this->user_id = $user_id;
      // Getting Product DATA and SELLER data.
      $resp = @mysqli_query($connect,'SELECT products.SellerID,products.product_id,products.product_name,products.product_price,products.product_desc,products.product_sold,products.date_set,person.username FROM products JOIN person ON person.user_id = products.SellerID WHERE BINARY product_id = '.$this->productid);
      // Checking if $resp has data.
      if($resp){
        // Looping through the rows.
        while($row = mysqli_fetch_array($resp)){
          $this->seller_id = $row['SellerID'];
          $this->name = $row['product_name'];
          $this->price = $row['product_price'];
          $this->description = $row['product_desc'];
          $this->seller_name = $row['username'];
        }
      }
      // Getting Seller Data;
      $this->seller_name = getUsername($this->seller_id);
      // Getting the current users account balance.
      $account_query = @mysqli_query($connect,'SELECT user_id,account_balance FROM person WHERE BINARY user_id = '.$this->user_id);
      // Checking if the variable is initalised.
      if($account_query){
        // Getting the first query.
        $row = mysqli_fetch_array($account_query);
        // Storing the users balance.
        $this->userBalance = $row['account_balance'];
      }
    }

    // Here we are displaying the product.
    public function toHTML(){
      // Checking if the SESSION id exists.
      if(isset($_SESSION['id'])){
        echo "    <div class = 'item_panel'>
              <h1>".$this->name."</h1>
              <h3>Price: ".$this->price."</h3>
              <p><b>Description: </b> $this->description</p>
              <p> Seller: ".$this->seller_name." </p>
              <form action = '/php/product_purchase/purchaseProduct.php' method='post'>
              <input type='hidden' name = 'productid' value = ".$this->productid.">
              <input type='hidden' name = 'userid' value = ".$this->user_id.">
              <input name = 'buyNow'type = 'submit' class = 'btn btn-primary' value = 'Buy Now!''>
            </form>
            </div>";
            // Side panel would show up and display the users balance and see if they can afford it.
            // Checking if the users balance is greater than the price of the product.
        if(($this->userBalance) >= ($this->price)){
          echo "<div class='Pricing'>
            <h1>Balance:</h1><h3>You have £". $this->userBalance."</h3>
            <p>After this purchase you will have £ ".(($this->userBalance) - ($this->price))." </p>
            <p style = 'color : green;'><b>Can Afford</b></p>
          </div>";

        }else{
          echo "<div class='Pricing'>
            <h1>Balance:</h1><h3>You have £". $this->userBalance."</h3>
            <p>You need to top up £ ".(($this->price) - ($this->userBalance))." </p>
            <p style = 'color : red;'><b>Cannnot Afford</b></p>
          </div>";
        }
      }
    }
  }
?>
