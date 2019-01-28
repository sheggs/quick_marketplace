<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="admin.css" type="text/css">
  <style>
    nav{
      margin:0 0;
      top:0;
      float:left;
      display: absolute;
      z-index: 1;
      width: 10vw;
      height: 100vh;
    }
    .test{
      width: 10vw;
      text-align: center;
      background-color: #888c8a;
    }

    li{
      max-width: 10vw;
      margin: auto;
    }
  </style>
  <title>Document</title>
</head>
<body>
  <header class = "bg-dark">
    <div class = "btn-group" style = "float:right;">
      <a href = "../home.php" class = 'btn btn-secondary'>Home</a>
      <a href = "../me.php" class = 'btn btn-secondary'>Account Panel</a>
      <a href = "../endSession.php" class = 'btn btn-secondary'>Log Out</a>
    </div>
    <h1>MarketPlace</h1>



  </header>

  <nav  class="sidebar-nav navbar-dark bg-dark" style = " min-height:100%;position:relative;">
  <a  class="test navbar-brand" href="admin.php">Admin Panel</a>
  <ul class = "navbar-nav">
    <li class = "nav-item active"><a href = "admin.php?approvalproducts=1">Approve Products</a></li>
    <li class = "nav-item active"><a href= "admin.php?user=1">Users</a></li>
    <li class = "nav-item active"><a href= "admin.php?admin=1">Admins</a></li>
    <li class = "nav-item"><a href = "admin.php?salary=1">P60</a></li>
  </ul>
  </nav>
  <?php

    require_once('../php/connect.php');
    require_once('../php/generalFunctions.php');
    session_start();
    global $connect;
    // Redirects if you are banned
    redirectBan($_SESSION['id']);
    // Checks if we are approving products.
    if(isset($_GET['approvalproducts'])){
      // Querys to get the data about the product and user selling the product.
      $resp = @mysqli_query($connect,'SELECT *  FROM products JOIN person ON products.SellerID = person.user_id WHERE products.approval = 0 ');
      // Creating a table about all the products and seller info. And adding options to approve or decline.
      echo "<table class = 'table_admin'><tr>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>User Name</th>
              <th>Full Name</th>
              <th>Price</th>
              <th>Approve?</th>
            </tr>";
      while($row = @mysqli_fetch_array($resp)){
          echo "<div class = 'table_wrapper'><tr><td>".$row['product_id']."</td>";
          echo "<td>".$row['product_name']."</td>";
          echo "<td>".$row['username']."</td>";
          echo "<td>".$row['first_name']. " ".$row['last_name']."</td>";
          echo "<td>£".$row['product_price']."</td>";
          echo "<td><form method = 'POST' action = 'admin_sql.php'>
          <input type = 'hidden' value = 'approval' name = 'approval'>
          <input type ='hidden' value =  \"".$row['product_name']." \" name = 'product_name'>
          <input type ='hidden' value =  \"".$row['product_id']." \" name = 'product_id'>
          <input type = 'submit' value = 'Approve!' name = 'approved' class = 'btn btn-primary override'>
          <input type = 'submit' value = 'Decline!' name = 'declined' class = 'btn btn-danger'>
          </form></td></tr>";


      }
      echo "</div></table>";
    }
    // Checking if we are going to show an error
    elseif (isset($_GET['error'])) {
      // Display an error.
      echo "<div class = 'jumbotron'>
        <h1 class = 'display-1' style:'color:red;'> Error </h1><hr>";
      if($_GET['error'] == 'data'){
        echo "<p class = 'display-4' style = 'font-size: 30px;'> The data you have inputted is either a duplicate or has an error!</p>";
      }
      echo "</div>";
    }
    // Checking we are going to edit user data.
    elseif(isset($_GET['user'])){
      // Displaying the user table.
      echo "<table class = 'table_admin'><tr>
              <th>User ID</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Balance</th>
              <th>Options</th>
            </tr>";
            $currentUser = $_SESSION['id'];
      // Here we obtain the data about each user.
      $query = mysqli_query($connect,"SELECT *  FROM person JOIN user ON person.user_id = user.user_id");

      // Looping through each row.
      while($row = mysqli_fetch_array($query)){
        $target = (int)$row['user_id'];
        // Here we are displaying a table of each user with options next to it
        echo "<tr ><td>".$row['user_id']."</td>";
        echo "<td>".$row['username']."</td>";
        echo "<td>".$row['first_name']. " ".$row['last_name']."</td>";
        echo "<td>£".$row['account_balance']."</td>";
        echo "<td style = 'padding: 10px;'>
        <form style = 'display:inline;' method = 'POST' action = 'admin_sql.php'><input type='hidden' name = 'userid' value = ".$row['user_id'].">";
        // Getting the current rank of the user.
        $currentRank = getAdminRank($target);
            echo "<select name = 'change_rank' class = 'form-control-sm'><option>$currentRank</option>";
            // Getting all the ranks.
            $subquery = mysqli_query($connect,"SELECT admin_rank,permission_value FROM adminRanks");
            // Loop through all the rows.
            while($subquery_row = mysqli_fetch_array($subquery)){
              // Checking if the rank is higher than the current users rank.
              if(($subquery_row['permission_value'] <= getAdminPermission($currentUser)) && ($subquery_row['admin_rank'] != $currentRank)){
                // Add it to the drop down menu.
                echo "<option>".$subquery_row['admin_rank']."</option>";
              }
            }
            echo"</select>";
            // Checking if the current user is trying to change their own rank.
            if($row['user_id'] == $currentUser){
              echo "<input type = 'submit' value = \"Can't change your own rank or rank above.\" name = 'approved' class = 'btn btn-danger' disabled><hr style='background-color:white;margin:3px;width:100%;' >";

            }else{
              echo "<input type = 'submit' value = 'Change Rank' name = 'approved' class = 'btn btn-danger'><hr style='background-color:white;margin:3px;width:100%;'>";
            }
          echo "</form>
        </div>
        <form style = 'display: inline;' method = 'POST' action = 'admin.php?account_settings=1'>
        <input type ='hidden' value =  \"".$row['user_id']." \" name = 'user_id'>
        <input type = 'submit' value = 'Change Details' name = 'approved' class = 'btn btn-danger' style = 'width:100%;'>

        </form><hr style='background-color:white;margin:3px;'>

        <form method = 'POST' action = 'admin_sql.php'>
        <input type = 'hidden' value = 'ban_user' name = 'ban_user'>
        <input type ='hidden' value =  \"".$row['user_id']." \" name = 'user_id'>";
        // Display a unban option
        if(isBanned($row['user_id'])){
          echo"<input type = 'submit' value = 'UnBan' name = 'UnBan' class = 'btn btn-danger'  style = 'width:100%;'>";

        }
        // Display ban option.
        else{
          echo"<input type = 'submit' value = 'Ban' name = 'Ban' class = 'btn btn-danger'  style = 'width:100%;'>";
        }
        echo"</form></td></tr>";
      }

    }
    // Checks if we are going to display the admin table.
    elseif(isset($_GET['admin'])){
      // DIsplay a table about all the admins
      echo "<table class = 'table_admin'><tr>
              <th>User ID</th>
              <th>Username</th>
              <th>Full Name</th>
              <th>Balance</th>
              <th>Options</th>
            </tr>";
            $currentUser = $_SESSION['id'];
      // Here we obtain the data about each user.
      $query = mysqli_query($connect,"SELECT *  FROM person JOIN admin ON person.user_id = admin.user_id");
      // Looping through each row.
      while($row = mysqli_fetch_array($query)){
        // Storing the user we are targeting.
        $target = (int)$row['user_id'];

        // Here we are displaying a table of each user with options next to it
        echo "<tr ><td>".$row['user_id']."</td>";
        echo "<td>".$row['username']."</td>";
        echo "<td>".$row['first_name']. " ".$row['last_name']."</td>";
        echo "<td>£".$row['account_balance']."</td>";
        // = We are now displaying an option in which the admin can change the rank of the user up to their own rank!. This is done using Permission Values.
        echo "<td style = 'padding: 10px;'>
        <form style = 'display:inline;' method = 'POST' action = 'admin_sql.php'><input type='hidden' name = 'userid' value = ".$row['user_id'].">";
          // Getting the current rank of the user.
            $currentRank = getAdminRank($target);
            // Displaying the drop down box. Setting the first value to the value of the targets rank.
            echo "<select name = 'change_rank' class = 'form-control-sm'><option>$currentRank</option>";
            // Here we are obtaining all the rank name and their permission values.
            $subquery = mysqli_query($connect,"SELECT admin_rank,permission_value FROM adminRanks");
            // Looping through all the rows.
            while($subquery_row = mysqli_fetch_array($subquery)){
              // Checking the current users permissions allow the to give this person a certain rank.
              if($subquery_row['permission_value'] <= getAdminPermission($currentUser)  && ($subquery_row['admin_rank'] != $currentRank)){
                // Adds the rank name as an option.
                echo "<option>".$subquery_row['admin_rank']."</option>";
              }
            }
            echo"</select>";
            // Checking if the target user's permission is equal to or greater than the current users.
            if(getAdminPermission($row['user_id']) >= getAdminPermission($currentUser)){
              echo "<input type = 'submit' value = \"Change Rank\" name = 'approved' class = 'btn btn-danger' disabled><hr style='background-color:white;margin:3px;width:100%;' >";
            }
            else{
              echo "<input type = 'submit' value = 'Change Rank' name = 'approved' class = 'btn btn-danger'><hr style='background-color:white;margin:3px;width:100%;'>";
            }

          echo "</form>
        </div>
        <form style = 'display: inline;' method = 'POST' action = 'admin.php?account_settings=1'>
        <input type ='hidden' value =  \"".$row['user_id']." \" name = 'user_id'>";
          echo "<input type = 'submit' value = 'Change Details' name = 'approved' class = 'btn btn-danger' style = 'width:100%;'>
        </form><hr style='background-color:white;margin:3px;'>";

        echo "<form style = 'display: inline;' method = 'POST' action = 'admin_sql.php'><input type = 'hidden' name = 'userid' value = \"".$row['user_id']."\">";
        if(getAdminPermission($row['user_id']) >= getAdminPermission($currentUser)){
          echo "<input type = 'submit'class = 'btn btn-danger' value = \"Demote to User!\" name = 'demote' style = 'width:100%;' disabled><hr style='background-color:white;margin:3px;width:100%;'>";
        }
        else{
          echo "<input type = 'submit'class = 'btn btn-danger' value = \"Demote to User!\" name = 'demote' style = 'width:100%;'><hr style='background-color:white;margin:3px;width:100%;'>";
        }
          echo "</form>";

      echo"<form method = 'POST' action = 'admin_sql.php'>
        <input type = 'hidden' value = 'ban_user' name = 'ban_user'>
        <input type ='hidden' value =  \"".$row['user_id']." \" name = 'user_id'>";
        // Checking if the target user's permission is equal to or greater than the current users.
        if(getAdminPermission($row['user_id']) >= getAdminPermission($currentUser)){
          //Checking if the user is banned.
          if(isBanned($row['user_id'])){
            echo"<input type = 'submit' value = 'UnBan' name = 'UnBan' class = 'btn btn-danger'  style = 'width:100%;' disabled>";

          }else{
            echo"<input type = 'submit' value = 'Ban' name = 'Ban' class = 'btn btn-danger'  style = 'width:100%;' disabled>";
          }
          echo"</form></td></tr>";
          }
        else{
          //Checking if the user is banned.
          if(isBanned($row['user_id'])){
            echo"<input type = 'submit' value = 'UnBan' name = 'UnBan' class = 'btn btn-danger'  style = 'width:100%;' >";

          }else{
            echo"<input type = 'submit' value = 'Ban' name = 'Ban' class = 'btn btn-danger'  style = 'width:100%;' >";
          }
          echo"</form></td></tr>";
          }
      }

    }

    // Checking if we are going to change the account settings
    elseif(isset($_GET['account_settings'])){
      // Querying through all the data for this user.
      $query = mysqli_query($connect,"SELECT *  FROM person WHERE user_id = ".$_POST['user_id']);
      // Getting the first row.
      $row = mysqli_fetch_array($query);
      $banned = 'False';
      // Checking if the person is banned.
      if(isBanned($_POST['user_id'])){
        $banned = 'True';
      }
      // Displaying the HTML of the users data and a form to change the data.
      echo "<div class =\"AccountModification\">
      <h1>Account Settings</h1><hr>
        <form method = 'POST' action = 'admin_sql.php' class = 'dataInputAccount'>
        <input type = 'hidden' name = 'account_options' value = '1'>

        <div class = 'paddingContent'>
            <label>Username: </label>
            <input name = 'username'  value = \"".$row['username']."\" ><br>
            <label>Email: </label>
            <input  name = 'email'  value = \"".$row['email']."\" ><br>
            <input type ='hidden' value =  \"".$row['user_id']." \" name = 'user_id'>
            <div class = 'input-group mb-3'>
            <label>Account Balance: </label>
              <div class='input-group-prepend'>
              <span class='input-group-text' id='basic-addon1'>£</span>
              </div>
              <input name = 'account_balance' type = 'text' value = \"".$row['account_balance']."\"><br>

            </div>
            <label>Banned: </label>

            <input style = 'background:#DCDCDC;border:none;' value = $banned readonly>

            <div style = 'padding: 2vh;'>
              <input style = 'display:block;' type = 'submit' value = 'Change Details' name = 'approved' class = 'btn btn-danger'>
            </div>
          </div>
        </form>

      </div>";

    }
    // If we have chosen the Salary section.
    elseif(isset($_GET['salary'])){
      // Getting the current user
      $currentUser = $_SESSION['id'];
      // Querying for salary info.
      $query = mysqli_query($connect,"SELECT admin.position,admin.user_id,adminRanks.salary FROM admin JOIN adminRanks ON admin.position = adminRanks.admin_rank WHERE admin.user_id = ".$currentUser);


      // Getting the first row.
      $row = mysqli_fetch_array($query);
      // Storing the salary amount.
      $salary = $row['salary'];
      // Storing data needed.
      $personalAllowance = 9300;
      $taxableSalary = $salary - $personalAllowance;
      // Creating the table.
      echo "<table class = 'table_admin'><tr>
              <th></th>
              <th>Yearly Income</th>
              <th>Yearly Deductions</th>
              <th>Calculations</th>
              <th>Total</th>
            </tr>";
            echo "<tr>
            <th>Salary:</th>
            <td>$$salary</td>
            <td></td>
            <td></td>
            <td></td>";
            echo "<tr>
            <th>Personal Allowance:</th>
            <td></td>
            <td></td>
            <td><b>-£$personalAllowance<b></td>
            <td></td>";
            echo "<tr>
            <th>Taxable Salary:</th>
            <td></td>
            <td></td>
            <td></td>
            <td><b>£$taxableSalary</b></td>";
      //Checking if the person gets paid 32000.
      if($taxableSalary > 32000){
        // Caluclate Tax for those who get paid more than 32000
        $remainingAmountTax = $taxableSalary - 32000;
        $tax40 = 32000 * 0.4;
        echo "<tr>
        <th>20% tax on £32000: </th>
        <td></td>
        <td><b>-£$tax40</b></td>
        <td></td>
        <td></td>";

        $tax20 = $remainingAmountTax * 0.2;
        echo "<tr>
          <th>40% tax on $remainingAmountTax: </th>
          <td></td>
          <td><b>-£$tax20</b></td>
          <td></td>
          <td></td>";
        $finalIncome = $salary - $tax40 - $tax20;
        echo "<tr>
        <th>Total Final Income </th>
        <td></td>
        <td><b></b></td>
        <td>$salary - $tax40 - $tax20 =   </td>
        <td><b>£$finalIncome</b></td>";


        echo"</table>";

      }else{
        $tax40 = 0.4 * $taxableSalary;
        echo "<tr>
          <th>40% tax on $taxableSalary: </th>
          <td></td>
          <td><b>-£$tax20</b></td>
          <td></td>
          <td></td>";
      }
    }
    else{
      // Checking if there is GET method for success so we can display a message for the user.
      if(isset($_GET['success'])){
        echo '<div style="margin-left: 10vw;" class="alert alert-secondary" role="alert">
        <span class="badge badge-primary">SUCCESS</span>Query successfully applied
        </div>';
      }
      // Now we are going to display the main admin page HTML.
      echo "<div class = 'wrapper2'>
         <div class = 'displayNoApprovals'>
           <h1>Pending Approvals</h1><hr>";
              // Getting the number of approvals.
               $resp = @mysqli_query($connect,'SELECT COUNT(*) FROM products WHERE approval = 0');
               $row = @mysqli_fetch_array($resp);
               echo('<p style="font-size:30px;color:red;"> '.$row['COUNT(*)'].'</p>');
              // Getting the total number of users.
          echo ("
         </div>
         <div class = 'displayNoUsers'>
           <h1>Number of Users</h1><hr>
           ");
             global $connect;
             // Getting the number of users.
             $resp = @mysqli_query($connect,'SELECT COUNT(*) FROM person');
             $row = @mysqli_fetch_array($resp);
             echo("<p style='font-size:50px;color:red;''>".$row['COUNT(*)']."</p>");
        // Displaying the number of biddable products.
      echo("</div>
         <div class = 'displayNoBiddableProducts'>
           <h1 style = 'font-size: 30px;'>Number of Biddable Products Total.</h1><hr> ");
           // Query for the number of biddable products.
             require_once('../php/connect.php');
             global $connect;
             $resp = @mysqli_query($connect,'SELECT COUNT(*) FROM biddableproducts');
             $row = @mysqli_fetch_array($resp);
             echo('<p style="font-size:30px;color:red;"> '.$row['COUNT(*)'].'</p>');
             // DIsplaying the number of buy now products.
        echo("
         </div>
         <div class = 'displayNoPurchaseProducts'>
           <h1 style = 'font-size: 30px;'>Number of Instant Sell products.</h1><hr>");
           // Querying for the number of buy now products.
             require_once('../php/connect.php');
               global $connect;
               $resp = @mysqli_query($connect,'SELECT COUNT(*) FROM buynowproducts');
               $row = @mysqli_fetch_array($resp);
               echo('<p style="font-size:30px;color:red;"> '.$row['COUNT(*)'].'</p>');
               // Now displaying the admin logs.
        echo"
         </div>
         </div>

         <div class = 'displayAdminLogs'>
           <h1>Admin Logs</h1><hr>";

             require_once('../php/connect.php');
               global $connect;
               // Querying the admin logs and the admins that are linked to the log.
               $resp = @mysqli_query($connect,"SELECT * FROM person AS T1 JOIN (SELECT *  FROM adminLog JOIN admin USING(admin_id)) AS T2 USING(user_id)");
               // displaying a table
               echo "<table style = 'width: 80vw;margin-left:0vw;'class = 'table_admin'><tr>
                       <th>Log ID</th>
                       <th>Log Text</th>
                       <th>Admin Username</th>
                       <th>Admin ID</th>
                       <th>User ID</th>
                     </tr>";
              // Displaying the row.
               while($row = @mysqli_fetch_array($resp)){
                 echo "<tr><td>".$row['log_id']."</td>";
                 echo "<td>".$row['log_text']."</td>";
                 echo "<td>".$row['username']."</td>";
                 echo "<td>".$row['admin_id']."</td>";
                 echo "<td>".$row['user_id']."</td>";

               }

      echo"
       </div> ";
    }
   ?>


   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
