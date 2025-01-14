<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
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

// Fetch current user data
$currentUsername = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username='$currentUsername'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $currentUser = $result->fetch_assoc();
    $currentUserId = $currentUser['id'];
} else {
    echo "No user found";
    exit;
}

// Fetch all user profiles excluding the current user
$sqlProfiles = "SELECT p.*, u.username 
                FROM user_profiles p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.user_id != ?";
$stmt = $conn->prepare($sqlProfiles);
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$resultProfiles = $stmt->get_result();

// Handle follow action
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow'])) {
    $followingId = $_POST['follow'];

    // Insert follow relationship into the database
    $sqlFollow = "INSERT INTO user_followers (follower_id, following_id) 
                  VALUES (?, ?)";
    $stmtFollow = $conn->prepare($sqlFollow);
    $stmtFollow->bind_param("ii", $currentUserId, $followingId);

    if ($stmtFollow->execute()) {
        // Update followers and following counts
        $sqlUpdateFollowers = "UPDATE user_profiles 
                               SET followers = followers + 1 
                               WHERE user_id = ?";
        $stmtUpdateFollowers = $conn->prepare($sqlUpdateFollowers);
        $stmtUpdateFollowers->bind_param("i", $followingId);
        $stmtUpdateFollowers->execute();

        $sqlUpdateFollowing = "UPDATE user_profiles 
                               SET following = following + 1 
                               WHERE user_id = ?";
        $stmtUpdateFollowing = $conn->prepare($sqlUpdateFollowing);
        $stmtUpdateFollowing->bind_param("i", $currentUserId);
        $stmtUpdateFollowing->execute();

        echo "You are now following the user!";
    } else {
        echo "Error following user: " . $conn->error;
    }
    $stmtFollow->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suggestions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .suggestions-container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
        }
        .user-card {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .user-card img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .user-card .info {
            flex-grow: 1;
        }
        .user-card .info h3 {
            margin: 0;
            font-size: 18px;
        }
        .user-card .info p {
            margin: 5px 0;
            color: #666;
        }
        .user-card .action {
            text-align: right;
        }
        .user-card .action button {
            padding: 8px 15px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .user-card .action button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="suggestions-container">
        <h1>Suggested Users</h1>
        <?php while ($profile = $resultProfiles->fetch_assoc()) : ?>
            <div class="user-card">
                <?php if ($profile['profile_image']) : ?>
                    <img src="uploads/<?php echo htmlspecialchars($profile['profile_image']); ?>" alt="Profile Image">
                <?php else : ?>
                    <img src="default.png" alt="Profile Image">
                <?php endif; ?>
                <div class="info">
                    <h3><?php echo htmlspecialchars($profile['username']); ?></h3>
                    <p>Bio: <?php echo htmlspecialchars($profile['bio']); ?></p>
                    <p>Followers: <?php echo htmlspecialchars($profile['followers']); ?> | Following: <?php echo htmlspecialchars($profile['following']); ?></p>
                </div>
                <div class="action">
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="follow" value="<?php echo htmlspecialchars($profile['user_id']); ?>">
                        <button type="submit">Follow</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
