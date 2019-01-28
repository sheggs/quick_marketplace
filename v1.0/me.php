<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <link rel="stylesheet" href="css/me.css" type="text/css">
  <title>Document</title>
</head>
<body>

     <header>
       <?php
        require_once('php/connect.php');
        include('php/generalFunctions.php');
        include('php/autologout.php');
        include('php/me_items/list_items.php');
        echo "<div class ='btn-group' style = 'float:right;'>";
        // Checks if you are logged in.
          if(isset($_SESSION['id'])){
          // Redirects if you are banned
          redirectBan($_SESSION['id']);
          // Checks if you are an admin.
          if(checkIfAdmin($_SESSION['id'])){
            echo "<a href='admin/admin.php' class='btn btn-secondary'>Admin Panel</a>";
          }
        }
        ?>
         <a href="/php/endSession.php" class="btn btn-danger">Log Out</a>
         <a href = "/home.php" class = "btn btn-primary">Home</a>
       </div>
    <h1>MarketPlace</h1>

  </header>
  <?php   if(isset($_GET['error'])){
      echo '<div class="alert alert-danger" role="alert">
      <span class="badge badge-danger">ERROR</span> Product your tried to set up has a error.!
      </div>';
    }?>
    <?php
      require_once('php/connect.php');

      if(isset($_SESSION['id'])){
      //echo $_SESSION['id'];
      $user_id = $_SESSION['id'];
      // BOOTSTRAP DROPDOWN MENU TO DISPLAY HISTORY!
      echo "
        <a class='btn btn-primary historyButton' data-toggle='collapse' href='#dropDown' role='button' aria-expanded='false' aria-controls='dropDown'>View History</a>
      <div class='collapse' id='dropDown'>
        <div class='customCard'>";
        $query = @mysqli_query($connect,'SELECT * FROM historylog WHERE BINARY user_id ='.$user_id);
        // Here we are checking if the rows exist in the database.
          echo "<h3> History Log  </h3><br><hr>";
        while($row = mysqli_fetch_array($query)){

          echo "<p style = 'margin:0;padding:10px;font-weight'><b> - ".$row['log_text']." </b></h3><br>";

        }
        echo "</div></div>";
      //echo $user_id;
      $user_id = $_SESSION['id'];

      $resp = @mysqli_query($connect,'SELECT email,username,first_name,last_name,account_balance,admin,date_of_creation FROM person WHERE BINARY user_id ='.$user_id);
      $admin = "False";
      $row = mysqli_fetch_array($resp);
        if($row['admin'] == 1){
          $admin = "True";
        }
        echo "  <div class='wrapper'><div class='section_data'>
          <h3> Your information </h3><br>
          <h1> Email: </h1><p>". $row['email']." </p><br>
          <h1> Username: </h1><p>".$row['username'] ."</p><br>
          <h1> First Name: </h1><p>".$row['first_name'] ."</p><br>
          <h1> Last Name: </h1><p> ".$row['last_name'] ."</p><br>
          <h1> Account Balance: </h1><p>".$row['account_balance'] ."</p><br>
          <h1> Admin: </h1><p> ". $admin ."</p><br>


        </div>";




  }else{

          echo "Location: /login.php?error=not";
  }
     ?>
     <form action="/php/newbiditem.php" class="biditems"  method="post">

     <div class="wrapper_sell">

     <h1 class = "sellitemsinput">Biddable Products</h1>
     <div class="form-group data_input sellitemsinput">
       <label >Product Name</label>
       <input  class="form-control sellitemsinput" type = "text" name = "product_name" placeholder="Enter Product Name">
       <small id="emailHelp" class="form-text text-muted">Ensure this is correct.</small>
       <label >Reserved Price</label>

       <div class="input-group mb-3 sellitemsinput">
       <div class="input-group-prepend">
         <span class="input-group-text" id="basic-addon1">£</span>
       </div>
       <input name = "price" class="form-control" placeholder="Price">

   </div>
   <label >Product Description</label>

   <input type="text" name = "desc" class="form-control sellitemsinput" a aria-describedby="inputGroup-sizing-lg">
   <input id = "submitthething" type="submit" class="btn btn-primary">
   </div>
 </div>
         </form>
      <form action="/php/sellitems.php" class="sellitems"  method="post">

      <div class="wrapper_sell">

      <h1 class = "sellitemsinput">Instant Sell Products</h1>
      <div class="form-group data_input sellitemsinput">
        <label >Product Name</label>
        <input  class="form-control sellitemsinput" type = "text" name = "product_name" placeholder="Enter Product Name">
        <small id="emailHelp" class="form-text text-muted">Ensure this is correct.</small>
        <label >Product Price</label>

        <div class="input-group mb-3 sellitemsinput">
        <div class="input-group-prepend">
          <span class="input-group-text" id="basic-addon1">£</span>
        </div>
        <input name = "price" class="form-control" placeholder="Price">

    </div>
    <label >Product Description</label>

    <input type="text" name = "desc" class="form-control sellitemsinput" a aria-describedby="inputGroup-sizing-lg">
    <input id = "submitthething" type="submit" class="btn btn-primary">
    </div>
  </div>
          </form>

          <div class = "listeditems">
            <h1> SellNow Items on market </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              //echo $_SESSION['id'];
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,biddable,product_name,product_price,product_desc,product_sold,date_set,BuyerID,SellerID FROM products WHERE BINARY product_sold = 0 AND approval = 1 AND biddable = 0 AND SellerID ='.$user_id);
              if($resp){
              while($row = mysqli_fetch_array($resp)){

                $testing = new Item(0,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],0);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>

          <div class = "listeditems2">
            <h1> Biddable Items on market </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              //echo $_SESSION['id'];
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,biddable,product_name,product_price,product_desc,product_sold,date_set,BuyerID,SellerID FROM products WHERE BINARY product_sold = 0 AND approval = 1 AND biddable = 1 AND SellerID ='.$user_id);
              if($resp){
              while($row = mysqli_fetch_array($resp)){

                $testing = new Item(1,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],0);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>
          <div class = "listeditemssold">
            <h1>Your sold Items </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,product_name,product_price,product_desc,product_sold,BuyerID,date_set,biddable,SellerID FROM products WHERE BINARY approval = 1 AND biddable = 0 AND product_sold = 1 AND SellerID ='.$user_id);
              if($resp){
              while($row = mysqli_fetch_array($resp)){
                $testing = new Item(0,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],0);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>
          <div class = "listeditemssold1">
            <h1>Your Sold Auction Items </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              //echo $_SESSION['id'];
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,product_name,product_price,product_desc,product_sold,BuyerID,date_set,biddable,SellerID FROM products WHERE BINARY biddable = 1 AND product_sold = 1 AND SellerID ='.$user_id);
              // /////////
              if($resp){
              while($row = mysqli_fetch_array($resp)){
                $testing = new Item(1,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],0);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>
          <div class = "listeditemssold3">
            <h1>Your Purchased Items </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              //echo $_SESSION['id'];
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,product_name,product_price,product_desc,product_sold,BuyerID,date_set,SellerID FROM products WHERE BINARY product_sold = 1 AND BuyerID ='.$user_id);
              // /////////
              if($resp){
              while($row = mysqli_fetch_array($resp)){
                $testing = new Item(1,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],1);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>
          <div class = "listeditemssold4">
            <h1>Your Waiting for approval Items </h1>
            <?php
              require_once('php/connect.php');
              if(isset($_SESSION['id'])){
              //echo $_SESSION['id'];
              $user_id = $_SESSION['id'];

              //echo $user_id;
              $resp = @mysqli_query($connect,'SELECT product_id,product_name,product_price,product_desc,product_sold,BuyerID,date_set,SellerID FROM products WHERE BINARY product_sold = 0 AND approval = 0 AND product_sold = 0 AND SellerID ='.$user_id);
              // /////////
              if($resp){
              while($row = mysqli_fetch_array($resp)){
                $testing = new Item(1,$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold'],$row['BuyerID'],$row['SellerID'],1);
                $testing->toHtml();

             }
           }else{
             echo "Nothing!";
           }


                }else{
                echo '<script type="text/javascript">
                           window.location = "http://localhost/login.php?error=not"
                      </script>';
                    }

             ?>

          </div>

</div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>
