<?php
require_once "../connection.php";
require_once "../functions.php";

if(!isset($_SESSION['current_user']) || $_SESSION['current_user'] <= 999) {
    die("<script>alert('⚠️ Unauthorized access is strictly prohibited. ⚠️')</script>");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeekSense</title>
    <link rel="stylesheet" href="../static/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>
    <nav class="navbar  has-background-light px-3 py-2" role="navigation" aria-label="main navigation">
        <h2 class="is-size-3  has-text-weight-bold">SeekSense</h2>
        <div class="navbar-end">
            <div class="navbar-item">
                <a href="../seeksense.php" class="button is-light" style="border-right: 1px solid #ccc;">Home</a>
                <div class="navbar-item has-dropdown is-hoverable" style="border-right: 1px solid #ccc;">
                    <a class="navbar-link">Manage User</a>
                    <div class="navbar-dropdown">
                        <a class="navbar-item js-modal-trigger" data-target="modal-js-example">Create User Form</a>
                        <a class="navbar-item js-modal-trigger" data-target="user-list">View All Users</a>
                    </div>
                </div>
                <a href="../logout.php" class="button is-light">Log out</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <section class="hero is-medium">
            <div class="hero-body">
                <div class="box">
                    <p class="title">Upload Images</p>
                    <p class="subtitle">Drag and drop images to store in database</p>

                    <div id="droppr" class="notification has-text-centered" style="padding: 13rem; border: dashed 0.125rem #777; user-select: none;">
                        <p class="title">Drop image(s) / archive here</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer stuck py-4">
        <div class="content has-text-centered">
            <p>⚠️ Unauthorized access is strictly prohibited. ⚠️</p>
        </div>
    </footer>

    <script src="../static/index.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="../static/user.js"></script>
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
    <script src="../static/uploader.js"></script>

    <script>
        async function handleFile(filename, fileb64) {
            return new Promise(async (resolve, reject) => {
                console.log(filename, fileb64);
                const metadata = {
                    filename
                };
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/api/v1/face/collection?metadata=' + encodeURIComponent(JSON.stringify(metadata)), true);
                // xhr.setRequestHeader('Content-Type', 'multipart/form-data');
                const formData = new FormData();
                formData.append('file', await url2file(filename, fileb64), filename);
                xhr.send(formData);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // todo: file upload success message
                            resolve(xhr.responseText);
                        } else {
                            reject(xhr.statusText);
                        }
                    }
                };
            });
        }

        initDropZone(document.getElementById('droppr'), handleFile)
    </script>
</body>
</html>