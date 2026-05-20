<?php require_once "../../connection.php";

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

global $conn;

$stmt = $conn->prepare("
    SELECT l.id, l.ip, l.created_at, u.USERNAME 
    FROM logs_table l 
    JOIN users_table u ON l.user_id = u.USER_ID 
    ORDER BY l.id DESC
");
$stmt->execute();
$result = $stmt->get_result();

$logs = array();
while ($row = $result->fetch_assoc()) {
    $datetime = new DateTime($row['created_at'], new DateTimeZone('UTC'));
    $datetime->setTimezone(new DateTimeZone('Asia/Manila'));
    $formatted_datetime = $datetime->format('Y M. d H:i:s');

    $logs[] = array(
        'id' => $row['id'],
        'ip' => $row['ip'],
        'time' => $formatted_datetime,
        'username' => $row['USERNAME']
    );
}
echo json_encode($logs);

$stmt->close();