<?php
include_once 'classes/Photo.php';
include_once 'services/checkPermission.php';
include_once 'services/print.php';


function route($method, $urlData, $formData)
{

    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method === 'GET') {
        get($urlData, $mysqli);
    }
    if ($method == 'POST') {
        post($mysqli, $urlData);
    }
    if ($method == 'DELETE') {
        delete($urlData, $mysqli);
    }

    $mysqli->close();
}

function get($urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        if (count($urlData) == 0) {
            $headers = getallheaders();
            $perm = checkPermission($headers["Authorization"]);
            if ($perm != 0) {
                $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
                $userId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
                printjsonPhotos($mysqli->query("SELECT * FROM `photo` WHERE `photo`.`UserId` = '$userId'"));
            } else {
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'HTTP/1.0' => "403 Forbidden"
                ));
            }
        }
    }
}

function post($mysqli, $urlData)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
        $perm = checkPermission($headers["Authorization"]);
        if ($perm != 0) {
            $user = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`token` = '$UserToken'"))[0];

            if ($_FILES && $_FILES["filename"]["error"] == UPLOAD_ERR_OK ) {
                $name = htmlspecialchars(basename($_FILES["File"]["name"]));
                $path =  "uploads/" . time() . $name;
                if (move_uploaded_file($_FILES["File"]["tmp_name"], $path)) {
                    $mysqli->query("INSERT INTO `photo` (`id`, `userid`, `link`) VALUES (NULL, '$user', '$path')");
                    header('HTTP/1.0 200 OK');
                    echo json_encode(array(
                        'HTTP/1.0' => "200 OK"
                    ));
                    echo json_encode($mysqli->query("SELECT * FROM `photo` WHERE `photo`.`UserId` = '$user' AND `photo`.`Link` = '$path'"));
                    return;
                } else {
                    header('HTTP/1.0 409 Conflict');
                    echo json_encode(array(
                        'HTTP/1.0' => "409 Conflict"
                    ));
                    return;
                }
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'HTTP/1.0' => "403 Forbidden"
            ));
            return;
        }
    }
}

function delete($urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error number:' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
        $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
        if ($per == 1 || $UserId == $urlData[0]) {
            if (count($urlData) == 1) {
                if (!is_numeric($urlData[0])) {
                    header('X-PHP-Response-Code: 404', true, 404);
                    return;
                }
                $mysqli->query("DELETE FROM `photo` WHERE `photo`.`Id` = '$urlData[0]'");
                header('HTTP/1.0 200 OK');
                echo json_encode(array(
                    'HTTP/1.0' => "200 OK"
                ));
                return;
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'HTTP/1.0' => "403 Forbidden"
            ));
            return;
        }
    }
}
