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
    // Log the error to a secure log file
    error_log('Database connection error: ' . $e->getMessage());

    // Display a generic error message to the user
    echo 'An unexpected error occurred. Please try again later.';
    exit();
}
?>
