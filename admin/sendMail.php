<?php
// Include config file
require_once "config.php";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get URL parameter
    $id =  trim($_GET["id"]);
    $consumer_id =  trim($_GET["consumer_id"]);
    
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
                $email = $row["email"];

                if(isset($email) && !empty($email)){
                    // $template_file = "includes/billing-statement.php";
                    $to_email = $email;
                    $subject = "MACWAS Billing Statement";
                    $body = "Hi $name, This is test email send by PHP Script";
                    $headers = "From: MACWAS";
                    // $headers .= "MIME-version: 1.0\r\n";
                    // $headers .= "Content-type: text/html; charset=IOS-8859-1\r\n";

                    // if(file_exists($template_file)){
                    //     $body = file_get_contents($template_file);
                    // }else{
                    //     die('Unable to locate file.');
                    // }
                    
                    if (mail($to_email, $subject, $body, $headers)) {
                        echo "<script>alert('Email sent successfully.')</script>";
                        // header("location: reading.php?consumer_id=$consumer_id");
                        exit();
                    } else {
                        header("location: sent-email.php?success=false");
                        exit();
                    }
                }else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: sent-email.php");
                    exit();
                }
                
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
}else{
    header("location: reading.php?consumer_id=$consumer_id");
    exit();
}