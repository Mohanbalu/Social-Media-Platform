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

// Get GET parameters
$sender_id = $_GET['sender_id'];
$receiver_id = $_GET['receiver_id'];

// Retrieve messages between the sender and receiver
$sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

// Return messages as JSON
header('Content-Type: application/json');
echo json_encode($messages);

$stmt->close();
$conn->close();
?>
