<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "usersdata"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = $_POST['action'];
$userId = $_POST['user_id'];
$loggedInUser = $_SESSION['username'];

$sql = "SELECT id FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();
$loggedInUserId = $result->fetch_assoc()['id'];

if ($action == 'follow') {
    $sql = "INSERT INTO user_followers (follower_id, following_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $loggedInUserId, $userId);
    $stmt->execute();
} else {
    $sql = "DELETE FROM user_followers WHERE follower_id=? AND following_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $loggedInUserId, $userId);
    $stmt->execute();
}

$conn->close();
?>
