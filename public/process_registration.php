<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include your database connection file
    require 'openDB.php';
    require "TURNSTILE_SECRET.php";

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
    }

    // Close database connection
    $file_db = null;
} else {
    // Redirect to the registration page if accessed directly
    header('Location: index.php');
    exit();
}
?>
