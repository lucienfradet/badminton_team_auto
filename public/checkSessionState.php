<?php
session_start();
require('openDB.php');


// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    //declare session variables
    $tablename = $_SESSION['table'];
    $username = $_SESSION['username'];

    $queryFetchTeamArray = "
      SELECT team_array
      FROM users
      WHERE username = :username
    ";

    $stmt = $file_db->prepare($queryFetchTeamArray);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $existingTeamArray = $stmt->fetchColumn();

    $existingTeams = json_decode($existingTeamArray, true);

    // Check if $existingTeams is empty
    $response = empty($existingTeams);

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode([
      'isEmpty' => $response,
      'teams' => $existingTeams
    ]);

  } catch (PDOException $e) {
    // Print PDOException message
    echo "Error: " . $e->getMessage();
  }
} else {
    // Handle invalid requests (e.g., direct access to this file)
    echo "Invalid request";
}
?>
