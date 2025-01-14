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

// Fetch logged-in user data
$loggedInUser = $_SESSION['username'];
$sql = "SELECT id, username FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $userId = $user['id'];
} else {
    echo "No user found";
    exit;
}

// Check if user profile exists in user_profiles table
$sqlProfileCheck = "SELECT * FROM user_profiles WHERE user_id=?";
$stmt = $conn->prepare($sqlProfileCheck);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultProfileCheck = $stmt->get_result();

if ($resultProfileCheck->num_rows > 0) {
    $profile = $resultProfileCheck->fetch_assoc();
} else {
    // If no profile exists, create a new profile with default values
    $sqlInsertProfile = "INSERT INTO user_profiles (user_id, bio, followers, following) VALUES (?, '', 0, 0)";
    $stmt = $conn->prepare($sqlInsertProfile);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Fetch the newly created profile
    $stmt = $conn->prepare($sqlProfileCheck);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $resultProfileCheck = $stmt->get_result();
    $profile = $resultProfileCheck->fetch_assoc();    
}

// Fetch profile image from profile_images table
$sqlImage = "SELECT profile_image FROM profile_images WHERE user_id=?";
$stmt = $conn->prepare($sqlImage);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultImage = $stmt->get_result();

$profileImage = null;
if ($resultImage->num_rows > 0) {
    $imageRow = $resultImage->fetch_assoc();
    $profileImage = $imageRow['profile_image'];
}

// Count followers and following
$sqlFollowers = "SELECT COUNT(*) AS followers_count FROM user_followers WHERE following_id=?";
$stmt = $conn->prepare($sqlFollowers);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultFollowers = $stmt->get_result();
$followersCount = $resultFollowers->fetch_assoc()['followers_count'];

$sqlFollowing = "SELECT COUNT(*) AS following_count FROM user_followers WHERE follower_id=?";
$stmt = $conn->prepare($sqlFollowing);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultFollowing = $stmt->get_result();
$followingCount = $resultFollowing->fetch_assoc()['following_count'];

// Handle profile updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $bio = htmlspecialchars($_POST['bio'], ENT_QUOTES, 'UTF-8');
    if (!empty($_FILES["profile_image"]["name"])) {
        if ($_FILES['profile_image']['error'] != UPLOAD_ERR_OK) {
            die('Upload failed with error code ' . $_FILES['profile_image']['error']);
        }
    
        // Get file info
        $fileName = basename($_FILES["profile_image"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
    
        // Allow certain file formats
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            $image = $_FILES['profile_image']['tmp_name'];
            $imgContent = file_get_contents($image);
    
            // Check if an image already exists for this user
            $sqlCheckImage = "SELECT id FROM profile_images WHERE user_id=?";
            $stmt = $conn->prepare($sqlCheckImage);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $resultCheckImage = $stmt->get_result();
    
            if ($resultCheckImage->num_rows > 0) {
                // Update existing image
                $sqlUpdateImage = "UPDATE profile_images SET profile_image = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sqlUpdateImage);
                $stmt->send_long_data(0, $imgContent); // Send the binary data
                $stmt->bind_param("bi", $imgContent, $userId);
            } else {
                // Insert new image
                $sqlInsertImage = "INSERT INTO profile_images (user_id, profile_image) VALUES (?, ?)";
                $stmt = $conn->prepare($sqlInsertImage);
                $stmt->bind_param("ib", $userId, $imgContent);
            }
    
            if ($stmt->execute()) {
                echo "Profile image updated successfully.";
            } else {
                echo "Error updating profile image: " . $stmt->error;
            }
        } else {
            echo 'Sorry, only JPG, JPEG, PNG, & GIF files are allowed to upload.';
        }
    }
    
    // Update user profile in the database
    $sqlUpdate = "UPDATE user_profiles SET bio=? WHERE user_id=?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("si", $bio, $userId);
    $stmt->execute();

    // Refresh the page to show updated profile
    header("Location: profile.php");
    exit;
}

