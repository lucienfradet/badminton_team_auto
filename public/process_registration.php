<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Include your database connection file
  require('openDB.php');

  // Validate and sanitize user input
  $newUsername = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
  $newPassword = isset($_POST['password']) ? $_POST['password'] : '';

  // Check if the table already exists
  $checkTableQuery = "SELECT name FROM sqlite_master WHERE type='table' AND name=:username";
  $stmt = $file_db->prepare($checkTableQuery);
  $stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
  $stmt->execute();

  if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    // Table already exists, handle the error
    echo 'User with this username already exists. Please choose a different username.';
  } else {
    // Table doesn't exist, create it
    try {
      $createTableQuery = "
      CREATE TABLE IF NOT EXISTS $newUsername (
      timestamp TEXT,
      username TEXT,
      password TEXT,
      team_array TEXT,
      algorithm_selection TEXT
      )
      ";

      $file_db->exec($createTableQuery);

      //hash password
      $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);

      // Insert data into the newly created table
      $insertIntoUserTableQuery = "INSERT INTO $newUsername (timestamp, username, password, team_array, algorithm_selection) VALUES (CURRENT_TIMESTAMP, :username, :password, '', 'random')";
      $stmt = $file_db->prepare($insertIntoUserTableQuery);
      $stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
      $stmt->bindParam(':password', $hashPassword, PDO::PARAM_STR);
      $stmt->execute();

      echo 'User registered successfully!';
    } catch (PDOException $e) {
      // Log or display the error
      echo 'Error registering user: ' . $e->getMessage();
    }
  }

  // Close database connection
  $file_db = null;
} else {
  // Redirect to the registration page if accessed directly
  header('Location: index.php');
  exit();
}
?>
