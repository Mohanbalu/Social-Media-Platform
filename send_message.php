<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usersdata";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data
$sender_id = $_POST['sender_id'];
$receiver_id = $_POST['receiver_id'];
$message = $_POST['message'];

// Insert message into database
$sql = "INSERT INTO messages (sender_id, receiver_id, message, status) VALUES (?, ?, ?, 'sent')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $sender_id, $receiver_id, $message);
$stmt->execute();

$stmt->close();
$conn->close();
?>
