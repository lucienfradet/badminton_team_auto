
<?php
session_start();
require('openDB.php');


// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $player1 = $_POST['player1'];
  $player2 = $_POST['player2'];

  try {
    //declare session variables
    $tablename = $_SESSION['table'];
    $username = $_SESSION['username'];
    
    // fetch previous teams
    $queryFetchTeamArray = "
      SELECT team_array
      FROM " . $tablename . "
      WHERE username = :username
    ";

    $stmt = $file_db->prepare($queryFetchTeamArray);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $existingTeamArray = $stmt->fetchColumn();

    $existingTeams = json_decode($existingTeamArray, true);
    
    //create team with null for bench people
    $newTeam1 = [
      'player1' => $player1,
      'player2' => null
    ];
    $newTeam2 = [
      'player1' => $player2,
      'player2' => null
    ];

    $teams = [$newTeam1, $newTeam2];

    // add team into team_array of db
    if (is_array($existingTeams)) {
      $combinedTeams = array_merge($existingTeams, $teams);
    }
    else {
      $combinedTeams = $teams;
    }

    // Store the team information in the database
    $updateTeamQuery = "
      UPDATE  " . $tablename . " 
      SET team_array = :teamArray
      WHERE username = :username
    ";

    $stmt = $file_db->prepare($updateTeamQuery);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':teamArray', json_encode($combinedTeams));
    $stmt->execute();

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode([
      'teams' => $teams,
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
