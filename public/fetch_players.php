<?php
require 'auth_check.php';
require('openDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $tablename = $_SESSION['table'];

        // Fetch players based on the tablename
        $fetchPlayersQuery = "
            SELECT * FROM players
            WHERE tablename = :tablename
        ";

        $stmt = $file_db->prepare($fetchPlayersQuery);
        $stmt->bindParam(':tablename', $tablename, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch all players as an associative array
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return players as JSON
        header('Content-Type: application/json');
        echo json_encode($players);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        http_response_code(500); // Set error status code
        // Log the real error to a file, but send a clean message to user
        echo json_encode(['error' => 'Database error occurred']); 
    }
}
?>
