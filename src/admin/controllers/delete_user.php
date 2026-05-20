<?php
require_once '../../connection.php';

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

if ($_SERVER["REQUEST_METHOD"] == 'DELETE') {
    global $conn;

    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM users_table WHERE `USER_ID`=?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute() === TRUE) {
        echo json_encode(array("message" => "User deleted successfully"));
    } else {
        echo json_encode(array("error" => "Error: " . $stmt->error));
    }

} else {
    echo json_encode(array("error" => "Invalid request method"));
}