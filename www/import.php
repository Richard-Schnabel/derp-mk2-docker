<!DOCTYPE html>
<html lang='cs'>
<head>
    <title>Login - DERP</title>
    <meta charset='utf-8' />

    <!-- reference na další soubory -->
    <script src="script.js"></script>
    <link rel="stylesheet" href="style.css" />

    <!-- favicon -->
    <link rel="icon" type="image/x-icon" href="res/favicon.png" />
</head>
<body onload="OnLoad()">

    <!-- logo -->
    <header>
        <h1 onclick="location.href='.'">DERP</h1>
        <h2>Developer's Engine for Recording Programing</h2>
    </header>

    <!-- delici cara -->
    <div class="cerna_cara"></div>

    <!-- main -->
    <main class="gradientBG">

        <!-- back button -->
        <div class="div_ramecek round_malo div_pravo">
            <span class="div_text">Back</span>
            <img src="res/back.png" id="back_button" onclick="history.back()" class="round_malo" width="35px" />
        </div>

        <!-- main pole -->
        <article class="round_hodne">
            <?php

            // Set the database credentials
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

            // Get the uploaded SQL file name and path
            $sql_file = $_FILES["sql_file"]["name"];
            $sql_path = $_FILES["sql_file"]["tmp_name"];

            // Read the SQL file contents
            $sql_contents = file_get_contents($sql_path);

            // Execute the SQL file contents
            if ($mysqli->multi_query($sql_contents)) {
                echo "<h2 style='text-align:center'>SQL file imported successfully.<h2>";
            } else {
                echo "Error importing SQL file: " . $mysqli->error;
            }

            // Close the database connection
            $mysqli->close();
            ?>
        </article>
    </main>
</body>
</html>
