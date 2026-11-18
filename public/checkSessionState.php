<?php
require 'auth_check.php';
require('openDB.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        //declare session variables
        $tablename = $_SESSION['table'];
        $username = $_SESSION['username'];

        $queryFetchTeamArray = "
      SELECT team_array
      FROM users
      WHERE username = :username
    ";

        $stmt = $file_db->prepare($queryFetchTeamArray);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $existingTeamArrayRaw = $stmt->fetchColumn();

        $existingTeams = json_decode($existingTeamArrayRaw ?? '[]', true);

        if ($existingTeams === null && $existingTeamArrayRaw !== '[]' && $existingTeamArrayRaw !== '') {
            throw new Exception("Invalid JSON format in database.");
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'isEmpty' => empty($existingTeams),
        'teams'   => $existingTeams ?: [] // Ensure we always return an array, never null
    ]);

    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'error' => 'Failed to fetch teams',
            /* 'details' => $e->getMessage() // Only show this in development! */
        ]);
    }
} else {
    // Handle invalid requests (e.g., direct access to this file)
    echo "Invalid request";
}
?>
