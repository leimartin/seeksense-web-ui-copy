<?php
require_once '../../connection.php';
require_once '../../functions.php';

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);

    $id = $data['USER_ID'];
    $role = $data['ROLE'];
    $surname = $data['SURNAME'];
    $firstname = $data['FIRSTNAME'];
    $position = $data['POSITION'];
    $email = $data['EMAIL'];
    $contactNum = $data['CONTACTNUM'];
    $username = $data['USERNAME'];

    if (isset($data['PASSWORD']) && !empty($data['PASSWORD'])) {
        $password = hash_pwd($data['PASSWORD']);
        $stmt = $conn->prepare("UPDATE users_table SET `ROLE` = ?, `SURNAME` = ?, `FIRSTNAME` = ?, `POSITION` = ?, `EMAIL` = ?, `CONTACTNUM` = ?, `USERNAME` = ?, `PASSWORD` = ? WHERE `USER_ID` = ?");
        $stmt->bind_param("ssssssssi", $role, $surname, $firstname, $position, $email, $contactNum, $username, $password, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users_table SET `ROLE` = ?, `SURNAME` = ?, `FIRSTNAME` = ?, `POSITION` = ?, `EMAIL` = ?, `CONTACTNUM` = ?, `USERNAME` = ? WHERE `USER_ID` = ?");
        $stmt->bind_param("sssssssi", $role, $surname, $firstname, $position, $email, $contactNum, $username, $id);
    }

    if ($stmt->execute()) {
        echo json_encode(array("message" => "User details updated successfully"));
    } else {
        echo json_encode(array("error" => "Error updating user details: " . $stmt->error));
    }

    $stmt->close();
} else {
    http_response_code(405);
    echo json_encode(array("error" => "only PUT requests are allowed"));
}
