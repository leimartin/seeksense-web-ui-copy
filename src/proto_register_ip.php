<?php

function current_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$conn = new mysqli("mariadb", "root", "12345", "seeksense_db");

$ip = current_ip();
$stmt = $conn->prepare("INSERT INTO ip_whitelist (ip) VALUES (?)");
$stmt->bind_param("s", $ip);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SeekSense</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="static/styles.css">
    <link rel="web icon" type="png" href="static/logo/seeksense-logo.png">
</head>
<body>
<p class="is-size-2 has-text-link has-text-centered m-6">You will be redirected to the login page in 3 seconds . . .</p>
<script>
        function return_to_login() {
            setTimeout(function () {
                window.location.replace('index.php');
                window.history.pushState(null, null, 'index.php');
            }, 3000);
        }
        window.onload = return_to_login;
</script>
</body>
</html>

