<?php
session_start();

session_destroy();

echo "<script>alert('logout successful'); window.location='login.php'; </script>";
?>