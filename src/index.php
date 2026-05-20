<?php require_once "connection.php";
include "functions.php";

global $conn;
$result = $conn->query("SELECT COUNT(*) as num_rows FROM users_table");
if ($result && $row = $result->fetch_assoc()) {
    if ($row['num_rows'] == 0) {
        header("Location: setup.php");
        die("redirecting");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit'])) {
        $USERNAME = $_POST['username'];
        $PASSWORD = $_POST['password'];
        $PASSWORD = hash_pwd($PASSWORD);

        $sth = $conn->prepare("SELECT * FROM `users_table` WHERE USERNAME = ? AND PASSWORD = ?");

        if ($sth) {
            $sth->bind_param('ss', $USERNAME, $PASSWORD);
            $sth->execute();
            $result = $sth->get_result();

            if ($result && ($result->num_rows > 0 && $row = $result->fetch_assoc())) {
                $_SESSION['current_user'] = $row['USER_ID'];
                $stmt = $conn->prepare("INSERT INTO logs_table (user_id, ip) VALUES (?, ?)");
                if ($stmt) {
                    $current_ip = current_ip();
                    $stmt->bind_param("is", $_SESSION['current_user'], $current_ip);
                    $stmt->execute();
                    $stmt->close();

                    header("refresh:1;url=seeksense.php");
                    exit();
                } else {
                    echo "Error: " . $conn->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initiacd l-scale=1.0">
    <title>SeekSense</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="static/styles.css">
    <link rel="web icon" type="png" href="static/logo/seeksense-logo.png">
</head>
<body>

<div class="column notification is-light is-fullhd">
    <div class="columns mx-3">

        <div class="column has-text-left">
            <figure class="image is-48x48 is-pulled-left is-hidden-touch"><img src="static/logo/seeksense-logo.png">
            </figure>
        </div>
        <div class="column is-three-fifths has-text-centered is-size-3 has-text-weight-bold">SeekSense</div>
        <div class="column has-text-right mx-3">
            <figure class="image is-32x32 is-pulled-right is-hidden-touch"><img src="static/logo/pnp.png"></figure>
        </div>
    </div>
</div>

<section class="section is-medium mb-4">
    <div class="columns is-centered has-text-centered mx-2">
        <div class="column is-two-fifths box py-4 px-5">
            <?php if (isset($_POST['submit']) && !isset($_SESSION['current_user'])) { ?>
                <div class="has-background-danger-light has-text-weight-bold p-1 mb-2">
                    <p class='help is-danger'> incorrect username/password.</p>
                </div>
            <?php } ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data"
                  autocomplete="ON">
                <div class="field py-1">
                    <div class="control has-icons-left ">
                        <input type="text" name="username" class="input" placeholder="janedoe123">
                        <label class="label">username</label>
                        <span class="icon is-small is-left"><i class="fas fa-user"></i></span>
                    </div>
                </div>

                <div class="field py-1">
                    <p class="control has-icons-left has-icons-right ">
                        <input type="password" id="password" name="password" class="input" placeholder="Password">
                        <span class="icon is-small is-left">
                                <i class="fa fa-lock"></i>
                            </span>
                        <span id="btn_toggle" class="icon is-small is-right"
                              style="pointer-events: all; cursor: pointer">
                                <i class="fa fa-fw fa-eye-slash field_icon toggle-password" toggle="#password"></i>
                            </span>
                        <label class="label">password</label>
                    </p>
                </div>

                <div class="field py-1">
                    <input type="submit" name="submit" value="LOG IN"
                           class="button is-rounded is-focused has-text-weight-bold">
                </div>
            </form>

        </div>
    </div>
</section>

<footer class="footer py-5" style="position: absolute; left: 0; bottom: 0; width: 100%">
    <div class="content has-text-centered">
        <p>⚠️ Unauthorized access is strictly prohibited. ⚠️</p>
    </div>
</footer>

<script>
    $("body").on('click', '.toggle-password', function () {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $("#password");
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
</script>
<script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool'></script>

</body>
</html>
