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

// Fetch user_id from GET parameter
if (!isset($_GET['user_id'])) {
    echo "User ID is required";
    exit;
}

$profileUserId = intval($_GET['user_id']);

// Fetch user data
$sql = "SELECT u.username, p.bio, pi.profile_image FROM users u
        LEFT JOIN user_profiles p ON u.id = p.user_id
        LEFT JOIN profile_images pi ON u.id = pi.user_id
        WHERE u.id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userProfile = $result->fetch_assoc();
} else {
    echo "No user found";
    exit;
}

// Fetch logged-in user id
$loggedInUser = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();
$loggedInUserId = $result->fetch_assoc()['id'];

// Check follow status
$sqlFollowStatus = "SELECT COUNT(*) AS is_following FROM user_followers WHERE follower_id=? AND following_id=?";
$stmt = $conn->prepare($sqlFollowStatus);
$stmt->bind_param("ii", $loggedInUserId, $profileUserId);
$stmt->execute();
$resultFollowStatus = $stmt->get_result();
$isFollowing = $resultFollowStatus->fetch_assoc()['is_following'] > 0;

// Handle follow/unfollow actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['follow_user_id'])) {
    $followUserId = intval($_POST['follow_user_id']);
    if (!$isFollowing) {
        // Follow user
        $sqlFollow = "INSERT INTO user_followers (follower_id, following_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sqlFollow);
        $stmt->bind_param("ii", $loggedInUserId, $followUserId);
        $stmt->execute();
    } else {
        // Unfollow user
        $sqlUnfollow = "DELETE FROM user_followers WHERE follower_id=? AND following_id=?";
        $stmt = $conn->prepare($sqlUnfollow);
        $stmt->bind_param("ii", $loggedInUserId, $followUserId);
        $stmt->execute();
    }
    header("Location: user_profile.php?user_id=" . $profileUserId);
    exit;
}

// Fetch the number of followers
$sqlFollowers = "SELECT COUNT(*) AS followers FROM user_followers WHERE following_id=?";
$stmt = $conn->prepare($sqlFollowers);
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$resultFollowers = $stmt->get_result();
$followersCount = $resultFollowers->fetch_assoc()['followers'];

// Fetch the number of users being followed
$sqlFollowing = "SELECT COUNT(*) AS following FROM user_followers WHERE follower_id=?";
$stmt = $conn->prepare($sqlFollowing);
$stmt->bind_param("i", $profileUserId);
$stmt->execute();
$resultFollowing = $stmt->get_result();
$followingCount = $resultFollowing->fetch_assoc()['following'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userProfile['username']); ?>'s Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #333;
            padding: 1em;
            color: #fff;
        }
        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em;
        }
        .navbar-right {
            float: right;
        }
        .profile-container {
            max-width: 800px;
            margin: 2em auto;
            padding: 2em;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-info {
            margin: 1em 0;
        }
        .button-container {
            margin-top: 1em;
        }
        .button-container button,
        .button-container form button {
            padding: 0.5em 1em;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .button-container button:disabled {
            background-color: #ddd;
            cursor: not-allowed;
        }
        .button-container form button {
            background-color: #007bff;
            color: #fff;
        }
        .button-container form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
        <div class="navbar-right">
            <a href="home.php">Home</a>
            <a href="messages.php">Messages</a>
            <a href="notifications.php">Notifications</a>
        </div>
    </div>

    <div class="profile-container">
        <img src="<?php echo $userProfile['profile_image'] ? 'data:image/jpeg;base64,' . base64_encode($userProfile['profile_image']) : 'default-profile.jpg'; ?>" alt="Profile Image" class="profile-image">

        <div class="profile-info">
            <h1><?php echo htmlspecialchars($userProfile['username']); ?></h1>
            <p>Bio: <?php echo htmlspecialchars($userProfile['bio']); ?></p>
            <p>Followers: <?php echo $followersCount; ?></p>
            <p>Following: <?php echo $followingCount; ?></p>
        </div>

        <div class="button-container">
            <?php if (!$isFollowing): ?>
                <form method="POST" action="user_profile.php?user_id=<?php echo $profileUserId; ?>">
                    <input type="hidden" name="follow_user_id" value="<?php echo $profileUserId; ?>">
                    <button type="submit">Follow</button>
                </form>
            <?php else: ?>
                <form method="POST" action="user_profile.php?user_id=<?php echo $profileUserId; ?>">
                    <input type="hidden" name="follow_user_id" value="<?php echo $profileUserId; ?>">
                    <button type="submit">Unfollow</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
