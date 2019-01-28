<?php
require_once('../connect.php');
require_once('../generalFunctions.php');

  // Checking if the POST variables exist
  if(isset($_POST['productid']) && isset($_POST['userid'])){
    // Checking if there is a client loggedin
    session_start();
    if(isset($_SESSION['id'])){
      // Checking the current SESSION user is the same as the user trying to submit a bid request.
      if($_SESSION['id'] == $_POST['userid']){
        // Setting up my variables. Storing all my POST and SESSIONs inside variables.
        $productid = $_POST['productid'];
        $user_id = $_SESSION['id'];
        $productid = (int)$_POST['productid'];
        $requestBid = (int)$_POST['bidValue'];
        $currentBid = (int)$_POST['currentPrice'];
        $product_price = 10000;
        // Querying for the user_id and account balance
        $query = @mysqli_query($connect,"SELECT user_id,account_balance FROM person WHERE BINARY user_id =".$_SESSION['id']);
        // Getting the first row.
        $row = mysqli_fetch_array($query);
        // Storing the balance of the user.
        $balance = (int)$row['account_balance'];
        // Checking the users balance is greater than the requested bid.
            if($balance >  $requestBid){
              // CHecking the requested bid is greater than the highest bid.
              if($requestBid > $currentBid){
                // Taking money away from the users account.
                subtractMoney($requestBid,$user_id);
                // Inserting the bid into the bids table.
                $query = "INSERT INTO bids (bid_id,product_id,bidValue,user_id,refunded)
                VALUES (NULL,$productid,$requestBid,$user_id,0)";
                // Execute the query.
                $stmt = mysqli_prepare($connect,$query);
                mysqli_stmt_execute($stmt);

                //////////// Here we are setting up the refunds for the previous bids. ////////////////////
                // Query for the bids which have not been refunded and belongs to this specific product.
                $joiningQuery = @mysqli_query($connect,"SELECT products.product_id,bids.product_id,bids.bidValue,bids.user_id,bids.bid_id,bids.refunded FROM bids JOIN  products ON products.product_id = bids.product_id WHERE products.product_id = $productid AND bids.refunded = 0 ORDER BY bids.bidValue DESC");
                // Checking if the query is not empty.
                if($joiningQuery != ''){
                  // Looping throw each row.
                while($refundRow = mysqli_fetch_array($joiningQuery)){
                  global $productid;
                  // Checking the highest bidder is the person we are attempting to refund
                  if(getHighestBidder($productid) == $refundRow ['user_id']){
                    // No refund given to the highest Bidder
                  }else{
                    // Bid is being refunded to the previous bidder.
                    addMoney($refundRow['bidValue'],$refundRow['user_id']);
                    // Adds to the users history.
                    addHistory("Recieved a refund from the bid ".getProductName($productid)."the cost £".$refundRow['bidValue'],$refundRow['user_id']);
                    // Sets the refunded column to 1.
                    setRefuneded($refundRow['user_id'],$refundRow['bid_id']);
                }
              }

            }
            header("Location: {$_SERVER['HTTP_REFERER']}");
        }else{header("Location: ../../home.php?poor=true");}
      }else{header("Location: ../../home.php?poor=true");}
    }
  }
}

 ?>
