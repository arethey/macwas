<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $id =  trim($_GET["id"]);
    
    // Prepare a select statement
    $sql = "SELECT *, (present - previous) as used FROM readings LEFT JOIN consumers ON consumers.id = readings.consumer_id WHERE readings.id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = $id;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $present = $row["present"];
                $previous = $row["previous"];
                $reading_date = $row["reading_date"];

                $name = $row["name"];
                $barangay = $row["barangay"];
                $account_num = $row["account_num"];
                $registration_num = $row["registration_num"];
                $meter_num = $row["meter_num"];
                $type = $row["type"];
                
            } else{
                // URL doesn't contain valid id. Redirect to error page
                header("location: reading.php?consumer_id=$consumer_id");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}  else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: consumer.php");
    exit();
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill</title>
    <?php include 'includes/links.php'; ?>
</head>
<body>
    <div class="container pt-5">
        <?php include 'includes/bill-template.php'; ?>
    </div>
    
    <script type="text/javascript">
      window.onload = function() { window.print(); }
    </script>
</body>
</html>