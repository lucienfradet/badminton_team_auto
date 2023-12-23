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
    $enteredUsername = $_POST['username'];
    $enteredPassword = $_POST['password'];

    $tables = ['group_debut', 'group_inter', 'group_advan'];

    foreach ($tables as $table) {
        $query = "
            SELECT * FROM $table
            WHERE username=:username AND password=:password
        ";

        $stmt = $file_db->prepare($query);
        $stmt->bindParam(':username', $enteredUsername, PDO::PARAM_STR);
        $stmt->bindParam(':password', $enteredPassword, PDO::PARAM_STR);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Match found, start session
            $_SESSION['username'] = $enteredUsername;
            $_SESSION['table'] = $table;
            echo "Login successful!";
            header('Location: dashboard.php'); // Redirect to dashboard or any other page
            exit();
        }
    }

    // No match found
    echo "Invalid username or password";
}

$file_db = null; // Close database connection
?>
