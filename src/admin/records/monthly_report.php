<?php
require_once "../../connection.php";

if (!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

global $conn;

$lastId = $_GET['lastId'] ?? PHP_INT_MAX;
$start_time = date('Y-m-01 00:00:00');
$end_time = date('Y-m-t 23:59:59');

$stmt = $conn->prepare("
    SELECT l.id, l.ip, l.created_at, u.USERNAME, COUNT(DISTINCT s.id) AS num_rows
    FROM logs_table l 
    JOIN users_table u ON l.user_id = u.USER_ID 
    JOIN history_table h ON l.user_id = h.user_id
    LEFT JOIN searches_table s ON h.user_id = s.user_id AND s.created_at >= l.created_at AND s.created_at < (
        SELECT COALESCE(MIN(l2.created_at), '9999-12-31 23:59:59')
        FROM logs_table l2 
        WHERE l2.user_id = l.user_id AND l2.created_at > l.created_at
    )
    WHERE l.created_at BETWEEN ? AND ?
    GROUP BY l.id, l.ip, l.created_at, u.USERNAME
    ORDER BY l.id DESC
");
$stmt->bind_param("ss", $start_time, $end_time);
$stmt->execute();
$result = $stmt->get_result();

$report = array();

//foreach ($result as $row) {
while($row = $result->fetch_assoc()) {
    $datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
    $datetime->setTimezone(new DateTimeZone('Asia/Manila'));
    $formatted_datetime = $datetime->format('Y M. d H:i:s');

//    $row['payload'] = json_decode($row['payload'], true);
    $report[] = array(
        'id' => $row['id'],
        'ip' => $row['ip'],
        'datetime' => $formatted_datetime,
        'username' => $row['USERNAME'],
        'searches' => $row['num_rows'],
        'admin' => $_SESSION['current_user']
    );
}

$stmt->close();

echo json_encode($report);
