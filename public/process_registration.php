<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Include your database connection file
  require('openDB.php');

  // Validate and sanitize user input
  $newUsername = isset($_POST['username']) ? htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8') : '';
  $newPassword = isset($_POST['password']) ? $_POST['password'] : '';

  // Check if the password is empty
  if (empty($newPassword)) {
    echo 'Error: Password cannot be empty.';
    exit();
  }

  // Check if the username already exists in the 'users' table
  $checkUserQuery = "SELECT * FROM users WHERE username=:username";
  $stmt = $file_db->prepare($checkUserQuery);
  $stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
  $stmt->execute();

  if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    // Table already exists, handle the error
    echo 'User with this username already exists. Please choose a different username.';
  } else {
    // User doesn't exist, proceed with registration
    try {
      //hash password
      $hashPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);

      // Insert data into the 'users' table
      $insertIntoUsersQuery = "INSERT INTO users (timestamp, username, password, team_array, algorithm) VALUES (CURRENT_TIMESTAMP, :username, :password, '', 'random')";
      $stmt = $file_db->prepare($insertIntoUsersQuery);
      $stmt->bindParam(':username', $newUsername, PDO::PARAM_STR);
      $stmt->bindParam(':password', $hashPassword, PDO::PARAM_STR);
      $stmt->execute();

      echo 'User registered successfully!';
    }
    catch (PDOException $e) {
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
