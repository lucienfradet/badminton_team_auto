<?php
session_start();
require('openDB.php');

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log(`Debug Objects: " . $output . "`);</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitize and validate user input
  $enteredUsername = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
  $enteredPassword = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

  if (empty($enteredUsername) || empty($enteredPassword)) {
    // Invalid input, handle the error
    echo "Invalid username or password";
    exit();
  }

  // Check if the user exists in the 'users' table
  $checkUserQuery = "SELECT * FROM users WHERE username=:username";
  $stmt = $file_db->prepare($checkUserQuery);
  $stmt->bindParam(':username', $enteredUsername, PDO::PARAM_STR);
  $stmt->execute();

  if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // User exists, now check the hashed password
    if (password_verify($enteredPassword, $row['password'])) {
      // Match found, start session
      $_SESSION['username'] = $enteredUsername;
      $_SESSION['table'] = $enteredUsername;
      ob_start();
      echo "Login successful!";
      header('Location: dashboard.php'); // Redirect to dashboard or any other page
      ob_end_flush();
      exit();
    }
  }

  // No match found
  echo "Invalid username or password";
}

$file_db = null; // Close database connection
?>
