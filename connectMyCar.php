<?php

    $serverName = 'localhost';
    $username = 'root';
    $password = 'root';
    $database = 'mycar';

    $connMyCar = new mysqli($serverName, $username, $password, $database);

    if ($connMyCar->connect_error) {
        die("Connection failed: " . $connMyCar->connect_error );
    }

?>
