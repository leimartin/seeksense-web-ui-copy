<?php

require_once "../../connection.php";

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}

global $conn;

$batch_size = 1;
$offset = 0;


$stmt = $conn->prepare("SELECT count(*) FROM searches_table LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $batch_size);
$stmt->execute();

$url = 'http://seeksense_embeddings/v1/face/stats';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$faceStats = json_decode(curl_exec($ch), true);
curl_close($ch);

$response = [
    'searches' => $stmt->get_result()->fetch_assoc()["count(*)"],
    'posts' => $faceStats["images"],
    'faces' => $faceStats["faces"]
];

die(json_encode($response));
