<?php
session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
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

    <button id="togglePlayerList" class="toggle-buttons">Hide Players</button>

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
</body>

  <!-- My script(s) -->
  <script src="js/script.js"></script>

</html>
