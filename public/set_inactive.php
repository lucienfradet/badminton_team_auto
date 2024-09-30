<?php
session_start();
require('openDB.php');

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['playerId']) && isset($_POST['active'])) {
    try {
        $playerId = $_POST['playerId'];
        // if ($_POST['active'] == true) {
        //   $active = 0;
        // }
        // else {
        //   $active = 0;
        // }
        $active = $_POST['active'];

        // Update the player to set as inactive
        $updatePlayerQuery = "
            UPDATE players
            SET active = :active
            WHERE id = :playerId
        ";

        $stmt = $file_db->prepare($updatePlayerQuery);
        $stmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
        $stmt->bindParam(':active', $active, PDO::PARAM_BOOL);
        $stmt->execute();
        
        // Provide detailed JSON response with debug information
        $response = [
            'success' => true,
            'active?' => $active,
            'rowCount' => $stmt->rowCount(),
        ];
        echo json_encode($response);
    } catch (PDOException $e) {
        // Log or handle the error
        echo json_encode(['error' => 'Error setting player as inactive: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>
