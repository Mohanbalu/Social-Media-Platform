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

$loggedInUser = $_SESSION['username'];
$sql = "SELECT id FROM users WHERE username=?";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postType = $_POST['post_type'];
    
    if ($postType == 'image' && !empty($_FILES['post_image']['name'])) {
        if ($_FILES['post_image']['error'] != UPLOAD_ERR_OK) {
            die('Upload failed with error code ' . $_FILES['post_image']['error']);
        }
        $fileName = basename($_FILES["post_image"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowTypes)) {
            $image = $_FILES['post_image']['tmp_name'];
            $imgContent = file_get_contents($image);
            $description = htmlspecialchars($_POST['post_description'], ENT_QUOTES, 'UTF-8');
            $sqlInsertPost = "INSERT INTO posts (user_id, post_type, post_content, post_image) VALUES (?, 'image', ?, ?)";
            $stmt = $conn->prepare($sqlInsertPost);
            $stmt->bind_param("isb", $userId, $description, $imgContent);
            $stmt->execute();
        } else {
            echo 'Only JPG, JPEG, PNG, & GIF files are allowed.';
        }
    } elseif ($postType == 'video' && !empty($_FILES['post_video']['name'])) {
        if ($_FILES['post_video']['error'] != UPLOAD_ERR_OK) {
            die('Upload failed with error code ' . $_FILES['post_video']['error']);
        }
        $fileName = basename($_FILES["post_video"]["name"]);
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowTypes = array('mp4', 'avi', 'mov', 'wmv');
        if (in_array($fileType, $allowTypes)) {
            $video = $_FILES['post_video']['tmp_name'];
            $videoContent = file_get_contents($video);
            $description = htmlspecialchars($_POST['post_description'], ENT_QUOTES, 'UTF-8');
            $sqlInsertPost = "INSERT INTO posts (user_id, post_type, post_content, post_video) VALUES (?, 'video', ?, ?)";
            $stmt = $conn->prepare($sqlInsertPost);
            $stmt->bind_param("isb", $userId, $description, $videoContent);
            $stmt->execute();
        } else {
            echo 'Only MP4, AVI, MOV, & WMV files are allowed.';
        }
    } elseif ($postType == 'content') {
        $content = htmlspecialchars($_POST['post_content'], ENT_QUOTES, 'UTF-8');
        $sqlInsertPost = "INSERT INTO posts (user_id, post_type, post_content) VALUES (?, 'content', ?)";
        $stmt = $conn->prepare($sqlInsertPost);
        $stmt->bind_param("is", $userId, $content);
        $stmt->execute();
    } elseif ($postType == 'poll') {
        $question = htmlspecialchars($_POST['poll_question'], ENT_QUOTES, 'UTF-8');
        $option1 = htmlspecialchars($_POST['poll_option1'], ENT_QUOTES, 'UTF-8');
        $option2 = htmlspecialchars($_POST['poll_option2'], ENT_QUOTES, 'UTF-8');
        $option3 = htmlspecialchars($_POST['poll_option3'], ENT_QUOTES, 'UTF-8');
        $option4 = htmlspecialchars($_POST['poll_option4'], ENT_QUOTES, 'UTF-8');
        $sqlInsertPost = "INSERT INTO posts (user_id, post_type, post_content) VALUES (?, 'poll', ?)";
        $stmt = $conn->prepare($sqlInsertPost);
        $stmt->bind_param("is", $userId, $question);
        $stmt->execute();
        $pollId = $stmt->insert_id;
        
        $sqlInsertOptions = "INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?), (?, ?), (?, ?), (?, ?)";
        $stmt = $conn->prepare($sqlInsertOptions);
        $stmt->bind_param("isisisis", $pollId, $option1, $pollId, $option2, $pollId, $option3, $pollId, $option4);
        $stmt->execute();
    }

    $conn->close();
    header("Location: profile.php");
    exit;
}
?>
