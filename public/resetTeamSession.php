<?php
session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

$tablename = $_SESSION['table'];
$username = $_SESSION['username'];
$teamArray = [];
$courtArray = [];

// Store the team information in the database
$updateTeamQuery = "
  UPDATE users 
  SET team_array = :teamArray,
      court_array = :courtArray
  WHERE username = :username
";

$teamArrayEncoded = json_encode($teamArray);
$courtArrayEncoded = json_encode($courtArray);

$stmt = $file_db->prepare($updateTeamQuery);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':teamArray', $teamArrayEncoded);
$stmt->bindParam(':courtArray', $courtArrayEncoded);
$stmt->execute();

?>