// Fetch suggested users (excluding the logged-in user)
$sqlSuggestions = "SELECT u.id, u.username, p.profile_image FROM users u LEFT JOIN profile_images p ON u.id = p.user_id WHERE u.id != ?";
$stmt = $conn->prepare($sqlSuggestions);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultSuggestions = $stmt->get_result();

// Fetch follow status for each suggested user
$suggestions = [];
while ($suggestion = $resultSuggestions->fetch_assoc()) {
    $suggestionId = $suggestion['id'];

    // Check if the current user is following this suggested user
    $sqlFollowStatus = "SELECT COUNT(*) AS is_following FROM user_followers WHERE follower_id=? AND following_id=?";
    $stmt = $conn->prepare($sqlFollowStatus);
    $stmt->bind_param("ii", $userId, $suggestionId);
    $stmt->execute();
    $resultFollowStatus = $stmt->get_result();
    $isFollowing = $resultFollowStatus->fetch_assoc()['is_following'] > 0;

    $suggestion['is_following'] = $isFollowing;
    $suggestions[] = $suggestion;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .navbar {
            background-color: #2c3e50;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            float: left;
            display: block;
            color: #ecf0f1;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-weight: 500;
        }

        .navbar a:hover {
            background-color: #34495e;
            color: #ecf0f1;
        }

        .navbar .navbar-right {
            float: right;
        }

        .navbar .navbar-right a {
            padding: 14px 20px;
            color: #ecf0f1;
            font-size: 18px;
        }

        .navbar .navbar-right a:hover {
            background-color: #34495e;
            color: #ecf0f1;
        }

        .profile-container {
            max-width: 800px;
            margin: 80px auto 20px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px auto;
            display: block;
            border: 4px solid #3498db;
        }

        .profile-info {
            text-align: center;
        }

        .profile-info h1 {
            font-size: 24px;
            margin: 10px 0;
        }

        .profile-info p {
            font-size: 16px;
            color: #7f8c8d;
        }

        .bio-textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-top: 20px;
            resize: none;
        }

        .form-group {
            margin-top: 20px;
            text-align: center;
        }

        .form-group label {
            display: inline-block;
            margin-bottom: 10px;
        }

        .profile-image-input {
            display: block;
            margin: 0 auto 10px auto;
        }

        .btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #2980b9;
        }

        .followers-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .followers-info div {
            text-align: center;
            flex: 1;
        }

        .suggested-users {
            margin-top: 40px;
        }

        .suggested-user {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .suggested-user img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .suggested-user h4 {
            font-size: 18px;
            margin: 0;
            flex-grow: 1;
        }

        .suggested-user button {
            padding: 6px 12px;
            background-color: #2ecc71;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .suggested-user button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="#">Home</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php" class="navbar-right">Logout</a>
</div>

<div class="profile-container">
    <img src="data:image/jpeg;base64,<?= base64_encode($profileImage) ?>" alt="Profile Image" class="profile-image">
    <div class="profile-info">
        <h1><?= htmlspecialchars($loggedInUser, ENT_QUOTES, 'UTF-8') ?></h1>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="bio">Bio:</label>
                <textarea id="bio" name="bio" class="bio-textarea"><?= htmlspecialchars($profile['bio'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group">
                <label for="profile_image">Update Profile Image:</label>
                <input type="file" name="profile_image" id="profile_image" class="profile-image-input">
            </div>
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>
    </div>

    <div class="followers-info">
        <div>
            <strong>Followers</strong>
            <p><?= $followersCount ?></p>
        </div>
        <div>
            <strong>Following</strong>
            <p><?= $followingCount ?></p>
        </div>
    </div>

    <div class="suggested-users">
        <h3>Suggested Users to Follow</h3>
        <?php foreach ($suggestions as $suggestedUser): ?>
            <div class="suggested-user">
                <img src="data:image/jpeg;base64,<?= base64_encode($suggestedUser['profile_image']) ?>" alt="Profile Image">
                <h4><?= htmlspecialchars($suggestedUser['username'], ENT_QUOTES, 'UTF-8') ?></h4>
                <?php if ($suggestedUser['is_following']): ?>
                    <button disabled>Following</button>
                <?php else: ?>
                    <button>Follow</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
