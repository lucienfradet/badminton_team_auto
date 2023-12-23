<?php
try {
    /******************************************
    * Create databases and  open connections*
    ******************************************/
 
    // Create (connect to) SQLite database in file
    $file_db = new PDO('sqlite:../server/database/badminton_teams.db');
  // Set errormode to exceptions
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
                            PDO::ERRMODE_EXCEPTION);
   }
catch(PDOException $e) {
    // Print PDOException message
    echo $e->getMessage();
}
?>
