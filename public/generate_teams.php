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
  $postSwitch = $_POST['postSwitch'];
  if (isset($_POST['pastData'])) {
    $jsonPastData = $_POST['pastData'];
  }
  else {
    $jsonPastData = null;
  }
  // this switch can be null or "on"
  if (isset($_POST['balanceCourtsSwitch'])) {
    $balanceCourtsSwitch = $_POST['balanceCourtsSwitch'];
  }
  else {
    $balanceCourtsSwitch = null;
  }

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

    // fetch previous courts
    $queryFetchCourtArray = "
      SELECT court_array
      FROM users
      WHERE username = :username
    ";

    $stmt = $file_db->prepare($queryFetchCourtArray);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $existingCourtArray = $stmt->fetchColumn();

    $existingCourts = json_decode($existingCourtArray, true);

    // Post previous data if needed
    if ($postSwitch === "true" && $jsonPastData !== null) {
      // post teams data

      // add team_array
      //get the past data ready
      $pastData = json_decode($jsonPastData);

      if ($pastData !== null) {
        $pastTeams = [];
        foreach ($pastData->courts as $court) {
          foreach ($court as $team) {
            $pastTeams[] = [
              'player1' => $team->player1->name,
              'player2' => $team->player2->name
            ];
          }
        }
        foreach ($pastData->bench as $team) {
          $pastTeams[] = [
            'player1' => $team->player1->name,
            'player2' => null
          ];
        }

        // add pastTeam into team_array of db
        if (isset($existingTeams) && is_array($existingTeams)) {
          $existingTeams = array_merge($existingTeams, $pastTeams);
        }
        else {
          $existingTeams = $pastTeams;
        }

        // Store the team information in the database
        $updateTeamQuery = "
        UPDATE users 
        SET team_array = :teamArray
        WHERE username = :username
        ";

        $combineTeamsEncoded = json_encode($existingTeams);

        $stmt = $file_db->prepare($updateTeamQuery);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':teamArray', $combineTeamsEncoded);
        $stmt->execute();


        // add court_array
        $pastCourts = [];
        foreach ($pastData->courts as $court) {
          $tempCourt = [];
          foreach ($court as $team) {
            $tempCourt[] = $team->player1->name;
            $tempCourt[] = $team->player2->name;
          }
          $pastCourts[] = $tempCourt;
        }

        if (isset($existingCourts) && is_array($existingCourts)) {
          $existingCourts = array_merge($existingCourts, $pastCourts);
        }
        else {
          $existingCourts = $pastCourts;
        }

        // Store the team information in the database
        $updateCourtQuery = "
        UPDATE users 
        SET court_array = :courtArray
        WHERE username = :username
        ";

        $combineCourtsEncoded = json_encode($existingCourts);

        $stmt = $file_db->prepare($updateCourtQuery);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':courtArray', $combineCourtsEncoded);
        $stmt->execute();
      }
    }


    // !!!!!!!!!!!!!!!! TEAM CREATION !!!!!!!!!!!!!!!!!!!!!
    function convertTeams($teamsRaw) {
      $teams = [];

      foreach ($teamsRaw as $teamRaw) {
        if ($teamRaw['player2'] === null) {
          $team = ['player1' => $teamRaw['player1']['name'], 'player2' => $teamRaw['player2']];
        }
        else {
          $team = ['player1' => $teamRaw['player1']['name'], 'player2' => $teamRaw['player2']['name']];
        }
        $teams[] = $team;
      }

      return $teams;
    }

    function convertTeamForBench($teamPlayer) {
      return [
        'player1' => [
          'name' => $teamPlayer['name'],
          'level' => $teamPlayer['level']
        ],
        'player2' => null
      ];
    }

    function buildCourts($teams) {
      $numCourts = $GLOBALS['numCourts'];
      // Initialize arrays for courts and bench
      $courts = [];
      $bench = [];
      $teamsInCourt = 0;

      // Iterate over teams
      $i = 0;
      $courtCounter = 0;
      foreach ($teams as $team) {
        // Check if player2 is null and team is alone in court
        if ($team['player2'] === null) {
          $bench[] = $team; // Put the team in the bench
        }
        else {
          // Check if there are available courts
          if ($courtCounter < $numCourts) {
            if ($teamsInCourt >= 2) {
              $courtCounter++;
              $courts[] = [$team]; // Create a new court with the current team
              $teamsInCourt = 1;
            }
            else {
              // If the court has less than 2 teams, add the team to it
              $courts[$courtCounter][] = $team;
              $teamsInCourt++;
            }
          } else {
            // If the team was not added to any court, put it in the bench
            // convert bench player to their own null teams
            $bench[] = convertTeamForBench($team['player1']);
            $bench[] = convertTeamForBench($team['player2']);
          }
        }
        $i++;
      }

      // remove teams alone in a court
      foreach ($courts as $index => $court) {
        if (count($court) === 1) {
          $bench[] = convertTeamForBench($court[0]['player1']);
          $bench[] = convertTeamForBench($court[0]['player2']);
          unset($courts[$index]);
        }
      }

      return compact('courts', 'bench');
    }

    function compareByLevel($player1, $player2) {
      return $player1['level'] - $player2['level'];
    }

    function balanceCourts($courts) {
      foreach ($courts['courts'] as $index => $court) {
        $players = [];
        foreach ($court as $teams) {
          foreach ($teams as $player) {
            $players[] = $player;
          }
        }
        usort($players, 'compareByLevel');
        $courts['courts'][$index][0] = ['player1' => $players[0], 'player2' => $players[3]];
        $courts['courts'][$index][1] = ['player1' => $players[1], 'player2' => $players[2]];
      }
      return $courts;
    }


    // !!!!!!!!!! RANDOM AGLO !!!!!!!!!!!!!!!
    function calculateScoreRandom($newTeams, $existingTeams, $existingCourts) {
      $score = 0;
      $scoreIncrementBench = 10;
      $courtIncrement = 0.16;

      $index = 0;
      foreach ($newTeams as $team) {
        foreach ($existingTeams as $existingTeam) {

          if ($team['player2'] !== null && $team['player1'] !== null) {
            if (
              ($team['player1']['name'] === $existingTeam['player1'] && $team['player2']['name'] === $existingTeam['player2']) ||
              ($team['player1']['name'] === $existingTeam['player2'] && $team['player2']['name'] === $existingTeam['player1'])
            ) {
              $score++;
            }
          }


          if ($team['player2'] !== null && $team['player1'] !== null) {
            if (
              $team['player1']['name'] === $existingTeam['player1'] && $team['player2']['name'] === null && $existingTeam['player2'] === null
            ) {
              $score += $scoreIncrementBench;
            }
          }

          // check the case where there is a team alone but without null
          if ((count($newTeams) % 2 !== 0 && $index === count($newTeams) - 1)
            // OR the case where there is an even number but there is a player on bench and a team alone on a court 
            || ($index === count($newTeams) - 2 && $newTeams[count($newTeams) - 1]['player2'] === null)) 
          {
            if ($team['player2'] !== null && $team['player1'] !== null) {
              if (($team['player1']['name'] === $existingTeam['player1'] && $existingTeam['player2'] === null)
                || ($team['player2']['name'] === $existingTeam['player1'] && $existingTeam['player2'] === null)
            ) {
                $score += $scoreIncrementBench;
              }
            }
          }
        }

        // check for court redundancy
        if (
          $index != count($newTeams) - 1 &&
          $team['player2'] !== null &&
          $newTeams[$index + 1]['player2'] !== null &&
          $existingCourts !== null
      ) {
          $nextTeam = $newTeams[$index + 1];
          $players = [];
          $players[] = $team['player1']['name'];
          $players[] = $team['player2']['name'];
          $players[] = $nextTeam['player1']['name'];
          $players[] = $nextTeam['player2']['name'];
          foreach ($existingCourts as $court) {
            $atLeastOne = false;
            foreach ($players as $player) {
              foreach ($court as $courtPlayer) {
                if ($player === $courtPlayer) {
                  if (!$atLeastOne) {
                    $atLeastOne = true;
                  }
                  else {
                    $score+= $courtIncrement;
                  }
                }
              }
            }
          }
        }

        $index++;
      }

      return $score;
    }
    
    if ($algorithm === "random") {
      $numIterations = 3000;
      $bestScore = PHP_INT_MAX;
      $bestTeams = [];

      for ($i = 0; $i < $numIterations; $i++) {
        shuffle($players);
        $teams = [];

        for ($j = 0; $j < count($players); $j += 2) {
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

        $score = calculateScoreRandom($teams, $existingTeams, $existingCourts);

        if ($score < $bestScore) {
          $bestScore = $score;
          $bestTeams = $teams;
        }
      }


      $teams = $bestTeams;
      $courts = buildCourts($teams);

      if ($balanceCourtsSwitch == 'on') {
        $courts = balanceCourts($courts);
      }
    }

    // !!!!!!!!!! MATCH LEVEL ALGO !!!!!!!!!!!!!!
    function generateTeams($players, $numCourts) {
      $courts = [
        'courts' => [],
        'bench' => []
      ];
      
      for ($i = 0; $i < count($players); $i+=4) { 
        if ($i + 4 >= count($players) || $numCourts <= 0) {
          $benchPlayers = array_slice($players, $i, 4);
          foreach ($benchPlayers as $benchPlayer) {
            $courts['bench'][] = $benchPlayer;
          }
        }
        else {
          $court = [];
          $court[] = array_slice($players, $i, 2);
          $court[] = array_slice($players, $i + 2, 2);
          $courts['courts'][] = $court;
          $numCourts--;
        }
      }

      // Format players and teams for 'bench'
      foreach ($courts['bench'] as &$player) {
        $player = [
          'player1' => [
            'name' => $player['name'],
            'level' => $player['level']
          ],
          'player2' => null
        ];
      }
      unset($player); // Unset the reference to avoid unexpected behavior in subsequent iterations

      // Format players and teams for 'courts'
      foreach ($courts['courts'] as &$court) {
        $court[0] = [
          'player1' => [
            'name' => $court[0][0]['name'],
            'level' => $court[0][0]['level']
          ],
          'player2' => [
            'name' => $court[0][1]['name'],
            'level' => $court[0][1]['level']
          ]
        ];
        $court[1] = [
          'player1' => [
            'name' => $court[1][0]['name'],
            'level' => $court[1][0]['level']
          ],
          'player2' => [
            'name' => $court[1][1]['name'],
            'level' => $court[1][1]['level']
          ]
        ];
      }
      unset($court); // Unset the reference to avoid unexpected behavior in subsequent iterations

      return $courts;
    }

    function selectBestTeams($courtsArray, $existingTeams) {
      $selectedIndex = 0;
      $previousBestScore = PHP_INT_MAX;

      foreach ($courtsArray as $index => $teams) {
        $score = calculateScoreMatchLevel($teams, $existingTeams);

        if ($score < $previousBestScore) {
          $selectedIndex = $index;
          $previousBestScore = $score;
        }
      }

      return $selectedIndex;
    }

    function calculateScoreMatchLevel($courts, $existingTeams) {
      $score = 0;
      $scoreIncrementBench = 20;

      // player level based score
      foreach ($courts['courts'] as $court) {
        // find the level of best player
        $highestScore = 0;
        foreach ($court as $team) {
          for ($i = 1; $i  <= 2; $i ++) { 
            if ($highestScore === 0) {
              $highestScore = $team['player'.$i]['level'];
              continue;
            }
            if ($team['player'.$i]['level'] > $highestScore) {
              $highestScore = $team['player'.$i]['level'];
            }
          }
        }

        // calculate how close to the best the other players are.
        // higher number is bad
        $courtScore = 0;
        foreach ($court as $team) {
          for ($i = 1; $i  <= 2; $i ++) { 
            $courtScore += $highestScore - $team['player'.$i]['level'];
          }
        }
        $score += $courtScore;
      }

      // increase score for bench redos
      if (is_array($existingTeams) && !is_null($existingTeams) && count($existingTeams) > 0) {
        foreach ($courts['bench'] as $benchTeam) {
          foreach ($existingTeams as $existingTeam) {
            if ($existingTeam['player2'] === null && $existingTeam['player1'] === $benchTeam['player1']['name']) {
              $score += $scoreIncrementBench;
            }
          }
        }
      }

      return $score;
    }

    if ($algorithm === "matchLevel") {
      $numIterations = 3000;
      $selectedTeams = [];
      $courtsArray = [];

      // Create many teams distributions
      for ($i = 0; $i < $numIterations; $i++) {
        shuffle($players);
        $courts = generateTeams($players, $numCourts);

        $courtsArray[] = $courts;
      }

      // Test the teams with existing previous teams
      $selectedIndex = selectBestTeams($courtsArray, $existingTeams);

      $bestCourts = $courtsArray[$selectedIndex];

      $courts = $bestCourts;

      $courts = balanceCourts($courts);
    }


    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode([
      'courtNumber' => $numCourts,
      'algorithm' => $algorithm,
      'balanceCourtsSwitch' => $balanceCourtsSwitch,
      'postSwitch' => $postSwitch,
      'courts' => $courts
      // 'pastData' => $pastData
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
