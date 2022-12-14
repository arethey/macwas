<?php

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $id =  trim($_GET["id"]);
    
    $sql = "SELECT *, (present - previous) as used, readings.status as reading_status FROM readings LEFT JOIN consumers ON consumers.id = readings.consumer_id WHERE readings.id = ?";
    if($stmt = mysqli_prepare($link, $sql)){
        
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        $param_id = $id;
        
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) == 1){
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

                if(isset($row['email']) && !empty($row['email'])){
                    $to_email = $row['email'];
                    $subject = "MACWAS Notice of Disconnection";

                    $headers = array(
                        "MIME-Version" => "1.0",
                        "Content-type" => "text/html;charset=UTF-8",
                        "From" => "MACWAS"
                    );

                    ob_start();
                    include("templates/notice.php");
                    $message = ob_get_contents();
                    ob_get_clean();

                    $send = mail($to_email, $subject, $message, $headers);
                    echo ($send ? "Email sent successfully." : "There was an error.");
                }
                
            } else{
                header("location: reading.php?consumer_id=$consumer_id");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    mysqli_stmt_close($stmt);
}  else{
    header("location: consumer.php");
    exit();
}