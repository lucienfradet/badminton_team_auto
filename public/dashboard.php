<?php
session_start();
require 'openDB.php';

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

// Process form submission to add a new player
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addPlayer'])) {
    try {
        // Validate and sanitize playerLevel
        if (isset($_POST['playerLevel'])) {
            $newPlayerLevel = filter_var($_POST['playerLevel'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 10)));

            if ($newPlayerLevel === false) {
                // Invalid playerLevel, handle the Error
                // (e.g., display an error message or log it)
                echo 'Invalid playerLevel. Please enter a number between 1 and 10.';
                exit();
            }
        } else {
            // playerLevel is not set, handle the error
            echo 'playerLevel is required.';
            exit();
        }

        // Sanitize playerName
        if (isset($_POST['playerName'])) {
            $newPlayerName = htmlspecialchars($_POST['playerName'], ENT_QUOTES, 'UTF-8');
        } else {
            // playerName is not set, handle the error
            echo 'playerName is required.';
            exit();
        }

        $table = $_SESSION['table'];

        // Insert the new player into the "players" table
        $insertPlayerQuery = "
        INSERT INTO players (name, level, tablename, active)
        VALUES (:name, :level, :tablename, :active)
        ";

        $stmt = $file_db->prepare($insertPlayerQuery);
        $stmt->bindParam(':name', $newPlayerName, PDO::PARAM_STR);
        $stmt->bindParam(':level', $newPlayerLevel, PDO::PARAM_INT);
        $stmt->bindParam(':tablename', $table, PDO::PARAM_STR);
        $activeValue = true;
        $stmt->bindParam(':active', $activeValue, PDO::PARAM_BOOL);
        $stmt->execute();
    } catch (PDOException $e) {
        // Log or display the error
        echo 'Error adding players in php: ' . $e->getMessage();
    }

    // Reset form fields
    $_POST['playerName'] = '';
    $_POST['playerLevel'] = '';

    // Redirect to the same page to avoid form resubmission
    header('Location: dashboard.php');
    exit();
}

$file_db = null; // Close database connection
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Tableau de bord</title>
        <!-- Add your styles here if needed -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <!-- CSS stylesheet(s) -->
        <!-- <link rel="stylesheet" type="text/css" href="css/style.css" /> -->
        <?php require 'style_css.php'; ?>
    </head>
    <body>
        <div id="dashboard-container">
            <h2>Connecté en tant que, <?php echo $_SESSION['username']; ?>!</h2>

            <!-- Display already added players -->
            <div id="players-container" class="inner-container"></div> 

            <button id="togglePlayerList" class="toggle-buttons">Afficher la liste de joueurs</button>

            <div id="addPlayer-container" class="inner-container">
                <!-- Button to toggle the form -->
                <button id="toggleAddPlayerForm" class="toggle-buttons">Ajouter un joueur</button>

                <!-- Form to add new player -->
                <form id="addPlayerForm" action="" method="post">
                    <label for="playerName">Nom:</label>
                    <input type="text" id="playerName" name="playerName" required>

                    <label for="playerLevel">Niveau:</label>
                    <input type="number" id="playerLevel" name="playerLevel" min="1" max="10" value="1" required>

                    <button id="add-player-btn" type="submit" name="addPlayer">Ajouter</button>
                </form>
            </div>

            <div id="generate-teams-container" class="inner-container">
                <h2>Générer les équipes</h2>
                <form id="generateTeamsForm" action="generate_teams.php" method="post">
                    <div class="generateTeamsForm-inner-container">
                        <label for="numCourts"><strong>Nombre de terrain(s):</strong></label>
                        <input type="number" name="numCourts" id="numCourts" min="1" value="1" required>
                    </div>

                    <div>
                        <p>Nombres de joueurs actifs: <span id="active-player-count"></span></p>
                    </div>

                    <div class="generateTeamsForm-inner-container">
                        <table>
                            <tr>
                                <td colspan="2" style="text-align: center;"><label for="randomAlgorithm"><strong>Sélection d'algorithme:</strong></label></td>
                            </tr>
                            <tr>
                                <td style="width: 50%;"><input id="randomAlgorithm" type="radio" name="algorithm" value="random"> Aléatoire</td>
                                <td style="width: 50%;"><input id="matchLevelAlgorithm" type="radio" name="algorithm" value="matchLevel"> Balancer avec le niveau des joueurs</td>
                            </tr>
                        </table>
                    </div>

                    <span style="font-size: 10pt;">Balancer les équipes sur les terrains</span><input type="checkbox" name='balanceCourtsSwitch' id="balance-courts-switch">

                    <div class="generateTeamsForm-inner-container">
                        <button id="generateTeamsButton" type="button">Générer les équipes</button>
                        <button id="sessionDeleteButton" type="button">Effacer l'historique des équipes</button>
                    </div>
                    <p><strong>Historique existante?</strong> 
                        <span id="session-active-flag">None</span>
                    </p>

                    <div class="generateTeamsForm-inner-container">
                        <button id="toggle-teams-button" type="button">Cacher les équipes</button>
                    </div>

                </form>
                <div id="teams-container" class="inner-container">

                </div>
            </div>

            <!-- Logout button container -->
            <div id="logout-container">
                <form action="logout.php" method="post">
                    <button id="logout-btn" type="submit">Déconnexion</button>
                </form>
            </div>
        </div>
    </body>
    <!-- My script(s) -->
    <!-- <script src="script.js"></script> -->
    <?php require 'script_js.php'; ?>

</html>
