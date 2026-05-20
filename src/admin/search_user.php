<?php
require_once '../connection.php';
require_once '../functions.php';

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

global $conn;

$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
$searchQuery = "%$searchQuery%";
$stmt = $conn->prepare("SELECT * FROM users_table WHERE SURNAME LIKE ? OR FIRSTNAME LIKE ?");
$stmt->bind_param("ss", $searchQuery, $searchQuery);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$users = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo 'No user found.';
}

header('Content-Type: application/json');
echo json_encode($users);