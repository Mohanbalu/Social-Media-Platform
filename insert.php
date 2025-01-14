<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $phoneCode = $_POST['phnCode'];
    $phone = $_POST['phn'];
    $password = $_POST['password'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Check if any field is empty
    if (empty($username) || empty($phoneCode) || empty($phone) || empty($password) || empty($dob) || empty($gender)) {
        echo "All fields are required";
        die(); // Terminate script execution
    }

    // Database connection parameters
    $host = "localhost";
    $dbUsername = "root";
    $dbPassword = ""; 
    $dbname = "databaseuu"; // Specify your database name

    // Create database connection
    $conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO registeruu (username, phncode, phn, password, dob, gender) VALUES (?, ?, ?, ?, ?, ?)");

    // Bind parameters to the prepared statement
    $stmt->bind_param("ssssss", $username, $phoneCode, $phone, $password, $dob, $gender);

    // Execute the prepared statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
