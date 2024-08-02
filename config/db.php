<?php
// Database configuration
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '/Otosorb110';
$dbName = 'crime-app';


// Create a database connection
$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
