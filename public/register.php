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
    <title>Inscription</title>
    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Include additional CSS if needed -->
    <link rel="stylesheet" type="text/css" href="/css/style.css" />

    <style>
      /* styles.css */

      body {
        font-family: 'Helvetica', sans-serif;
        margin: 0;
        padding: 0;
      }

      header {
        background-color: DarkSlateGrey;
        color: white;
        text-align: center;
        padding: 20px;
      }

      main {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 80vh; /* Adjust the height based on your design */
      }

      form {
        background-color: beige;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 300px; /* Adjust the width based on your design */
      }

      form label {
        display: block;
        margin-bottom: 10px;
      }

      form input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        width: 280px;
      }

      form button {
        background-color: DarkSlateGrey;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
      }

      a button {
        display: block;
        background-color: DarkSlateGrey;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 300px; /* Adjust the width based on your design */
        text-align: center;
        text-decoration: none;
        margin: 10px auto; /* Center the button */
      }

      /* Add any additional styles or adjustments based on your preferences */
    </style>
</head>

<body>

    <header>
        <h1 id="title">Register for Badminton Team Builder</h1>
    </header>

    <main>
        <form id="registration-form" action="process_registration.php" method="post">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>
            <label for="password-confirmation">Confirmer le mot de passe:</label>
            <input type="password" id="password-confirmation" name="password-confirmation" required>
            <div class="cf-turnstile" data-sitekey="0x4AAAAAAAxuiXlilIFOJWyW"></div>

            <button type="submit">S'inscrire</button>
        </form>
    </main>

        <!-- Include your script(s) -->
        <script src="js/script.js"></script>
        <script>
        $(document).ready(function() {
            $('#registration-form').on('submit', function(event) {
                // Get the values of the password and password confirmation fields
                var password = $('#password').val();
                var passwordConfirmation = $('#password-confirmation').val();

                // Check if the passwords match
                if (password !== passwordConfirmation) {
                    // Prevent the form from submitting
                    event.preventDefault();
                    // Alert the user
                    alert("Les mots de passe ne correspondent pas. Veuillez réessayer.");
                }
            });
        });
        </script>

</body>

</html>
