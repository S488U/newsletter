<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "newsletter_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "<script>console.log('DB connected');</script>";
}
?>
