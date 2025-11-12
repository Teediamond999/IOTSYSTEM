<?php
include 'header.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
include 'connect.php';
?>
<div class="main-container">
    <div class="pd-ltr-20">
        <div class="card-box pd-20 height-100-p mb-30">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <img src="vendors/images/banner-img.png" alt="" />
                </div>
                <div class="col-md-8">
                    <h4 class="font-20 weight-500 mb-10 text-capitalize">
                        Welcome back
                        <div class="weight-600 font-30 text-blue"><?php echo $row['name']; ?>!</div>
                    </h4>
                </div>
            </div>
        </div>
        </div> 
<?php
include 'footer.php';?>