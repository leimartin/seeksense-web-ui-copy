<?php
global $conn;
require_once "connection.php";

$result = $conn->query("SELECT COUNT(*) as num_rows FROM users_table");
if ($result && ($row = $result->fetch_assoc()) && $row['num_rows'] > 0) {
    header('Location: /index.php');
    die("redirecting");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeekSense</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="static/styles.css">
    <link rel="web icon" type="png" href="static/logo/seeksense-logo.png">
</head>
<body>

<?php

global $conn;
$result = $conn->query("SELECT COUNT(*) as num_rows FROM users_table");
if ($result && ($row = $result->fetch_assoc()) && $row['num_rows'] == 0) { ?>
    <div id="setupPage">
        <div class="modal-card mt-5">
            <div class="column notification is-light is-fullhd mb-1" id="setup-page">
                <div class="columns">
                    <div class="column has-text-left">
                        <figure class="image is-64x64 is-pulled-left mx-2"><img src="./static/logo/seeksense-logo.png">
                        </figure>
                    </div>
                    <div class="column is-three-fifths has-text-centered is-size-4 has-text-weight-bold">SeekSense
                        <h2 class="subtitle">Setup</h2></div>
                    <div class="column has-text-right">
                        <figure class="image is-48x48 is-pulled-right mx-2"><img src="./static/logo/pnp.png"></figure>
                    </div>
                </div>
            </div>
            <!--   setup page   -->
            <?php include 'components/user_template.php'; ?>
        </div>
    </div>
<?php }

$conn->close();
?>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="static/setup.js"></script>
<script src="static/index.js"></script>
<script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool'></script>

</body>
</html>