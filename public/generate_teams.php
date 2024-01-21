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
      FROM users
      WHERE username = :username
    ";

    $stmt = $file_db->prepare($queryFetchTeamArray);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $existingTeamArray = $stmt->fetchColumn();

    $existingTeams = json_decode($existingTeamArray, true);

    // !!!!!!!!!!!!!!!! TEAM CREATION !!!!!!!!!!!!!!!!!!!!!

    // !!!!!!!!!! RANDOM AGLO !!!!!!!!!!!!!!!
    function calculateScoreRandom($newTeams, $existingTeams) {
        $score = 0;

        foreach ($newTeams as $team) {
            foreach ($existingTeams as $existingTeam) {
                if (
                    ($team['player1'] === $existingTeam['player1'] && $team['player2'] === $existingTeam['player2']) ||
                    ($team['player1'] === $existingTeam['player2'] && $team['player2'] === $existingTeam['player1'])
                ) {
                    $score++;
                }

                if (
                    $team['player1'] === $existingTeam['player1'] && $team['player2'] === null && $existingTeam['player2'] === null
                ) {
                    $score += 5;
                }
            }
        }

        return $score;
    }
    
    if ($algorithm === "random") {
      $numIterations = 1000;
      $bestScore = PHP_INT_MAX;
      $bestTeams = [];

      for ($i = 0; $i < $numIterations; $i++) {
        shuffle($players);
        $teams = [];

        for ($j = 0; $j < count($players); $j += 2) {
          $team = [
            'player1' => $players[$j]['name'],
            'player2' => ($j + 1 < count($players)) ? $players[$j + 1]['name'] : null
          ];
          $teams[] = $team;
        }

        $score = calculateScoreRandom($teams, $existingTeams);

        if ($score < $bestScore) {
          $bestScore = $score;
          $bestTeams = $teams;
        }
      }

      $teams = $bestTeams;
    }

    // !!!!!!!!!! MATCH LEVEL ALGO !!!!!!!!!!!!!!
    function generateTeams($players, $existingTeams) {
      $teams = [];

      for ($j = 0; $j < count($players); $j += 2) {
        if (is_array($existingTeams)) {
          $team = [
            'player1' => [
              'name' => $players[$j]['name'],
              'level' => $players[$j]['level']
            ],
            'player2' => ($j + 1 < count($players)) ? [
              'name' => $players[$j + 1]['name'],
              'level' => $players[$j + 1]['level']
            ] : null
          ];

          $teams[] = $team;
        }
      }

      return $teams;
    }

    function selectBestTeams($teamsArray, $existingTeams) {
      $selectedIndex = 0;
      $previousBestScore = PHP_INT_MAX;

      foreach ($teamsArray as $index => $teams) {
        $score = calculateScoreMatchLevel($teams, $existingTeams);

        if ($score < $previousBestScore) {
          $selectedIndex = $index;
          $previousBestScore = $score;
        }
      }

      return $selectedIndex;
    }

    function calculateScoreMatchLevel($teams, $existingTeams) {
      $score = 0;

      foreach ($teams as $team) {
        if ($team['player2'] !== null && $team['player1'] !== null) {
          if ($team['player1']['level'] !== null && $team['player2']['level'] !== null) {
            $score += max(0, abs($team['player1']['level'] - $team['player2']['level']));
          }
        }
        
        if (is_array($existingTeams) && !is_null($existingTeams) && count($existingTeams) > 0) {
          foreach ($existingTeams as $existingTeam) {
            if (
            $team['player1']['name'] === $existingTeam['player1'] && $team['player2'] === null && $existingTeam['player2'] === null
          ) {
              $score += 8;
            }
          }
        } 
      }

      return $score;
    }

    function convertTeams($teamsRaw) {
      $teams = [];

      foreach ($teamsRaw as $teamRaw) {
        $team = ['player1' => $teamRaw['player1']['name'], 'player2' => $teamRaw['player2']['name']];
        $teams[] = $team;
      }

      return $teams;
    }

    if ($algorithm === "matchLevel") {
      $numIterations = 3000;
      $selectedTeams = [];
      $teamsArray = [];

      // Create many teams distributions
      for ($i = 0; $i < $numIterations; $i++) {
        shuffle($players);
        $teams = generateTeams($players, $existingTeams);

        $teamsArray[] = $teams;
      }

      // Test the teams with existing previous teams
      $selectedIndex = selectBestTeams($teamsArray, $existingTeams);

      $teamsRaw = $teamsArray[$selectedIndex];

      // Convert teams to only player names
      $teams = convertTeams($teamsRaw);
    }

    // add team into team_array of db
    if (isset($existingTeams) && is_array($existingTeams)) {
      $combinedTeams = array_merge($existingTeams, $teams);
    }
    else {
      $combinedTeams = $teams;
    }

    // Store the team information in the database
    $updateTeamQuery = "
      UPDATE users 
      SET team_array = :teamArray
      WHERE username = :username
    ";

    $combineTeamsEncoded = json_encode($combinedTeams);

    $stmt = $file_db->prepare($updateTeamQuery);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':teamArray', $combineTeamsEncoded);
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
