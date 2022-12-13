<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "config.php";

$consumers_sql = "SELECT * FROM consumers;";
$consumers_result = mysqli_query($link, $consumers_sql);
$consumers_total = mysqli_num_rows($consumers_result);

$complaints_sql = "SELECT * FROM complaints;";
$complaints_result = mysqli_query($link, $complaints_sql);
$complaints_total = mysqli_num_rows($complaints_result);

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <?php include 'includes/links.php'; ?>
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>

    <section class="home-section">
        <nav class="navbar navbar-light bg-white border-bottom">
            <span class="navbar-brand mb-0 h1 d-flex align-items-center">
                <i class='bx bx-menu mr-3' style='cursor: pointer; font-size: 2rem'></i>
                Dashboard
            </span>
            <?php include 'includes/userMenu.php'; ?>
        </nav>

        <div class="container-fluid py-5">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?php echo $consumers_total; ?></h4>
                                    <small class="mb-0">Consumers</small>
                                </div>
                                <i class='bx bx-user bx-md'></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-0"><?php echo $complaints_total; ?></h4>
                                    <small class="mb-0">Complaints</small>
                                </div>
                                <i class='bx bx-message-rounded-dots bx-md'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <h4>Overdue Billing Statement</h4>
                <div>
                <?php
                        $currDate = GETDATE();
                        $sql = "SELECT *, (present - previous) AS used, consumers.id  AS consumer_id, readings.id AS reading_id FROM readings LEFT JOIN consumers ON readings.consumer_id = consumers.id WHERE DATE(readings.due_date) < DATE(NOW()) AND readings.status = 0";
                        if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>Name</th>";
                                        echo "<th>Meter No.</th>";
                                        echo "<th>Date of Disconnection</th>";
                                        echo "<th>Due Date</th>";
                                        echo "<th>Present</th>";
                                        echo "<th>Previous</th>";
                                        echo "<th>Used</th>";
                                        echo "<th>Action</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    $status = $row['status'] == 0 ? 'Pending' : 'Paid';
                                    echo "<tr>";
                                        echo "<td>". $row['name'] ."</td>";
                                        echo "<td>" . $row['meter_num'] . "</td>";
                                        echo "<td>" . date("F j, Y", strtotime($row['due_date'] ." +15 day") ) . "</td>";
                                        echo "<td class='text-uppercase'>".date_format(date_create($row['due_date']), 'F j, Y')."</td>";
                                        echo "<td>" . $row['present'] . "</td>";
                                        echo "<td>" . $row['previous'] . "</td>";
                                        echo "<td>" . number_format((float)$row['used'], 2, '.', '') . "</td>";
                                        echo "<td>";
                                        ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="bx bxs-printer"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                                    <?php
                                                        echo '<a target="_blank" href="print-reading.php?id='. $row['reading_id'] .'" class="dropdown-item" title="Print Billing Statement" data-toggle="tooltip">Billing Statement</a>';
                                                        echo '<a target="_blank" href="print-nod.php?id='. $row['reading_id'] .'" class="dropdown-item" title="Print Billing Statement" data-toggle="tooltip">Notice of Disconnection</a>';
                                                    ?>
                                                </div>
                                            </div>
                                        <?php
                                        
                                            // echo '<a target="_blank" href="sendMail.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" class="mr-2" title="Send Billing Statement" data-toggle="tooltip"><i class="bx bx-sm bx-mail-send"></i></a>';
                                            // echo '<a target="_blank" href="print-reading.php?id='. $row['reading_id'] .'" class="mr-2" title="Print Billing Statement" data-toggle="tooltip"><i class="bx bx-sm bxs-printer"></i></a>';
                                            // echo '<a href="reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" class="mr-2" title="Update Record" data-toggle="tooltip"><i class="bx bx-sm bxs-pencil" ></i></a>';
                                            // echo '<a onclick="javascript:confirmationDelete($(this));return false;" href="delete-reading.php?consumer_id='.$_GET["consumer_id"].'&id='. $row['id'] .'" title="Delete Record" data-toggle="tooltip"><i class="bx bx-sm bxs-trash-alt" ></i></a>';
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
                    ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>

<?php
// Close connection
mysqli_close($link);
?>