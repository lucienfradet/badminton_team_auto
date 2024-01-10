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
  $numCourts = $_POST['numCourts'];
  $algorithm = $_POST['algorithm'];

  try {
    //declare session variables
    $tablename = $_SESSION['table'];
    $username = $_SESSION['username'];

    // Fetch active players from the 'players' table
    $queryFetchActivePlayers = "
      SELECT * FROM players 
      WHERE active = 1 AND tablename = :tablename
    ";

    $stmt = $file_db->prepare($queryFetchActivePlayers);
    $stmt->bindParam(':tablename', $tablename, PDO::PARAM_STR);
    $stmt->execute();

    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    // !!!!!!!!!!!!!!!! TEAM CREATION !!!!!!!!!!!!!!!!!!!!!
    $numItterations = 1000;
    $teamsArray = [];

    // Create many teams distributions
    for ($i = 0; $i < $numItterations; $i++) {
      shuffle($players);
      $teams = [];
      for ($j = 0; $j < count($players); $j += 2) {
        $team = [
            'player1' => $players[$j]['name'],
            'player2' => ($j + 1 < count($players)) ? $players[$j + 1]['name'] : null
        ];
        // Store the team composition in the $teams array
        $teams[] = $team;
      }
      $teamsArray[] = $teams;
    }
    
    // test the teams with existing previous teams
    $index = 0;
    $selectedIndex = 0;
    $previousBestScore = 0;
    foreach ($teamsArray as $teams) {
      $score = 0;
      foreach ($teams as $team) {
        if (is_array($existingTeams) and count($existingTeams) > 0) {
          foreach ($existingTeams as $existingTeam) {
            if (is_array($existingTeam)) {
              // Compare the teams and increment the counter if they are the same (regardless of order)
              if (
                  ($team['player1'] === $existingTeam['player1'] && $team['player2'] === $existingTeam['player2']) ||
                  ($team['player1'] === $existingTeam['player2'] && $team['player2'] === $existingTeam['player1'])
              ) {
                  $score++;
              }
            }
          }
        }
      }
      if ($score < $previousBestScore) {
        $selectedIndex = $index;
        $previousBestScore = $score;
      }
      $index++;
    }
    
    $teams = $teamsArray[$selectedIndex];

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
      'courtNumber' => $numCourts,
      'algorithm' => $algorithm
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
