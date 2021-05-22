<?php
include_once 'services/print.php';

function route($method, $urlData, $formData)
{

    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method === 'GET') {
        get($urlData, $mysqli);
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
        $headers = getallheaders();
        if (isset($headers["Authorization"])){
            $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
            $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
            if (count($urlData) == 0) {
                printjsonMessages($mysqli->query("SELECT * FROM `message` WHERE `message`.`UserOwnerId` = '$UserId' OR `message`.`UserAimId` = '$UserId'"));
            }
            if (count($urlData) == 1) {
                $mesOwnerId = mysqli_fetch_array($mysqli->query("SELECT UserOwnerId FROM `message` WHERE `message`.`Id` = '$urlData[0]'"))[0];
                $mesAimId = mysqli_fetch_array($mysqli->query("SELECT UserAimId FROM `message` WHERE `message`.`Id` = '$urlData[0]'"))[0];
                if ($mesAimId == $UserId || $mesOwnerId == $UserId){
                    printjsonMessages($mysqli->query("SELECT * FROM `message` WHERE `message`.`Id` = '$urlData[0]'"));
                }
            }
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array(
                'HTTP/1.0' => "401 Unauthorized"
            ));
            return;
        }
    }
}

function delete($urlData, $mysqli){
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        if (isset($headers["Authorization"])) {
            $per = checkPermission($headers["Authorization"]);
            $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
            $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
            $mesOwnerId = mysqli_fetch_array($mysqli->query("SELECT UserOwnerId FROM `message` WHERE `message`.`Id` = '$urlData[0]'"))[0];
            $mesAimId = mysqli_fetch_array($mysqli->query("SELECT UserAimId FROM `message` WHERE `message`.`Id` = '$urlData[0]'"))[0];
            if ($per == 1 || $UserId == $mesOwnerId || $UserId == $mesAimId) {
                if (count($urlData) == 1) {
                    if (!is_numeric($urlData[0])) {
                        header('X-PHP-Response-Code: 404', true, 404);
                        return;
                    }
                    $mysqli->query("DELETE FROM `message` WHERE `message`.`Id` = '$urlData[0]'");
                    header('HTTP/1.0 200 OK');
                    echo json_encode(array(
                        'HTTP/1.0' => "200 OK"
                    ));
                }
            } else {
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'HTTP/1.0' => "403 Forbidden"
                ));
                return;
            }
        } else {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(array(
                'HTTP/1.0' => "401 Unauthorized"
            ));
            return;
        }
    }
}