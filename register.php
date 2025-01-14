<?php
// Connect to PostgreSQL
$conn = pg_connect("host=localhost dbname=usersdata user=postgres password=2004");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Insert data example
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $phone_code = $_POST['phnCode'];
    $phone_number = $_POST['phn'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // Prepare and execute the query
    $query = "INSERT INTO users (username, phone_code, phone_number, password, dob, gender)
              VALUES ($1, $2, $3, crypt($4, gen_salt('bf')), $5, $6)";
    $result = pg_query_params($conn, $query, array($username, $phone_code, $phone_number, $password, $dob, $gender));

    if ($result) {
        echo "Registration successful!";
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}
?>
