<?php
require_once "connection.php";

if(!isset($_SESSION["current_user"])) {
    die('<script>alert("User authentication failed.")</script>');die();
}

global $conn;

$currentId = $_GET["lastId"] ?? PHP_INT_MAX;
$stmt = $conn->prepare("SELECT id, query, query_filename, payload FROM searches_table WHERE user_id = ? AND id < ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("ii", $_SESSION['current_user'], $currentId);
$stmt->execute();
$result = $stmt->get_result();

if ($currentId == 0x7fffffff || $currentId == PHP_INT_MAX || $result->num_rows < 1) {
    header("Cache-Control: no-cache");
    header( "ETag: " . mt_rand(0, mt_getrandmax()));
} else {
    header("Cache-Control: public");
    $offset = 60 * 60 * 24 * 3;
    header( "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
}

while ($row = $result->fetch_assoc()) {
    header( "ETag: " . $row['id']);
    $row['payload'] = json_decode($row['payload'], true);
    $stmt->close();
    die(json_encode($row));
}

