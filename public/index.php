<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badminton Team Builder</title>
    <!-- You can include additional CSS and JavaScript files here -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    
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
        background-color: lightGray;
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
        <h1 id="title">Connect to Badminton Team Builder</h1>
        <!-- Add navigation or other header content here -->
    </header>

    <main>
        <form action="login.php" method="post">
          <label for="username">Username:</label>
          <input type="text" id="username" name="username" required>

          <label for="password">Password:</label>
          <input type="password" id="password" name="password" required>

          <button type="submit">Login</button>
        </form>

    </main>
    <!-- Button to navigate to the registration page -->
    <!-- <a href="register.php"><button>Register</button></a> -->

    <!-- disabled version of the button -->
    <!-- <a href="register.php"><button disabled>Register</button></a> -->
</body>

</html>
