<!DOCTYPE html>

<?php
// Initialize the session
ob_start();
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(!isset($_GET["consumer_id"]) || empty(trim($_GET["consumer_id"]))){
    header("location: consumer.php");
    exit;
}else{
    require_once "config.php";

    // Get URL parameter
    $id =  trim($_GET["consumer_id"]);
        
    // Prepare a select statement
    $sql3 = "SELECT * FROM consumers WHERE id = ?";
    if($stmt3 = mysqli_prepare($link, $sql3)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt3, "i", $param_id);
        
        // Set parameters
        $param_id = $id;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt3)){
            $result = mysqli_stmt_get_result($stmt3);

            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $name = $row["name"];
                $barangay = $row["barangay"];
                $account_num = $row["account_num"];
                $registration_num = $row["registration_num"];
                $meter_num = $row["meter_num"];
                $type = $row["type"];
            } else{
                // URL doesn't contain valid id. Redirect to error page
                header("location: consumer.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Close statement
    mysqli_stmt_close($stmt3);
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
            <div class="row w-100">
                <div class="col-12 col-lg-9">
                    <!-- <a target="_blank" href="print-reading.php?consumer_id=<?php echo $_GET["consumer_id"] ?>" class="btn btn-primary btn-sm mb-3"><i class='bx bxs-printer'></i> Print</a> -->
                
                    <?php
                    // Include config file
                    // require_once "config.php";
                    
                    // Attempt select query execution
                    $id = $_GET["consumer_id"];
                    $sql = "SELECT *, (present - previous) as used FROM readings WHERE consumer_id = $id";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        // echo "<th>#</th>";
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
                                        // echo "<td>" . $row['id'] . "</td>";
                                        echo "<td class='text-uppercase'>".date_format(date_create($row['reading_date']), 'Y')."</td>";
                                        echo "<td class='text-uppercase'>".date_format(date_create($row['reading_date']), 'F')."</td>";
                                        echo "<td>" . $row['present'] . "</td>";
                                        echo "<td>" . $row['previous'] . "</td>";
                                        echo "<td>" . number_format((float)$row['used'], 2, '.', '') . "</td>";
                                        echo "<td>" . $status . "</td>";
                                        echo "<td>";
                                            echo '<a target="_blank" href="sendMail.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" class="mr-2" title="Send Billing Statement" data-toggle="tooltip"><i class="bx bx-sm bx-mail-send"></i></a>';
                                            echo '<a target="_blank" href="print-reading.php?id='. $row['id'] .'" class="mr-2" title="Print Billing Statement" data-toggle="tooltip"><i class="bx bx-sm bxs-printer"></i></a>';
                                            echo '<a href="reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" class="mr-2" title="Update Record" data-toggle="tooltip"><i class="bx bx-sm bxs-pencil" ></i></a>';
                                            echo '<a onclick="javascript:confirmationDelete($(this));return false;" href="delete-reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><i class="bx bx-sm bxs-trash-alt" ></i></a>';
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
                    // mysqli_close($link);
                    ?>
                </div>
                <div class="col-12 col-lg-3 card">
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Name: </small><span><?php echo $name; ?></span></p>
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Barangay: </small><span><?php echo $barangay; ?></span></p>
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Account #: </small><span><?php echo $account_num; ?></span></p>
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Registration #: </small><span><?php echo $registration_num; ?></span></p>
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Meter #: </small><span><?php echo $meter_num; ?></span></p>
                            <p class="mb-0 d-flex align-items-center justify-content-between"><small class="text-muted">Type: </small><span><?php echo $type; ?></span></p>
                        </div>    
                        <?php include 'forms/reading-form.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>