<?php
// Include config file
// require_once "config.php";
 
// Define variables and initialize with empty values
$present = $previous = "";
$present_err = $previous_err = $month_year_err = "";

$consumer_id = $_GET['consumer_id'];

$url = htmlspecialchars($_SERVER["PHP_SELF"]).'?consumer_id='.$_GET["consumer_id"];
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $url = htmlspecialchars($_SERVER["PHP_SELF"]).'?consumer_id='.$_GET["consumer_id"].'&id='.$_GET["id"];
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate present
    $input_present = trim($_POST["present"]);
    if(empty($input_present)){
        $present_err = "Please enter an present.";     
    } else{
        $present = $input_present;
    }
    
    // Validate previous
    $input_previous = trim($_POST["previous"]);
    if(empty($input_previous)){
        $previous_err = "Please enter an previous.";     
    } else{
        $previous = $input_previous;
    }

    $reading_date = date('Y-m-d H:i:s', time());
    // $month_year = date('Y-m', time());
    // echo "<script>alert('$month_year')</script>";
    
    // $sql = "SELECT id FROM readings WHERE DATE_FORMAT(reading_date, '%Y-%m') = ?";
    // if($stmt = mysqli_prepare($link, $sql)){
    //     // Bind variables to the prepared statement as parameters
    //     mysqli_stmt_bind_param($stmt, "s", $month_year);
        
    //     // Attempt to execute the prepared statement
    //     if(mysqli_stmt_execute($stmt)){
    //         /* store result */
    //         mysqli_stmt_store_result($stmt);
            
    //         if(mysqli_stmt_num_rows($stmt) == 1){
    //             $month_year_err = "duplicate record";
    //             echo "<script>alert('Duplicate record.')</script>";
    //         }else{
    //             echo "<script>alert('wa')</script>";
    //         }
    //     } else{
    //         echo "Oops! Something went wrong. Please try again later.";
    //     }

    //     // Close statement
    //     mysqli_stmt_close($stmt);
    // }

    // Check input errors before inserting in database
    if(empty($present_err) && empty($previous_err) && empty($month_year_err)){
        // Prepare an insert statement
        $sql2 = "INSERT INTO readings (consumer_id, reading_date, previous, present, status) VALUES (?, ?, ?, ?, ?)";
        if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
            $sql2 = "UPDATE readings SET consumer_id=?, reading_date=?, previous=?, present=?, status=? WHERE id=?";
        }
         
        if($stmt2 = mysqli_prepare($link, $sql2)){
            // Bind variables to the prepared statement as parameters
            if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
                $id =  trim($_GET["id"]);
                mysqli_stmt_bind_param($stmt2, "sssssi", $param_consumer_id, $param_reading_date, $param_previous, $param_present, $param_status, $id);
            }else{
                mysqli_stmt_bind_param($stmt2, "sssss", $param_consumer_id, $param_reading_date, $param_previous, $param_present, $param_status);
            }
            
            // Set parameters
            $param_consumer_id = $_GET["consumer_id"];
            $param_reading_date = $reading_date;
            $param_previous = $previous;
            $param_present = $present;
            $param_status = 0;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // Records created successfully. Redirect to landing page
                header("location: reading.php?consumer_id=$consumer_id");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt2);
    }
}else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM readings WHERE id = ?";
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
    }
}

// Close connection
// mysqli_close($link);
 echo $month_year_err;
?>

<form action="<?php echo $url ?>" method="post">
    <div class="form-group">
        <label>Present</label>
        <input type="text" name="present" class="form-control <?php echo (!empty($present_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $present; ?>">
        <span class="invalid-feedback"><?php echo $present_err;?></span>
    </div>
    <div class="form-group">
        <label>Previous</label>
        <input type="text" name="previous" class="form-control <?php echo (!empty($previous_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $previous; ?>">
        <span class="invalid-feedback"><?php echo $previous_err;?></span>
    </div>
    <input type="submit" class="btn btn-primary btn-block" value="<?php echo isset($_GET["id"]) && !empty($_GET["id"]) ? 'Update' : 'Submit' ?>">
    <?php
        if(isset($_GET["id"]) && !empty($_GET["id"])){
            ?>
                <a class="btn btn-link btn-block" href="reading.php?consumer_id=<?php echo $consumer_id; ?>">Cancel</a>
            <?php
        }
    ?>
</form>