<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli("mariadb", "root", "12345", "seeksense_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_cache_limiter(false);
session_start();

$create_query = "CREATE TABLE users_table (
        USER_ID INT AUTO_INCREMENT PRIMARY KEY,
        ROLE VARCHAR(64) NOT NULL,
        SURNAME VARCHAR(100) NOT NULL,
        FIRSTNAME VARCHAR(100) NOT NULL, 
        POSITION VARCHAR(100) NOT NULL,
        EMAIL VARCHAR(100) NOT NULL,
        CONTACTNUM VARCHAR(64) NOT NULL,
        USERNAME VARCHAR(64) NOT NULL,
        PASSWORD VARCHAR(64) NOT NULL,
        CREATED_AT TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

if ($conn->query($create_query) === TRUE ) {
    echo '<script>console.log("users table created successfully.")</script>';
}

$searches_query = "CREATE TABLE IF NOT EXISTS searches_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    query LONGBLOB,
    query_filename TEXT,
    payload LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users_table(USER_ID)
)";

if ($conn->query($searches_query) !== TRUE) {
    die('<script>console.log("error creating tables: ' . $conn->error . '")</script>');
}

$logs_query = "CREATE TABLE IF NOT EXISTS logs_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users_table(USER_ID)
)";

if ($conn->query($logs_query) !== TRUE ) {
    die('<script>console.log("error creating tables: ' . $conn->error . '")</script>');
}

$history_query = "CREATE TABLE IF NOT EXISTS history_table (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    payload LONGBLOB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users_table(USER_ID)
)";

if ($conn->query($history_query) !== TRUE ) {
    die('<script>console.log("error creating tables: ' . $conn->error . '")</script>');
}

$ip_whitelist_query = "CREATE TABLE IF NOT EXISTS ip_whitelist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) UNIQUE NOT NULL
)";

if ($conn->query($ip_whitelist_query) !== TRUE ) {
    die('<script>console.log("error creating tables: ' . $conn->error . '")</script>');
}

$invalid_access_query = "CREATE TABLE IF NOT EXISTS invalid_access (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip VARCHAR(45) NOT NULL,
    headers TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($invalid_access_query) !== TRUE ) {
    die('<script>console.log("error creating tables: ' . $conn->error . '")</script>');
}

function current_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function get_request_headers(): array {
    $headers = array();
    foreach($_SERVER as $key => $value) {
        if (substr($key, 0, 5) <> 'HTTP_') {
            continue;
        }
        $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$header] = $value;
    }
    return $headers;
}

$ip = current_ip();
$stmt = $conn->prepare("SELECT * FROM ip_whitelist WHERE ip = ?");
$stmt->bind_param("s", $ip);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if($result->num_rows < 1) {
    $headers = json_encode(get_request_headers());
    $stmt = $conn->prepare("INSERT INTO invalid_access (ip, headers) VALUES (?, ?)");
    $stmt->bind_param("ss", $ip, $headers);
    $stmt->execute();
    $stmt->close();
    die("<script>const ip='" . $ip . "';</script> Unauthorized access detected! This incident has been recorded.");
}
