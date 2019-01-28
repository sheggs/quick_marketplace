<?php
  // Importing the required PHP scripts.
  require_once('php/connect.php');
  require_once('php/generalFunctions.php');
  class BidNow{
    // Declaring Fields.
    private $name;
    private $price;
    private $description;
    private $productid;
    private $user_id;
    private $userBalance;
    private $seller_name;
    private $seller_id;
    // Setting up constructor.
    public function __construct($productid,$user_id) {
      // Initalising fields.
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

      // Getting the current users account balance.
      $account_query = @mysqli_query($connect,'SELECT user_id,account_balance FROM person WHERE BINARY user_id = '.$this->user_id);
      if($account_query){
        while($row = mysqli_fetch_array($account_query)){
          // Storing the users account balance.
          $this->userBalance = $row['account_balance'];
        }
      }

      // Getting the highest bid.
      $joiningQuery = @mysqli_query($connect,'SELECT products.product_id, products.product_name,products.product_price,products.product_desc,products.date_set,products.product_sold,products.SellerID,bids.bidValue,bids.product_id FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = '.$this->productid.' ORDER BY bids.bidValue DESC;');
      // Checking if joiningQuery is set up.
      if($joiningQuery){
        $row = mysqli_fetch_array($joiningQuery);
        // Checking if row is initalised.
        if($row){
          $this->price =  $row['bidValue'];
        }
      }
    }
    // Here we are displaying the product.
    public function toHTML(){
      // Checking if the SESSION id exists.
      if(isset($_SESSION['id'])){
        echo "    <div class = 'item_panel'>
              <h1>".$this->name."</h1>
              <h3 style = 'color:green;'>Price: £".$this->price."</h3>
              <p><b>Description: </b> ".$this->description."</p>
              <p> Seller: ".$this->seller_name." </p>";
              // Getting the highest bid and displaying it.
          if(getHighestBidder($this->productid) == $_SESSION['id']){
            echo "<form action = '#' method='get'>
            <div class='input-group mb-3'>
            <div class='input-group-prepend'>
              <span class='input-group-text' id='basic-addon1'>£</span>
            </div>
            <input  id = 'disabledInput' type='text' name = 'bidValue' class='form-control' style = 'margin-right:30px;' placeholder='Bid Amount' disabled>
          </div>
            <button class = 'btn btn-primary' disabled>Disabled you have the highest bid!</button>
          </form>
          </div>";
        }
        // There is no highest bid so reserved price is shown.
        else{
              echo "<form action = '/php/product_purchase/bidProduct.php' method='post'>
              <input type='hidden' name = 'biddable' value = ''>
              <input type = 'hidden' name = 'currentPrice' value = ".$this->price.">
              <input type='hidden' name = 'productid' value = ".$this->productid.">
              <input type='hidden' name = 'userid' value = ".$this->user_id.">
              <div class='input-group mb-3'>
              <div class='input-group-prepend'>
                <span class='input-group-text' id='basic-addon1'>£</span>
              </div>
              <input type='text' name = 'bidValue' class='form-control' style = 'margin-right:30px;' placeholder='Bid Amount' >
            </div>
              <input name = 'buyNow'type = 'submit' class = 'btn btn-primary' value = 'Bid Now!''>
            </form>
            </div>";
          }
          // Side panel would show up and display the users balance and see if they can afford it.
          // Checking if the users balance is greater than the reserved price.
        if(($this->userBalance) > ($this->price)){
          echo "<div class='Pricing'>
            <h1>Balance:</h1><h3>You have £". $this->userBalance."</h3>
            <p style = 'color : green;'><b>Can Afford to exceed the current bid.</b></p>
          </div>";
        }else{
          echo "<div class='Pricing'>
            <h1>Balance:</h1><h3>You have £". $this->userBalance."</h3>
            <p>You need to top up £ ".(($this->price) - ($this->userBalance))." </p>
            <p style = 'color : red;'><b>Cannnot Afford to exceed current bid.</b></p>
          </div>";
        }
      }
    }
  }

?>
