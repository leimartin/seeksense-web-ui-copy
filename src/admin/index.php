<?php
require_once "../connection.php";
require_once "../functions.php";

if (!isset($_SESSION['current_user'])) {
    header('Location: ../index.php');
    die();
}
if ($_SESSION['current_user'] <= 999) {
    header('Location: ../seeksense.php');
    die();
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="web icon" type="png" href="../static/logo/seeksense-logo.png">
    <link rel="stylesheet" href="../static/styles.css">
</head>
<body>

<nav class="navbar  has-background-light px-3 py-2" role="navigation" aria-label="main navigation">
    <h2 class="is-size-3  has-text-weight-bold mx-2">SeekSense</h2>
    <div class="navbar-end is-hidden-touch">
        <div class="navbar-item">
            <a href="../seeksense.php" class="button is-light" style="border-right: 1px solid #ccc;">Home</a>
            <div class="navbar-item has-dropdown is-hoverable" style="border-right: 1px solid #ccc;">
                <a class="navbar-link">Manage User</a>
                <div class="navbar-dropdown">
                    <a class="navbar-item js-modal-trigger px-3" data-target="createUser">Create User Form</a>
                    <a class="navbar-item js-modal-trigger px-3" data-target="user-list">View All Users</a>
                </div>
            </div>
            <a href="../logout.php" class="button is-light">Log Out</a>
        </div>
    </div>
</nav>

<!-- CRUD op by admin -->
<div id="adminPage">
    <div class="modal" id="createUser">
        <div class="modal-background"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">Create User Profile</p>
                <button class="delete" aria-label="close" id="close-btn"></button>
            </header>
            <?php include '../components/user_template.php'; ?>
        </div>
    </div>
</div>

<div class="modal" id="user-list">
    <div class="modal-background"></div>
    <div class="modal-card" style="width: 80%; /*height: 100vh*/">
        <header class="modal-card-head">
            <p class="modal-card-title">Users</p>
            <button class="delete" aria-label="close" id="close-btn"></button>
        </header>
        <section class="modal-card-body px-5">
            <?php
            include '../components/update_profile.php';
            display_users();
            ?>
        </section>
        <footer class="modal-card-foot field is-grouped is-grouped-right is-paddingless">
            <div class="p-3">
                <button class="button">Cancel</button>
            </div>
        </footer>
    </div>
</div>


<!-- body content: recent logs, statistics, history -->
<div class="tile is-ancestor px-4 my-2 mb-3">
    <div class="tile is-4 is-vertical is-parent">
        <div class="tile is-child box">
            <p class="title is-3 mb-1">Recent Logs</p>
            <?php include_once "../components/recent_logs.php" ?>
        </div>
        <div class="tile is-child box">
            <p class="title">Statistics</p>
            <?php include_once "../components/statistics.php" ?>
        </div>
    </div>

    <div class=" tile is-vertical is-parent">
        <div class="tile is-child box">
            <div id="generateReport">

                <div class="flex-container is-flex is-justify-content-space-between">
                    <p class="title is-3 m-1">History</p>
                    <a @click="generate_report">
                        <div class="icon-text">
                    <span class="icon has-text-info">
                        <i class="fas fa-download"></i>
                    </span>
                            <span>Monthly Report</span>
                        </div>
                    </a>
                </div>
            </div>
            <?php include_once "../components/users_history.php" ?>
        </div>
    </div>
</div>

<footer class="footer stuck py-3">
    <div class="content has-text-centered">
        <p>⚠️ Unauthorized access is strictly prohibited. ⚠️</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/vue@3.2.20/dist/vue.global.prod.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script src="../static/index.js"></script>
<script src="../static/user.js"></script>
<script src="../static/monthly_report.js"></script>
<script src="../static/user_records.js"></script>
<script src="../static/statistics.js"></script>
<script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool'></script>

</body>
</html>
