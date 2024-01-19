<?php
  require('openDB.php');

  function debug_to_console($data) {
    $output = $data;
    if (is_array($output)) {
    $output = implode(',', $output);
    }
    echo "<script>console.log(`Debug Objects: " . $output . "`);</script>";
  }

  try {
    // $queryCreateTableDebut = "
    //     CREATE TABLE IF NOT EXISTS group_debut (
    //         id INTEGER PRIMARY KEY NOT NULL,
    //         timeStamp TEXT,
    //         username TEXT,
    //         password TEXT,
    //         team_array TEXT
    //     )
    // ";
    // $file_db->exec($queryCreateTableDebut);
    //
    // $queryCreateTableInter = "
    //     CREATE TABLE IF NOT EXISTS group_inter (
    //         id INTEGER PRIMARY KEY NOT NULL,
    //         timeStamp TEXT,
    //         username TEXT,
    //         password TEXT,
    //         team_array TEXT
    //     )
    // ";
    // $file_db->exec($queryCreateTableInter);
    //
    // $queryCreateTableAdvan = "
    //     CREATE TABLE IF NOT EXISTS group_advan (
    //         id INTEGER PRIMARY KEY NOT NULL,
    //         timeStamp TEXT,
    //         username TEXT,
    //         password TEXT,
    //         team_array TEXT
    //     )
    // ";
    // $file_db->exec($queryCreateTableAdvan);

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
