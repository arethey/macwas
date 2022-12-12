<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}
 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$meter_num = $password = "";
$meter_num_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if meter_num is empty
    if(empty(trim($_POST["meter_num"]))){
        $meter_num_err = "Please enter meter_num.";
    } else{
        $meter_num = trim($_POST["meter_num"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($meter_num_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, status, password, isUpdated FROM consumers WHERE meter_num = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_meter_num);
            
            // Set parameters
            $param_meter_num = $meter_num;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if meter_num exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $status, $hashed_password, $isUpdated);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            if($status === 0){
                                $login_err = "Invalid account please contact system administrator.";
                            }else{
                                // Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["meter_num"] = $meter_num;
                                $_SESSION["isUpdated"] = $isUpdated;                            
                                
                                // Redirect user to welcome page
                                header("location: index.php");
                            }
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid meter_num or password.";
                        }
                    }
                } else{
                    // meter_num doesn't exist, display a generic error message
                    $login_err = "Invalid meter_num or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container pt-5">
        <div class="w-100 m-auto bg-white border rounded p-3" style="max-width: 400px">
            <h2>Login</h2>
            <p>Please fill in your credentials to login.</p>

            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Meter No.</label>
                    <input type="text" name="meter_num" class="form-control <?php echo (!empty($meter_num_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $meter_num; ?>">
                    <span class="invalid-feedback"><?php echo $meter_num_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <!-- <p>Don't have an account? <a href="register.php">Sign up now</a>.</p> -->
            </form>
        </div>
    </div>
</body>
</html>