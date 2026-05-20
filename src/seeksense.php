<?php require_once "connection.php";

global $conn;

if (!isset($_SESSION['current_user'])) {
    header("Location: index.php");
    die();
}

if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_type = $_FILES['file']['type'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];

    // Check file extension and type
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");
    $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    if(!in_array(strtolower($file_extension), $allowed_extensions)) {
        exit("Invalid file format. Only JPG, JPEG, PNG and GIF files are allowed.");
    }

    // Read file contents and convert to base64
    $file_data = file_get_contents($file_tmp);
    $base64_image = base64_encode($file_data);

    // Create base64 encoded URL
    $base64_url = 'data:' . $file_type . ';base64,' . $base64_image;
    // Prepare the file data for the API request
    $cfile = new CURLFile($file, $file_type, $file_name);

    // API endpoint URL
    $url = 'http://seeksense_embeddings/v1/face/collection/nearest';

    // Create a new cURL resource
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => $cfile));

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        $err = 'cURL error: ' . curl_error($ch);
        // Close the cURL resource
        curl_close($ch);
        die($err);
    } else {
        // Decode the JSON response
        $json_response = json_decode($response, true);

        // Save the response JSON payload to the MySQL database
        $stmt = $conn->prepare("INSERT INTO searches_table (user_id, query, query_filename, payload) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_SESSION['current_user'], $base64_url, $file_name, $response);
        $stmt->execute();
        $stmt->close();

        $sth = $conn->prepare("INSERT INTO history_table (user_id, payload) VALUES (?, ?)");
        $sth->bind_param("is", $_SESSION['current_user'], $response);
        $sth->execute();
        $sth->close();

        // Close the cURL resource
        curl_close($ch);
        die($response);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SeekSense</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bulma-tooltip@3.0.2/dist/css/bulma-tooltip.min.css" rel="stylesheet">
    <link rel="web icon" type="png" href="static/logo/seeksense-logo.png">
    <link rel="stylesheet" href="static/styles.css">
</head>
<body>
<div id="searchHistory">
    <nav class="navbar has-background-light pt-2" role="navigation" aria-label="main navigation" >
        <div id="mySidenav" class="sidenav">
            <section class="columns">
                <div class="column"><a href="" class="new-search has-tooltip-right" data-tooltip="new search" ><i class="fa fa-search"></i></a></div>
                <div class="column"><a href="javascript:void(0)" class="close-btn has-tooltip-left" data-tooltip="close sidebar" onclick="closeNav()">&times;</a></div>
            </section>

            <ul v-if="items.length > 0">
                <li v-for="item in items" :key="item.id">
                    <a href="javascript:void(0)" @click="showContent(item)" class="subtitle is-6">
                        {{ item.query_filename }} </a>
                </li>
            </ul>
            <div v-else>
                No items found.
            </div>
        </div>
        <span style="font-size:30px; cursor:pointer" class="mx-5 is-hidden-touch" onclick="openNav()">&#9776; </span>

        <h2 id="main-header" class="seeksense is-size-3 has-text-weight-bold">SeekSense</h2>

        <div class="navbar-end is-hidden-touch">
            <div class="navbar-item">

                <?php if ($_SESSION['current_user'] > 999) { ?>
                    <a href="admin/index.php" class="button is-light"
                       style="border-right: 1px solid #ccc;">Admin</a>
                <?php } ?>
                <a href="logout.php" class="button is-light">Log Out</a>
            </div>
        </div>

    </nav>

    <div id="main">
        <div class="columns is-variable is-3">
            <div class="column is-two-fifths ">
                <div class="box" style="min-height: 87vh;">
                    <div class="container">
                        <h2 class="title is-5">Upload a front-facing image:</h2>
                        <div class="columns is-centered full-height is-flex is-justify-content-center is-align-items-center">
                            <div id="droppr" class="file has-name is-boxed">
                                <label class="file-label">
                                    <input id="img-input" class="file-input has-text-centered" type="file"/>
                                    <span class="file-cta">
                                              <span class="file-icon"><i class="fas fa-upload"></i></span>
                                              <span class="file-label"> Choose a file… </span>
                                            </span>
                                    <span v-if="selectedItem" class="file-name">{{ selectedItem.query_filename }}</span>
                                    <span v-else class="file-name" id="file-name"></span>
                                </label>
                            </div>

                        </div>

                        <div class="notification has-text-centered " style="border: dashed 2px #ccc">
                            <figure class="image full-height is-flex is-justify-content-center is-align-items-center">
                                <div v-if="selectedItem"><img class="preview" :src="selectedItem.query"
                                                              alt="Image Preview" style="border-radius: 5px"/></div>
                                <div v-else><p>Image Preview</p></div>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column">
                <div class="box" style="height: 87vh; overflow: auto;">
                    <div id='results' class="columns is-multiline is-desktop"></div>
                    <div v-if="result_metadatas.length > 0" class="columns is-multiline is-desktop">
                        <div class="column is-full" v-if="selectedItem"
                             v-for="(metadata, index) in result_metadatas"
                             :key="index">
                            <article class="card media px-3 pt-3">
                                <figure class="media-left">
                                    <img class="image match" :src="result_images[index]" alt="Search Image">
                                </figure>
                                <div class="columns media-content">
                                    <div class="column content">
                                        <span class="title is-6">URL: &nbsp;</span>
                                        {{ metadata.post_url || 'Unavailable' }} <br/>
                                        <span class="title is-6">Post Author: &nbsp;</span>{{ metadata.post_author || 'Unavailable'
                                        }} <br/>
                                        <span class="title is-6">Post Time: &nbsp;</span>{{ metadata.post_time || 'Unavailable'
                                        }} <br/>
                                        <span class="title is-6">Post Location: &nbsp;</span>{{ metadata.post_location || 'Unavailable'
                                        }}
                                    </div>

                                    <div class="column is-4"
                                         style="display: flex; align-items: center; justify-content: center;">

                                        <span class="title is-4 has-text-centered has-text-grey">{{ Math.ceil(((1 - distances[index]) * 100) * 100) / 100 }}%</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    </div>
                    <p v-else>{{ error_message }}</p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/jszip/dist/jszip.min.js"></script>
<!--    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool'></script>-->
    <script src="static/uploader.js"></script>
<!--<<<<<<< HEAD-->
    <script>
        async function handleFile(filename, fileb64) {
            let preview_img = document.getElementById('preview');
            preview_img.src = fileb64;

            let query_filename = document.getElementById("file-name");
            query_filename.textContent = filename;

            console.log(filename, fileb64);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/api/v1/face/collection/nearest', true);
            // xhr.setRequestHeader('Content-Type', 'multipart/form-data');
            const formData = new FormData();
            formData.append('file', await url2file(filename, fileb64), filename);
            xhr.send(formData);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        const resultsDiv = document.getElementById("results");
                        resultsDiv.innerHTML = '';
                        // todo: file upload success message
                        const response = JSON.parse(xhr.responseText /* palitan mo to ng value ni searches_table.payload */);
                        console.dir(response);
                        for (let i = 0; i < response.ids.length; i++) {
                            // todo: make this into a vue thing- not sure how
                            let distance = response.distances[i];
                            let percentage = (1 - distance) * 100;

                            let post_url = response.metadatas[i].post_url || "Unavailable";
                            let post_author = response.metadatas[i].post_author || "Unavailable";
                            let post_time = response.metadatas[i].post_time || "Unavailable";
                            let post_location = response.metadatas[i].post_location || "Unavailable";
                            resultsDiv.innerHTML += `<div class="column is-full">
                                <article class="card media px-3 pt-3">
                                    <figure class="media-left">
                                        <img class="image match" src="${response.documents[i]}" alt="Matched Image">
                                    </figure>
                                    <div class="media-content columns">
                                        <div class="column content">
                                            <span class="title is-6">URL: &nbsp;</span>${post_url} <br/>
                                            <span class="title is-6">Post Author: &nbsp;</span>${post_author} <br/>
                                            <span class="title is-6">Post Time: &nbsp;</span>${post_time} <br/>
                                            <span class="title is-6">Post Location: &nbsp;</span>${post_location}
                                        </div>
                                        <div class="column is-3" style="display: flex; align-items: center; justify-content: center;">
                                            <span class="title is-4 has-text-centered has-text-grey">${Math.ceil(percentage * 100) / 100}%</span>
                                        </div>
                                    </div>
                                </article>
                            </div>`;
                        }
                        addUserSearch(response, fileb64, filename);
                    } else {
                        console.error('Error uploading file:', xhr.statusText);
                    }
                }
            };
        }

        initDropZone(document.getElementById('droppr'), handleFile);

        function addUserSearch(response, fileb64, filename) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '../add_search.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log('Search data added successfully.');
                    } else {
                        console.error('Error adding search data: ', xhr.statusText);
                    }
                }
            };
            const data = {
                results: response,
                file: fileb64,
                filename: filename
            };

            const jsonData = JSON.stringify(data);
            xhr.send(jsonData);
        }
    </script>
    <script src="static/search_item.js"></script>

    <script>
        function openNav() {
            document.getElementById("mySidenav").style.width = "250px";
            document.getElementById("main").style.marginLeft = "250px";
            document.body.style.backgroundColor = "rgba(0,0,0,0.4)";
            document.getElementById('main-header').style.transition = "margin-left .5s";
            document.getElementById('main-header').style.marginLeft = "200px";
        }

        function closeNav() {
            document.getElementById("mySidenav").style.width = "0";
            document.getElementById("main").style.marginLeft = "0";
            document.body.style.backgroundColor = "white";
            document.getElementById('main-header').style.transition = "margin-left .5s";
            document.getElementById('main-header').style.marginLeft = "0";
        }
    </script>
</body>
</html>

