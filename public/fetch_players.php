<?php
session_start();
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
        // Log or display the error
        echo 'Error fetching players in query: ' . $e->getMessage();
    }
}
?>
