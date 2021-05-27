<?php

function route($method, $urlData, $formData)
{
    $headers = getallheaders();
    $token = $headers["Authorization"];

    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method == 'POST') {
        if ($mysqli->connect_error) {
            echo 'Error №' . $mysqli->connect_errno . '<br>';
            echo $mysqli->connect_error;
        } else {
            $UserToken = str_replace("Bearer ", "", $token);
            $mysqli->query("UPDATE `user` SET `Status` = 'Offline' WHERE `user`.`Token` = '$UserToken'") or die(mysqli_error($mysqli));
            $mysqli->query("UPDATE `user` SET `Token` = NULL WHERE `user`.`Token` = '$UserToken'") or die(mysqli_error($mysqli));
            header('HTTP/1.0 200 OK');
        }
    }

    $mysqli->close();
}