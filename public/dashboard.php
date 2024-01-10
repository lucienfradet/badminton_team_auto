<?php
session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

// Process form submission to add a new player
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPlayer'])) {
    try {
      $newPlayerName = $_POST['playerName'];
      $newPlayerLevel = $_POST['playerLevel'];
      $table = $_SESSION['table'];

      // Insert the new player into the "players" table
      $insertPlayerQuery = "
          INSERT INTO players (name, level, tablename, active)
          VALUES (:name, :level, :tablename, :active)
      ";

      $stmt = $file_db->prepare($insertPlayerQuery);
      $stmt->bindParam(':name', $newPlayerName, PDO::PARAM_STR);
      $stmt->bindParam(':level', $newPlayerLevel, PDO::PARAM_INT);
      $stmt->bindParam(':tablename', $table, PDO::PARAM_STR);
      $activeValue = true;
      $stmt->bindParam(':active', $activeValue, PDO::PARAM_BOOL);
      $stmt->execute();
    } catch (PDOException $e) {
        // Log or display the error
        echo 'Error adding players in php: ' . $e->getMessage();
    }

    // Reset form fields
    $_POST['playerName'] = '';
    $_POST['playerLevel'] = '';

    // Redirect to the same page to avoid form resubmission
    header('Location: dashboard.php');
    exit();
}

$file_db = null; // Close database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Add your styles here if needed -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- CSS stylesheet(s) -->
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>

    <!-- Display already added players -->
    <div id="players-container"></div> 

    <button id="togglePlayerList" class="toggle-buttons">Show Player List</button>

    <div id="addPlayer-container">
      <!-- Button to toggle the form -->
      <button id="toggleAddPlayerForm" class="toggle-buttons">Add New Player</button>

      <!-- Form to add new player -->
      <form id="addPlayerForm" action="" method="post">
          <label for="playerName">Player Name:</label>
          <input type="text" id="playerName" name="playerName" required>

          <label for="playerLevel">Player Level:</label>
          <input type="number" id="playerLevel" name="playerLevel" required>

          <button type="submit" name="addPlayer">Add Player</button>
      </form>
    </div>

    <div id="generate-teams-container">
      <h2>Generate Teams</h2>
      <form id="generateTeamsForm" action="generate_teams.php" method="post">
        <label for="numCourts">Number of Courts:</label>
        <input type="number" name="numCourts" id="numCourts" min="1" value="1" required>

        <label>Algorithm Selection:</label>
        <input type="radio" name="algorithm" value="random" checked> Random
        <input type="radio" name="algorithm" value="matchLevel"> Match Level

        <button id="generateTeamsButton" type="button">Generate Team</button>
        <button id="sessionDeleteButton" type="button">Delete Active Session</button>
        <p>Session in progress? 
        <span id="session-active-flag">None</span>
        </p>
      </form>
      <div id="teams-container">
        
      </div>
    </div>
</body>

  <!-- My script(s) -->
  <script src="js/script.js"></script>

</html>
