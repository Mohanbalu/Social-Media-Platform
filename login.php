<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usersdata";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // To prevent SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Verify user credentials
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Set session variables
        $_SESSION['username'] = $user['username'];

        // Redirect to a protected page (dashboard or similar)
        header("Location: home.html");
        exit;
    } else {
        echo "<script>alert('Invalid username or password. Please try again.'); window.location.href='login.html';</script>";
    }
}

$conn->close();
?>
