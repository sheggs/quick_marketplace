<?php
include_once('php/connect.php');
require_once('php/generalFunctions.php');
  class BidItem{
    // Setting up my fields
    private $name;
    private $price;
    private $description;
    private $dateadded;
    private $productsold;
    private $productid;
    private $username;
    private $first_name;
    private $last_name;
    private $currentBid;
    // Setting up my constructor
    public function __construct($username,$first_name,$last_name,$productid,$name,$price,$description,$dateadded,$productsold) {
      // Initalising my fields.
      global $connect;
      $this->name = $name;
      $this->price = $price;
      $this->description = $description;
      $this->dateadded = $dateadded;
      $this->productsold = $productsold;
      $this->productid = $productid;
      $this->username = $username;
      $this->first_name = $first_name;
      $this->last_name = $last_name;
      // Obtaining the bids for this speicif product.
      $joiningQuery = @mysqli_query($connect,'SELECT products.product_id, products.product_name,products.product_price,products.product_desc,products.date_set,products.product_sold,products.SellerID,bids.bidValue,bids.product_id FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = '.$this->productid.' ORDER BY bids.bidValue DESC;');
      // Checking if it is not empty.
      if($joiningQuery != ''){
        // Getting the first row.
        $row = mysqli_fetch_array($joiningQuery);
        // Checking if the bidValue is set.
        if($row['bidValue']){
          // The current bid is returned.
          $this->currentBid =  $row['bidValue'];
        }else{
          // A message is stored saying there are no current bids.
          $this->currentBid = "NO CURRENT BID";
        }

      }
    }
    // Displays the output for what the guest sees.
    public function GuestData(){
      // Displays the buy now data. No interactions for the user.
      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
        <h3 class = 'pricing' style = 'font-size:15px;'>Current Bid: £"  . $this->currentBid ."</h3><br>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p class = 'badge badge-primary'> Seller: </p><p><b> Username: </b> ".$this->username. "</p><p><b> &nbsp Full-Name: </b>   " .$this->first_name . " " .$this->last_name. " </p>
        <p>". $this->description ."</p><br>
      </div>";
    }

    // Displays the output for what the member sees.
    public function MemberData(){
      // Display the buy now data with a form to bid for the product.
      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
        <h3 class = 'pricing' style = 'font-size:15px;'>Current Bid: £"  . $this->currentBid ."</h3><br>

        <form method = 'get' action = '../home.php' class = 'BuyNowForm'>
          <input name = 'biddable' type = 'hidden' value = 1>
          <input name = 'productidenity' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' style = 'float:right; margin-right:-5rem; ' type = 'submit' value = 'Bid Now!'>
        </form>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p>". $this->description ."</p><br>
        <p class = 'badge badge-primary'> Seller: </p><p><b> Username: </b> ".$this->username. "</p><p><b> &nbsp Full-Name: </b>   " .$this->first_name . " " .$this->last_name. " </p>


      </div>";
    }
    // Displays the output for what the member sees.
    public function adminData(){
      // Display the buy now data with a form to bid for the product.
      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
        <h3 class = 'pricing' style = 'font-size:15px;'>Current Bid: £"  . $this->currentBid ."</h3><br>
        <form method = 'POST' action = '/php/home/remove_item.php' class = 'BuyNowForm' style='padding-left:5.6vw;'>
          <input name = 'product_remove' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' type = 'submit' value = 'Delete!' style='background-color:red;border:red;'>
        </form>
        <form method = 'get' action = '../home.php' class = 'BuyNowForm'>
          <input name = 'biddable' type = 'hidden' value = 1>
          <input name = 'productidenity' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' style = 'float:right; margin-right:-5rem; ' type = 'submit' value = 'Bid Now!'>
        </form>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p>". $this->description ."</p><br>
        <p class = 'badge badge-primary'> Seller: </p><p><b> Username: </b> ".$this->username. "</p><p><b> &nbsp Full-Name: </b>   " .$this->first_name . " " .$this->last_name. " </p>


      </div>";
    }
    public function toHTML(){
      if(isset($_SESSION['id'])){
        if(checkIfAdmin($_SESSION['id'])){
          $this->adminData();
        }else{
          $this->MemberData();
        }
      }else{
        $this->GuestData();
        }
      }
  }
//  if(isset($_POST['product_id'])){
  //   require_once('connect.php');
  //
  //   $productid =  $_POST['product_id'];
  //   $query = "UPDATE products SET product_sold = 1 WHERE product_id = ".$productid;
  //   $stmt = mysqli_prepare($connect,$query);
  //   mysqli_stmt_execute($stmt);
  // }
?>
