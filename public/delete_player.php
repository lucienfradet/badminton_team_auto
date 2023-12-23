<?php
session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playerId'])) {
    try {
        $playerId = $_POST['playerId'];

        // Delete the player from the "players" database
        $deletePlayerQuery = "
          DELETE FROM players
          WHERE
            id = :playerId
        ";
        $stmt = $file_db->prepare($deletePlayerQuery);
        $stmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Log or handle the error
        echo json_encode(['error' => 'Error deleting player in php: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
