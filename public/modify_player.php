<?php

use http\Header;

session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: index.php'); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $playerId = isset($_POST['playerId']);
        $playerName = $_POST['name'];
        $playerLevel = $_POST['level'];

        // Delete the player from the "players" database
        $updatePlayerQuery = "
          UPDATE players
          SET name = :name,
              level = :level
          WHERE
            id = :playerId
        ";

        $stmt = $file_db->prepare($updatePlayerQuery);
        $stmt->bindParam(':name', $playerName, PDO::PARAM_STR);
        $stmt->bindParam(':level', $playerLevel, PDO::PARAM_INT);
        $stmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $stmt->execute();

        $response = [
            'success' => true,
            'playerName' => $playerName,
            'rowCount' => $stmt->rowCount(),
        ];
        echo json_encode($response);

    } catch (PDOException $e) {
        // Log or handle the error
        echo json_encode(['error' => 'Error modifying player in php: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
