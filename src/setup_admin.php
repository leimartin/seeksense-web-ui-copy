<?php
require_once 'connection.php';
require_once 'functions.php';

global $conn;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = 1000;
    $role = 'Admin';
    $surname = $data['surname'];
    $firstname = $data['firstname'];
    $position = $data['position'];
    $email = $data['email'];
    $contactNum = $data['contactNum'];
    $username = $data['username'];
    $password = password();
    $hash_pwd = hash_pwd($password);

    if ($role == 'Admin') {
        $sth = $conn->prepare("SELECT MAX(USER_ID) AS MAX_ID FROM `users_table` WHERE `ROLE` = 'Admin'");
        $start = 1000;
    } else {
        $sth = $conn->prepare("SELECT MAX(USER_ID) AS MAX_ID FROM `users_table` WHERE `ROLE` = 'Standard'");
        $start = 1;
    }
    $sth->execute();
    $result = $sth->get_result();
    $row = $result->fetch_assoc();

    $id = ($row['MAX_ID'] !== null) ? $row['MAX_ID'] + 1 : $start;

    $stmt = $conn->prepare("INSERT INTO `users_table` (`USER_ID`, `ROLE`, `SURNAME`, `FIRSTNAME`, `POSITION`, `EMAIL`, `CONTACTNUM`, `USERNAME`, `PASSWORD`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("issssssss", $id, $role, $surname, $firstname, $position, $email, $contactNum, $username, $hash_pwd);
    $stmt->execute();
    $stmt->close();

    $response = array(
        "username" => $username,
        "password" => $password
    );

    echo json_encode($response);
    exit;
}


