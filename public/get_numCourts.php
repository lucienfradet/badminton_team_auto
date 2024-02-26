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

try {
    // Fetch the algorithm selection from the database
    $query = "SELECT numCourts FROM users WHERE username = :username";
    $stmt = $file_db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send JSON response with the algorithm selection
    header('Content-Type: application/json');
    echo json_encode(['numCourts' => $result['numCourts']]);
} catch (PDOException $e) {
    // Print PDOException message
    echo json_encode(['error' => 'Error fetching numCourts selection']);
}
