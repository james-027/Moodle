<?php

require_once(__DIR__ . '/../../config.php');


function insert_record_to_second_db($record, $db_config) {
    global $DB;

    $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

    if (!$externalDb) {
        // Handle connection error if needed
        die('Connection to the second database failed: ' . mysqli_connect_error());
    }

    // Build the SQL query for insertion
    $sql = "INSERT INTO moodle_demo (user_email, user_name, phone_number, file_path, added_by, added_time)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($externalDb, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssss', $record->user_email, $record->user_name, $record->phone_number, $record->file_path, $record->added_by, $record->added_time);

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Get the insert ID if needed
    $insertID = mysqli_insert_id($externalDb);

    // Close the statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($externalDb);

    return $insertID;
}




function fetch_data_from_second_db($user_id, $db_config) {
    // Establish a direct connection to the second database
    $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

    if (!$externalDb) {
        // Handle connection error if needed
        die('Connection to the second database failed: ' . mysqli_connect_error());
    }

    // Build the SQL query for fetching data
    $sql = "SELECT * FROM form_tbl";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($externalDb, $sql);
    if (!$stmt) {
        // Handle query preparation error
        die('Query preparation failed: ' . mysqli_error($externalDb));
    }
    // Execute the prepared statement
    if (!mysqli_stmt_execute($stmt)) {
        // Handle query execution error
        die('Query execution failed: ' . mysqli_error($externalDb));
    }

    // Get result set
    $result = mysqli_stmt_get_result($stmt);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($externalDb);

    return $data;
}



function fetch_data_from_bel_db($db_config) {
    // Establish a direct connection to the second database
    $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

    if (!$externalDb) {
        // Handle connection error if needed
        die('Connection to the second database failed: ' . mysqli_connect_error());
    }

    // Build the SQL query for fetching data
    $sql = "SELECT * FROM `form_section_tbl` WHERE `form_id` = '1' AND `status` = 1 ";

    // Prepare and bind parameters
    $stmt = mysqli_prepare($externalDb, $sql);
    if (!$stmt) {
        // Handle query preparation error
        die('Query preparation failed: ' . mysqli_error($externalDb));
    }
    // Execute the prepared statement
    if (!mysqli_stmt_execute($stmt)) {
        // Handle query execution error
        die('Query execution failed: ' . mysqli_error($externalDb));
    }

    // Get result set
    $result = mysqli_stmt_get_result($stmt);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the statement and connection
    mysqli_stmt_close($stmt);
    mysqli_close($externalDb);

    return $data;
}

function fetch_options_bel_db($db_config) {
     // Establish a direct connection to the second database
     $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

     if (!$externalDb) {
         // Handle connection error if needed
         die('Connection to the second database failed: ' . mysqli_connect_error());
     }

     // Build the SQL query for fetching data
     $sql = "SELECT * FROM `form_field_choice_tbl` WHERE `status` = 1 ";
 
     // Prepare and bind parameters
     $stmt = mysqli_prepare($externalDb, $sql);
     if (!$stmt) {
         // Handle query preparation error
         die('Query preparation failed: ' . mysqli_error($externalDb));
     }
     // Execute the prepared statement
     if (!mysqli_stmt_execute($stmt)) {
         // Handle query execution error
         die('Query execution failed: ' . mysqli_error($externalDb));
     }
 
     // Get result set
     $result = mysqli_stmt_get_result($stmt);
 
     // Fetch data as an associative array
     $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
     // Close the statement and connection
     mysqli_stmt_close($stmt);
     mysqli_close($externalDb);
 
     return $data;
}
function fetch_sections_bel_db($db_config) {
     // Establish a direct connection to the second database
     $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

     if (!$externalDb) {
         // Handle connection error if needed
         die('Connection to the second database failed: ' . mysqli_connect_error());
     }

     // Build the SQL query for fetching data
     $sql = "SELECT * FROM `form_section_tbl` WHERE `status` = 1 ";
 
     // Prepare and bind parameters
     $stmt = mysqli_prepare($externalDb, $sql);
     if (!$stmt) {
         // Handle query preparation error
         die('Query preparation failed: ' . mysqli_error($externalDb));
     }
     // Execute the prepared statement
     if (!mysqli_stmt_execute($stmt)) {
         // Handle query execution error
         die('Query execution failed: ' . mysqli_error($externalDb));
     }
 
     // Get result set
     $result = mysqli_stmt_get_result($stmt);
 
     // Fetch data as an associative array
     $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
     // Close the statement and connection
     mysqli_stmt_close($stmt);
     mysqli_close($externalDb);
 
     return $data;
}
function fetch_field_bel_db($db_config) {
     // Establish a direct connection to the second database
     $externalDb = mysqli_connect($db_config['dbhost'], $db_config['dbuser'], $db_config['dbpass'], $db_config['dbname']);

     if (!$externalDb) {
         // Handle connection error if needed
         die('Connection to the second database failed: ' . mysqli_connect_error());
     }

     // Build the SQL query for fetching data
     $sql = "SELECT a.*, b.*
        FROM form_section_tbl a
        JOIN form_field_tbl b ON a.section_id = b.section_id
        WHERE b.status = 1
        ORDER BY b.form_field_sequence ASC";

 
     // Prepare and bind parameters
     $stmt = mysqli_prepare($externalDb, $sql);
     if (!$stmt) {
         // Handle query preparation error
         die('Query preparation failed: ' . mysqli_error($externalDb));
     }
     // Execute the prepared statement
     if (!mysqli_stmt_execute($stmt)) {
         // Handle query execution error
         die('Query execution failed: ' . mysqli_error($externalDb));
     }
 
     // Get result set
     $result = mysqli_stmt_get_result($stmt);
 
     // Fetch data as an associative array
     $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
 
     // Close the statement and connection
     mysqli_stmt_close($stmt);
     mysqli_close($externalDb);
 
     return $data;
}