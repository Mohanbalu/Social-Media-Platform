<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your database password
$dbname = "usersdata"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle content creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_content'])) {
    $title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
    $content = htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8');
    $userId = $_SESSION['user_id'];

    // Insert new content into the database
    $sql = "INSERT INTO content (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $userId, $title, $content);
    $stmt->execute();

    // Redirect to a confirmation or success page
    header("Location: content_list.php");
    exit;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Content</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.html">Home</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php" class="navbar-right">Logout</a>
    </div>

    <div class="content-creation-container">
        <h1>Create New Content</h1>
        <form action="" method="post">
            <input type="text" name="title" placeholder="Content Title" required>
            <textarea name="content" placeholder="Content Body" rows="10" required></textarea>
            <button type="submit" name="create_content">Create Content</button>
        </form>
    </div>
</body>
</html>
