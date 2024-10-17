<?php
require 'openDB.php';

function Debug_To_console($data) 
{
    $output = $data;
    if (is_array($output)) {
        $output = implode(',', $output);
    }
    echo "<script>console.log(`Debug Objects: " . $output . "`);</script>";
}

try {
    $queryCreateTableDebut = "
    CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT UNIQUE NOT NULL,
    timeStamp TEXT,
    password TEXT NOT NULL,
    team_array TEXT,
    algorithm TEXT,
    numCourts TEXT DEFAULT '1'
    )
    ";
    $file_db->exec($queryCreateTableDebut);

    $queryCreateTablePlayers = "
    CREATE TABLE IF NOT EXISTS players (
    id INTEGER PRIMARY KEY NOT NULL,
    name TEXT,
    level TEXT,
    tablename TEXT,
    active BOOLEAN
    )
    ";
    $file_db->exec($queryCreateTablePlayers);
}
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
}

$file_db = null;
?>
