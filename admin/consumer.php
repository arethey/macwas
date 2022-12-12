<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consumers</title>
    <?php include 'includes/links.php'; ?>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <section class="home-section">
        <nav class="navbar navbar-light bg-white border-bottom">
            <span class="navbar-brand mb-0 h1 d-flex align-items-center">
                <i class='bx bx-menu mr-3' style='cursor: pointer; font-size: 2rem'></i>
                Consumers
            </span>
            <?php include 'includes/userMenu.php'; ?>
        </nav>

        <div class="container-fluid py-5">
            <a href="new-consumer.php" class="btn btn-primary btn-sm mb-3"><i class='bx bx-plus' ></i> New</a>
            
            <?php
            // Include config file
            require_once "config.php";
            
            // Attempt select query execution
            $sql = "SELECT * FROM consumers";
            if($result = mysqli_query($link, $sql)){
                if(mysqli_num_rows($result) > 0){
                    echo '<table class="table table-striped">';
                        echo "<thead>";
                            echo "<tr>";
                                // echo "<th>#</th>";
                                echo "<th>Name</th>";
                                echo "<th>Email</th>";
                                echo "<th>Phone</th>";
                                echo "<th>Address</th>";
                                echo "<th>Account No.</th>";
                                echo "<th>Registration No.</th>";
                                echo "<th>Meter No.</th>";
                                echo "<th>Type</th>";
                                echo "<th>Action</th>";
                            echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        while($row = mysqli_fetch_array($result)){
                            $email = $row['email'];
                            $phone = "+63".$row['phone'];
                            if(empty($row['email'])){
                                $email = 'N/A';
                            }

                            echo "<tr>";
                            if($row['status'] == 0){
                                echo '<td><a class="text-danger" href="reading.php?consumer_id='. $row['id'] .'">'. $row['name'] .'</a></td>';
                            }else{
                                echo '<td><a class="text-success" href="reading.php?consumer_id='. $row['id'] .'">'. $row['name'] .'</a></td>';
                            }
                                echo "<td>" . $email . "</td>";
                                echo "<td>" . $phone . "</td>";
                                echo "<td>" . $row['barangay'] . "</td>";
                                echo "<td>" . $row['account_num'] . "</td>";
                                echo "<td>" . $row['registration_num'] . "</td>";
                                echo "<td>" . $row['meter_num'] . "</td>";
                                echo "<td>" . $row['type'] . "</td>";
                                echo "<td>";
                                    // echo '<a href="read.php?id='. $row['id'] .'" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                    echo '<a href="update-consumer.php?id='. $row['id'] .'" class="mr-2" title="Update Record" data-toggle="tooltip"><i class="bx bx-sm bxs-pencil" ></i></a>';
                                    echo '<a onclick="javascript:confirmationDelete($(this));return false;" href="delete-consumer.php?id='. $row['id'] .'" class="mr-2" title="Delete Record" data-toggle="tooltip"><i class="bx bx-sm bxs-trash-alt" ></i></a>';
                                    if($row['status'] == 0){
                                        echo '<a onclick="javascript:confirmationStatus($(this), 1);return false;" href="status-consumer.php?id='. $row['id'] .'&status=1" title="Enable Record" data-toggle="tooltip"><i class="bx bx-sm bx-show"></i></a>';
                                    }else{
                                        echo '<a onclick="javascript:confirmationStatus($(this), 0);return false;" href="status-consumer.php?id='. $row['id'] .'&status=0" title="Disable Record" data-toggle="tooltip"><i class="bx bx-sm bx-hide"></i></a>';
                                    }
                                echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";                            
                    echo "</table>";
                    // Free result set
                    mysqli_free_result($result);
                } else{
                    echo '<div class="alert alert-danger"><em>No records were found.</em></div>';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close connection
            mysqli_close($link);
            ?>
        </div>
    </section>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>