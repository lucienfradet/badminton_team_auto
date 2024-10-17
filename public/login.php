<?php
session_start();
require 'openDB.php';
require "TURNSTILE_SECRET.php";

function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log(`Debug Objects: " . $output . "`);</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the Turnstile token and the user's IP address
    $token = $_POST['cf-turnstile-response'];
    $ip = $_SERVER['REMOTE_ADDR'];

    // Validate the token by calling the "/siteverify" API endpoint
    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";

    // Prepare the data to send
    $data = [
        'secret' => TURNSTILE_SECRET,
        'response' => $token,
        'remoteip' => $ip
    ];

    // Use cURL to send the request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    curl_close($ch);

    // Decode the response
    $outcome = json_decode($response, true);

    // Check if the verification was successful
    if ($outcome['success']) {
        // Continue with the login process
        // (e.g., check username and password against your database)
        // echo "captcha turnstile successful";

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

}

$file_db = null; // Close database connection
?>
