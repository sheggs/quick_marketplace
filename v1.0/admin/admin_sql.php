<?php
  session_start();
  require_once('../php/connect.php');
  require_once('../php/generalFunctions.php');
  $currentUser = $_SESSION['id'];
  global $connect;
  // Checking for if the request is a product approval.
  if(isset($_POST['approval'])){
    // Approve the product
    // Storing data from POST and getting more info using custom functions.
    $product_id = (int)$_POST['product_id'];
    $adminid = getAdminID($_SESSION['id']);
    $productname = $_POST['product_name'];
    $username = getUsername($_SESSION['id']);
    if(isset($_POST['approved'])){
      // Getting the admins ID.
      if(getAdminID($_SESSION['id'])){
        // Approves the product.
        @mysqli_query($connect,'UPDATE products SET approval = 1 WHERE BINARY product_id = '.$product_id);
        // Checking if the product is a biddable product.
        if((int)typeOfProduct($_POST['product_id']) == 1 ){
          // Inserts data into biddableproducts table.
          mysqli_query($connect,"INSERT INTO biddableproducts(biddable_id,product_id) VALUES (NULL,$product_id)");
        }
        else{
          // Inserts data into buynowproducts table.
          mysqli_query($connect,"INSERT INTO buynowproducts(buynow_id,product_id) VALUES (NULL,$product_id)");
        }
        // Logs the changes into the logs.
        addHistory("Admin ID: $adminid has approved your product (ID: $product_id Name :$productname)",(int)getOwner($product_id));
        addAdminHistory("Admin ID: $adminid has approved product(ID: $product_id Name :$productname)",(int)$adminid);
        // Goes back a page.
        header("Location: {$_SERVER['HTTP_REFERER']}");
      }
    }
    // Decline the product
    else{
      // Delets the product.

      mysqli_query($connect,'DELETE FROM products WHERE product_id = '.$product_id);

      // Logs the changes.
      addHistory("Admin ID: $adminid has deleted your product (ID: $product_id)",(int)getOwner($product_id));
      addAdminHistory("Admin ID: $adminid has deleted product(ID: $product_id)",(int)$adminid);
      // Goes back a page.
      header("Location: {$_SERVER['HTTP_REFERER']}");
    }
  }
  // This checks if we are going to change the account options e.g ban or change money
  elseif(isset($_POST['account_options'])){
    global $currentUser;
    // Retrieving all the data sent by POST method.
    $userid = (int)$_POST['user_id'];
    $balance = (int)$_POST['account_balance'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    // Checking for illegal Characters;
    if(checkIllegalChar($email) || checkIllegalChar($username)){
        header("Location: admin.php?error=data");

    }
    else{
      // Checks if the Inputted data has already been used [Such as the username and email].
      if((checkUsername($username,$userid)) || (checkEmailTaken($email,$userid))){
        // Goest to error page.
        header("Location: admin.php?error=data");
      }else{
        // Updates the user name, email and balance of the user.
        mysqli_query($connect,"UPDATE person SET username = \"$username\", email = \"$email\",  account_balance = $balance WHERE user_id = \"$userid\"");
        addAdminHistory("Updated userID: $userid account information.",$currentUser);
        // Goes back a page.
        header("Location: admin.php?success=datachange");
      }
    }
  }
  // This checks if we want to change the users rank.
  elseif(isset($_POST['change_rank'])){
    // Stores the id of the user we want to affect and the requested Rank.
    $target = $_POST['userid'];
    $requestedRank = $_POST['change_rank'];
    echo $requestedRank;
    // Checks if the user exists as a admin. If it is not a admin a row is created.
    if(getAdminRank($target) == ''){
      if(!mysqli_query($connect,"UPDATE person SET admin = 1 WHERE user_id = \"$target\"")){
        echo mysqli_error($connect);
      }
    mysqli_query($connect,"INSERT INTO admin (admin_id,position,user_id) VALUES (NULL, \"$requestedRank\" ,$target)");
    // Remove the user from the user table since this user is now a admin.
    mysqli_query($connect,"DELETE FROM user WHERE user_id = $target");

    }else{
      // Update the persons rank;
      mysqli_query($connect,"UPDATE admin SET position = \"$requestedRank\" WHERE user_id = \"$target\"");
      if(!mysqli_query($connect,"UPDATE person SET admin = 1 WHERE user_id = \"$target\"")){
        echo mysqli_error($connect);
      }    }
    // Updates the user to an admin;

    // Logs the change in rank.
    addAdminHistory("Changed $userid rank to $requestedRank",$currentUser);


    // Return to the main page.
    header("Location: {$_SERVER['HTTP_REFERER']}");
  }
  elseif(isset($_POST['ban_user'])){
    // Stores the target user in a variable
    $target = (int)$_POST['user_id'];
    // Checks if we are unbanning the user
    if(isset($_POST['UnBan'])){
      global $currentUser;
      // removes the ban.
       @mysqli_query($connect,"UPDATE person SET ban = 0 WHERE BINARY user_id = ".$target);
       addAdminHistory("Unbanned USERID: $target",$currentUser);

    }
      // This only occurs if we are banning the user
    else{
      global $currentUser;
      // bans the user.
      @mysqli_query($connect,"UPDATE person SET ban = 1 WHERE BINARY user_id = ".$target);
      addAdminHistory("Banned USERID: $target",$currentUser);

    }
    // Goes back a page.
    header("Location: {$_SERVER['HTTP_REFERER']}");


  }elseif(isset($_POST['demote'])){
    $target = (int)$_POST['userid'];
      if(!mysqli_query($connect,"DELETE FROM admin WHERE user_id = ".$target)){
        echo mysqli_error($connect);
      }
      if(!mysqli_query($connect,"INSERT INTO user(id,user_id) VALUES(NULL,$target)")){
        echo mysqli_error($connect);
      }
      header("Location: {$_SERVER['HTTP_REFERER']}");
  }


 ?>
