<?php
include_once('php/connect.php');
require_once('php/generalFunctions.php');
global $connect;

  class Item{
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
    // Creating a constructor.
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

    }
    // Displays the output for the guestData();
    public function GuestData(){
      // Displays the buy now data. No interactions for the user.
      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
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
        <form method = 'get' action = '../home.php' class = 'BuyNowForm'>
          <input name = 'productidenity' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' type = 'submit' value = 'Buy Now!'>
        </form>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p>". $this->description ."</p><br>
        <p class = 'badge badge-primary'> Seller: </p><p><b> Username: </b> ".$this->username. "</p><p><b> &nbsp Full-Name: </b>   " .$this->first_name . " " .$this->last_name. " </p>


      </div>";
    }
    // Displays the output what the admin sees.
    public function adminData(){
      // Display the buy now data with a form to bid for the product.
      echo "<div class = 'items_added'>
        <hr>
        <h3>". $this->name ."</h3>
        <h3 class = 'pricing'>£"  . $this->price ."</h3><br>
        <form method = 'POST' action = '/php/home/remove_item.php' class = 'BuyNowForm' style='padding-left:3px;'>
          <input name = 'product_remove' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' type = 'submit' value = 'Delete!' style='background-color:red;border:red;'>
        </form>
        <form method = 'get' action = '../home.php' class = 'BuyNowForm' style = ''>
          <input name = 'productidenity' type = 'hidden' value = ".$this->productid.">
          <input class = 'btn btn-secondary' type = 'submit' value = 'Buy Now!'>
        </form>
        <p> Date Added : " . $this->dateadded."</p><br>
        <p>". $this->description ."</p><br>
        <p class = 'badge badge-primary'> Seller: </p><p><b> Username: </b> ".$this->username. "</p><p><b> &nbsp Full-Name: </b>   " .$this->first_name . " " .$this->last_name. " </p>


      </div>";
    }

    public function toHTML(){
      // Checking if logged in.
      if(isset($_SESSION['id'])){
        // Checks if the user is an admin.
        if(checkIfAdmin($_SESSION['id'])){
          // Displays the adminData.
          $this->adminData();
        }else{
          // Displays the member data.
          $this->MemberData();
        }
      }
      // If not logged in guestData() shows.
      else{
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
