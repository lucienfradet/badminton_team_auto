<?php
require 'auth_check.php';
require('openDB.php');

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
