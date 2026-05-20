<?php
require_once "../../connection.php";

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

$lastId = $_GET['lastId'] ?? PHP_INT_MAX;
global $conn;

$stmt = $conn->prepare("
    SELECT h.id, h.payload, h.created_at, u.USERNAME, s.query
    FROM history_table h 
    JOIN users_table u ON h.user_id = u.USER_ID
    JOIN searches_table s ON h.id = s.id
    WHERE h.id < ?
    ORDER BY h.id DESC
    LIMIT 1
");
$stmt->bind_param("i", $lastId);
$stmt->execute();
$result = $stmt->get_result();

if ($lastId == 0x7fffffff || $lastId == PHP_INT_MAX || $result->num_rows < 1) {
    header("Cache-Control: no-cache");
    header( "ETag: " . mt_rand(0, mt_getrandmax()));
} else {
    header("Cache-Control: public");
    $offset = 60 * 60 * 24 * 3;
    header( "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT");
}
$history = array();

while ($row = $result->fetch_assoc()) {
    header( "ETag: " . $row['id']);
    $datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
    $datetime->setTimezone(new DateTimeZone('Asia/Manila'));
    $formatted_datetime = $datetime->format('Y M. d H:i:s');

    $row['payload'] = json_decode($row['payload'], true);
    $history = array(
        'id' => $row['id'],
        'payload' => $row['payload'],
        'datetime' => $formatted_datetime,
        'username' => $row['USERNAME'],
        'query' => $row['query']
    );
    $stmt->close();

    die(json_encode($history));
}


