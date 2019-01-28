<?php
require_once('connect.php');
global $connect;
$connection = $connect;

// Checking if user is an admin.
function checkIfAdmin($id){
  global $connect;
  // Querying the database to get the admin value.
  $query = @mysqli_query($connect,'SELECT user_id,admin FROM person WHERE BINARY user_id = '.$id );
  // getting the rows
    while($row = mysqli_fetch_array($query)){
      // Checking if the admin value is 1.
      if($row['admin'] == 1){
        // Return true as the user is now an admin
        return true;
      }
    }
  // Returned false as the user is not an admin
  return false;
}

// Checking for illegal characters
function checkIllegalChar($text){
  $texttoarray = str_split($text);
  $illegalCharacters = array("<",">",";","\\","\"","'");
  foreach($texttoarray as $letter){
    foreach($illegalCharacters as $Character){
      if($Character == $letter){
        return true;
        break;
      }
    }
  }
}

// Function checks if the user is banned
  function isBanned($userid){
    // Gets the global $connections variable
    global $connection;
    // Gets a binary value of the users ban status
    $query = @mysqli_query($connection,"SELECT ban FROM person WHERE user_id = $userid");
    // Gets the first row.
    $row = mysqli_fetch_array($query);
    // Casts this into a integer and check if the ban is false.
    if((int)$row['ban'] == 0){
      return false;
    }
    // If the ban is true. True is returned
    else{
      return true;
    }
  }

  // Redirects banned user.
    function redirectBan($userid){
      if(isBanned($userid)){
        // Emptying the session and destroying it!.
          session_start();
          $_SESSION = array();
          session_destroy();
          header("Location: ../banned.php");
      }
    }
  // Gets the highest bidder of the product.
  function getHighestBidder($productid){
    // Gets the global $connections variable.
    global $connection;
    // Joins the tables of bids and products. Sorts it in descending order
    $query = @mysqli_query($connection,"SELECT products.product_id,bids.product_id,bids.bidValue,bids.user_id FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = \"$productid\" ORDER BY bids.bidValue DESC");
    // Gets the first row.
    $getBidderQuery = mysqli_fetch_array($query);
    // Gets the userid of the  highest bidder.
    $highestBidder = $getBidderQuery['user_id'];
    // Use the value NULL if there is no highest bidder.
    if(!$highestBidder){
      $highestBidder = 0;
    }
    // Returns the highest Bidder
  return $highestBidder;

  }
  // Gets the highest bid value.
  function getHighestBidValue($productid){
    // Gets the global $connections variable.
    global $connection;
    // Joins the tables of bids and products. Sorts it in descending order
    $query = @mysqli_query($connection,"SELECT products.product_id,bids.product_id,bids.bidValue,bids.user_id FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = $productid ORDER BY bids.bidValue DESC");
    // Gets the first row.
    $getBidderQuery = mysqli_fetch_array($query);
    // Gets the bid value of the highest bidder.
    $highestBidder = $getBidderQuery['bidValue'];
    // Returns the highest Bid value
  return $highestBidder;
  }
  // Sets the refunded column to true. To note that the bid has been refunded
  function setRefuneded($user_id,$bid_id){
    global $connection;
    // Updates the refund column to true
    $query = "UPDATE bids SET refunded = 1 WHERE bid_id = $bid_id";
    $stmt = mysqli_prepare($connection,$query);
    mysqli_stmt_execute($stmt);
  }

  // Gets the products name
  function getProductName($productid){
    global $connection;
    // Querys the product name and id.
    $query = mysqli_query($connection,"SELECT product_name,product_id FROM products WHERE product_id = $productid");
    // Gets the first row of the product.
    $getBidderQuery = mysqli_fetch_array($query);
    //Returns the highest bidder.
    return $getBidderQuery['product_name'];
  }

  // Gets the username from the user id.
  function getUsername($userid){
    global $connection;
    // Querys for the user name and user id.
    $query = @mysqli_query($connection,"SELECT username,user_id FROM person WHERE user_id = $userid");
    // Gets the first row.
    $getUsername = mysqli_fetch_array($query);
    // Returns the username.
    if($userid == 0){
      $getUsername['username'] = "NULL. Sold to no one.";
    }
    return $getUsername['username'];
  }

  // Adds to the users history.
  function addHistory($string,$user_id){
    global $connection;
    // INSERTS the text and the user id into their history log.
    if(!mysqli_query($connection,"INSERT INTO historylog(log_id,log_text,user_id) VALUES(NULL,\"$string\",$user_id)")){
      echo mysqli_error($connection);
    }

  }

  // Getting the owner of the products
  function getOwner($product_id){
    global $connection;
    // Query joining both the person and products tables to get the username of the products owner.
    $query = mysqli_query($connection,"SELECT person.user_id FROM person JOIN products ON products.SellerID = person.user_id WHERE products.product_id = $product_id");
    // Gets the first row;
    $row = mysqli_fetch_array($query);
    // Returning the username.
    return $row['user_id'];
  }
  // Here we are adding money to the persons account
  function addMoney($amount,$userid){
    global $connection;
    // Querying for the account balance and the users id.
    $getAccountBalance = @mysqli_query($connection,'SELECT account_balance,user_id FROM person WHERE user_id = '.$userid );
    //Gets the row from the query.
    $abalance = mysqli_fetch_array($getAccountBalance);
    // adds the current account balance with the amount you want to add.
    $updateAccountBalance = intval($abalance['account_balance']) + intval($amount);
    // Sets up query to update the persons account
    $query = 'UPDATE person SET account_balance = '.$updateAccountBalance.' WHERE user_id = '.$userid;
    // Executes the query.
    $stmt = mysqli_prepare($connection,$query);
    mysqli_stmt_execute($stmt);
  }
  // Here we are subtracting money to the persons account
  function subtractMoney($amount,$userid){
    global $connection;
    // Querying for the account balance and the users id.
    $getAccountBalance = @mysqli_query($connection,'SELECT account_balance,user_id FROM person WHERE user_id = '.$userid );
    //Gets the row from the query.
    $abalance = mysqli_fetch_array($getAccountBalance);
    // subtracts the current account balance with the amount you want to add.
    $updateAccountBalance = intval($abalance['account_balance']) - intval($amount);
    // Sets up query to update the persons account
    $query = 'UPDATE person SET account_balance = '.$updateAccountBalance.' WHERE user_id = '.$userid;
    // Executes the query.
    $stmt = mysqli_prepare($connection,$query);
    mysqli_stmt_execute($stmt);
  }
  function getAdminID($userid){
    global $connection;
    $joiningQuery = @mysqli_query($connection,"SELECT admin_id,user_id FROM admin WHERE user_id = $userid");
    $getAdminID = mysqli_fetch_array($joiningQuery);
    if($getAdminID['admin_id']){
      return $getAdminID['admin_id'];
    }else{
      return false;
    }
  }
  // Function to get the Users admin rank.
  function getAdminRank($userid){
    global $connection;
    // Querys for the row in the admin table which belongs to this specific user.
    $query = @mysqli_query($connection,"SELECT position,user_id FROM admin WHERE user_id = $userid");
    // Gets the first result
    $row = mysqli_fetch_array($query);
    // Returns the rank of the user.
    return $row['position'];
  }
  // Function to get the admins permission value
  function getAdminPermission($userid){
    global $connection;
    // Querys for the admin permssion value.
    $query = @mysqli_query($connection,"SELECT admin.position,admin.user_id,adminRanks.permission_value FROM admin JOIN adminRanks ON adminRanks.admin_rank = admin.position  WHERE admin.user_id = $userid");
    // Gets the first result
    $row = mysqli_fetch_array($query);
    // Returns the rank of the user.
    return $row['permission_value'];
  }

  // Getting the type of the product.
  function typeOfProduct($productid){
    global $connection;
    // Querys the database for the biddable value.
    $query = @mysqli_query($connection,"SELECT biddable,product_id FROM products WHERE product_id = $productid");
    // Fetches the rows.
    $data = mysqli_fetch_array($query);
    // Returns the biddable column
    return $data['biddable'];
  }
  // Adding admin log
  function addAdminHistory($string,$adminid){
    global $connection;
    if(!mysqli_query($connection,"INSERT INTO adminLog(log_id,log_text,admin_id) VALUES(NULL,\"$string\",$adminid)")){
      echo mysqli_error($connection);
    }
  }
  // Checks if the email has been taken.
  function checkEmailTaken($email,$userid){
    global $connection;
    // Querys any records of the email
    $query = @mysqli_query($connection,"SELECT email,user_id FROM person WHERE email = \"$email\"");
    // Gets the row;
    $row = mysqli_fetch_array($query);
    // Checks if the email is taken
    if($row['email'] != ''){
      // Checks if the email is being already by this user.
      if($row['user_id'] == $userid){
        return false;
      }
      return true;
    }else{
      return false;
    }
  }
  // Checks if the username has been taken.
  function checkUsername($username,$userid){
    global $connection;
    // Querys any records of the email
    $query = @mysqli_query($connection,"SELECT username,user_id FROM person WHERE username = \"$username\"");
    // Gets the row;
    $row = mysqli_fetch_array($query);
    // Checks if the email is taken
    if($row['username'] != ''){
      // Checks if the email is being already by this user.
      if($row['user_id'] == $userid){
        return false;
      }
      return true;
    }else{
      return false;
    }
  }

 ?>
