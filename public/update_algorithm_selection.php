<?php
// Include the database connection file
require('openDB.php');

session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['table'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit();
}

$username = $_SESSION['username'];
$tablename = $_SESSION['table'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get the new algorithm selection from the POST request
        $newAlgorithm = $_POST['algorithmSelection'];

        // Update the algorithm selection in the database
        $query = "UPDATE $tablename SET algorithm_selection = :algorithmSelection WHERE username = :username";
        $stmt = $file_db->prepare($query);
        $stmt->bindParam(':algorithmSelection', $newAlgorithm);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Send a success response
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Print PDOException message
        echo json_encode(['error' => 'Error updating algorithm selection']);
    }
} else {
    // Handle invalid requests
    echo json_encode(['error' => 'Invalid request']);
}
