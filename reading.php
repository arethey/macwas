<!DOCTYPE html>

<?php
// Initialize the session
ob_start();
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}else{
    if(!isset($_SESSION["isUpdated"]) || $_SESSION["isUpdated"] !== 1){
        header("location: reset-password.php");
        exit;
    }
}
?>
 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill</title>
    <?php include 'includes/links.php'; ?>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <section class="home-section">
        <nav class="navbar navbar-light bg-white border-bottom">
            <span class="navbar-brand mb-0 h1 d-flex align-items-center">
                <i class='bx bx-menu mr-3' style='cursor: pointer; font-size: 2rem'></i>
                Bill
            </span>
            <?php include 'includes/userMenu.php'; ?>
        </nav>

        <div class="container-fluid py-5">
            <div>
                <?php
                // Include config file
                require_once "config.php";
                
                // Attempt select query execution
                $id = $_SESSION["id"];
                $sql = "SELECT *, (present - previous) as used FROM readings WHERE consumer_id = $id";
                if($result = mysqli_query($link, $sql)){
                    if(mysqli_num_rows($result) > 0){
                        echo '<table class="table table-striped">';
                            echo "<thead>";
                                echo "<tr>";
                                    echo "<th>Year</th>";
                                    echo "<th>Month</th>";
                                    echo "<th>Present</th>";
                                    echo "<th>Previous</th>";
                                    echo "<th>Used</th>";
                                    echo "<th>Status</th>";
                                    echo "<th>Action</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                            while($row = mysqli_fetch_array($result)){
                                $status = $row['status'] == 0 ? 'Pending' : 'Paid';
                                echo "<tr>";
                                    echo "<td class='text-uppercase'>".date_format(date_create($row['reading_date']), 'Y')."</td>";
                                    echo "<td class='text-uppercase'>".date_format(date_create($row['reading_date']), 'F')."</td>";
                                    echo "<td>" . $row['present'] . "</td>";
                                    echo "<td>" . $row['previous'] . "</td>";
                                    echo "<td>" . number_format((float)$row['used'], 2, '.', '') . "</td>";
                                    echo "<td>" . $status . "</td>";
                                    echo "<td>";
                                        echo '<a target="_blank" href="print-reading.php?id='. $row['id'] .'" class="mr-2" title="Print Record" data-toggle="tooltip"><i class="bx bxs-printer"></i></a>';
                                        // echo '<a href="reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" class="mr-2" title="Update Record" data-toggle="tooltip"><i class="bx bxs-pencil" ></i></a>';
                                        // echo '<a onclick="javascript:confirmationDelete($(this));return false;" href="delete-reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><i class="bx bxs-trash-alt" ></i></a>';
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
        </div>
    </section>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>