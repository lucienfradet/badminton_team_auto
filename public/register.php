<?php
// Disable registration by redirecting users away
// header('Location: index.php');
// exit();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Include additional CSS if needed -->
    <link rel="stylesheet" type="text/css" href="/css/style.css" />
</head>

<body>

    <header>
        <h1 id="title">Register for Badminton Team Builder</h1>
    </header>

    <main>
        <form action="process_registration.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Register</button>
        </form>
    </main>

</body>

<!-- Include your script(s) -->
<script src="js/script.js"></script>

</html>
