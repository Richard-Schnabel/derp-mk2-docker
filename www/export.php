<?php
    // Set the database credentials$server = "sql7.freesqldatabase.com";
    $hostname = "sql7.freesqldatabase.com";
    $username = "sql7600278";
    $password = "IGl6XIDQWs";
    $database = "sql7600278";

    // Create a database connection
    $mysqli = new mysqli($hostname, $username, $password, $database);

    // Check the connection
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Get all the tables from the database
    $tables = array();
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    // Loop through the tables and export each one
    $output = "";
    foreach ($tables as $table) {
        $result = $mysqli->query("SELECT * FROM $table");
        $num_fields = $result->field_count;

        $output .= "DROP TABLE IF EXISTS $table;\n";
        $row2 = $mysqli->query("SHOW CREATE TABLE $table")->fetch_row();
        $output .= $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = $result->fetch_row()) {
                $output .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $output .= "'" . $row[$j] . "'";
                    } else {
                        $output .= "''";
                    }
                    if ($j < ($num_fields - 1)) {
                        $output .= ",";
                    }
                }
                $output .= ");\n";
            }
        }
        $output .= "\n\n";
    }

    // Set the file name and content type
    $filename = "database_backup_" . date("Y-m-d_H-i-s") . ".sql";
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Output the backup file
    echo $output;

    // Close the database connection
    $mysqli->close();  
?>