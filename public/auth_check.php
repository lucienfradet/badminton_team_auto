<?php
session_start();
// Check if user is logged in and has the required session variables
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: login.php');
    exit();
}
?>
