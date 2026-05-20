<?php
require_once "../../connection.php";

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

global $conn;

if (isset($_GET['username'])) {
    $username = $conn->real_escape_string($_GET['username']);

    $stmt = $conn->prepare("SELECT USER_ID FROM users_table WHERE USERNAME = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if($result->num_rows > 0) {
        echo json_encode(['available' => false]);
    } else {
        echo json_encode(['available' => true]);
    }
} else {
    echo json_encode(['error' => 'username not provided.']);
}