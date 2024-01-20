<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Badminton Team Builder</title>
    <!-- You can include additional CSS and JavaScript files here -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- CSS stylesheet(s) -->
    <!-- <link rel="stylesheet" type="text/css" href="css/style.css" /> -->
    <?php include 'style_css.php'; ?>
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
    <a href="register.php"><button disabled>Register</button></a>
</body>

  <!-- My script(s) -->
  <!-- <script src="js/script.js"></script> -->
  <?php include 'script_js.php'; ?>

</html>
