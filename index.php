<?php
// Initialize the session
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

require_once "config.php";
$id = $_SESSION["id"];
$complaints_sql = "SELECT * FROM complaints WHERE consumer_id = $id;";
$complaints_result = mysqli_query($link, $complaints_sql);
$complaints_total = mysqli_num_rows($complaints_result);

// Close connection
mysqli_close($link);
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

        <div class="container py-5">
            <div class="row">
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
        </div>
    </section>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>