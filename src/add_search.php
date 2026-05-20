<?php
require_once "connection.php";

header('Content-Type: application/json');
if (!isset($_SESSION['current_user'])) {
    http_response_code(403);
    echo json_encode(['message' => 'User authentication failed.']);
    die();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid data.']);
    exit;
}

$current_user = $_SESSION['current_user'];
$fileData = $data['file'];
$filename = $data['filename'];
$results = json_encode($data['results']);

global $conn;

$stmt = $conn->prepare("INSERT INTO searches_table (user_id, query, query_filename, payload) VALUES (?, ?, ?, ?)");
echo $conn->error;
$stmt->bind_param("isss", $current_user, $fileData, $filename, $results);
$stmt->execute();
$stmt->close();

$sth = $conn->prepare("INSERT INTO history_table (user_id, payload) VALUES (?, ?)");
$sth->bind_param("is", $_SESSION['current_user'], $results);
$sth->execute();
$sth->close();
