<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

  <link rel="stylesheet" href="/css/homecss.css" type="text/css">
  <title>Document</title>
</head>
<body>

     <header>

    <h1>MarketPlace</h1>
    <?php
    session_start();
    // Adds a sign out button and account button if you are logged in
      if(isset($_SESSION['id'])){
        require_once('php/generalFunctions.php');
        // Redirects if you are banned
        redirectBan($_SESSION['id']);
        echo "<div class = 'widgets_market'><a href = '/php/endSession.php'  class = 'btn btn-danger'>Sign Out </a><a href = '/me.php' class = 'btn btn-primary'> Account Panel </a></div>";
      }
      // Only displays a log out button for a guest.
      else{
        echo "<div class = 'widgets_market button-box col-lg-12'><a href = '/login.php'  class = 'btn btn-primary'>Login</a></div>";
      }
    ?>
  </header>
  <?php
  // Checks if the GET value poor is set to display a custom message.
  if (isset($_GET['poor'])) {
    echo '<div class="alert alert-danger" role="alert">
    <span class="badge badge-danger">ERROR</span> You cannot afford!
    </div>';
  }
   ?>
  <div class="wrapper">

            <?php
            // Requring php scripts that are needed.
              require_once('php/connect.php');
              require_once('php/home/display_buynowitems.php');
              require_once('php/home/display_biddableitems.php');

              // Checks if the GET value productidentity is set. This is to display a specific product on the page.
                if(isset($_GET['productidenity'])){
                  // Checks if the product is a biddable product
                  if(isset($_GET['biddable'])){
                    // Displaying the product page.
                    require_once("php/home_productspage/biddableproduct.php");
                    $bidNow = new BidNow($_GET['productidenity'],$_SESSION['id']);
                    $bidNow->toHTML();
                  }else{
                    // Displaying the product page.
                    require_once("php/home_productspage/buynow_product.php");
                    $buyNowProduct = new BuyNow($_GET['productidenity'],$_SESSION['id']);
                    $buyNowProduct->toHTML();
                }

                }else{

              echo "<div class = 'listeditems'>
                  <h1> Buy Now on market </h1>";
              $resp = @mysqli_query($connect,'SELECT products.product_id, products.product_name,products.product_price,products.product_desc,products.date_set,products.product_sold,products.biddable,products.SellerID,person.username,person.first_name,person.last_name,person.user_id FROM products JOIN  person ON products.SellerID = person.user_id WHERE approval = 1');
              if($resp){
              while($row = mysqli_fetch_array($resp)){
                if((!$row['product_sold']) && ($row['biddable'] == 0)){

                $testing = new Item($row['username'],$row['first_name'],$row['last_name'],$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold']);
                $testing->toHTML();

              }
             }
           }
           echo"</div>";


           echo "<div class = 'listeditems'>
           <h1> Biddable Items on Market </h1>";
           $resp = @mysqli_query($connect,'SELECT products.product_id, products.product_name,products.product_price,products.product_desc,products.date_set,products.product_sold,products.biddable,products.SellerID,person.username,person.first_name,person.last_name,person.user_id FROM products JOIN  person ON products.SellerID = person.user_id  WHERE approval = 1');
           if($resp){
           while($row = mysqli_fetch_array($resp)){
             if((!$row['product_sold']) && ($row['biddable'] == 1)){

             $testing = new BidItem($row['username'],$row['first_name'],$row['last_name'],$row['product_id'],$row['product_name'],$row['product_price'],$row['product_desc'],$row['date_set'],$row['product_sold']);
             $testing->toHTML();

           }
          }
        }
        echo"</div>";

         }
             ?>

          </div>



          </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>
