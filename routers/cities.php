<?php
include_once 'classes/City.php';
include_once 'services/print.php';
include_once 'services/checkPermission.php';


function route($method, $urlData, $formData)
{

    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method === 'GET') {
        get($urlData, $mysqli);
    }
    if ($method == 'POST') {
        post($formData, $mysqli);
    }
    if ($method === 'PATCH') {
        patch($formData, $urlData, $mysqli);
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
            printjsonCity($mysqli->query("SELECT * FROM `city`"));
        }
        if (count($urlData) == 1) {
            printjsonCity($mysqli->query("SELECT * FROM `city` WHERE `id` = '$urlData[0]'"));
        }
        if (count($urlData) == 2) {
            if (is_numeric($urlData[0]) && $urlData[1] == "peoples") {
                printjsonCityPeople($mysqli->query("SELECT * FROM `user` WHERE `user`.`CityId` = '$urlData[0]'"));
            }
        }
    }
}

function post($formData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        if ($per == 1) {
            $model = new City();
            if ($model->setName($formData["Name"])) {
                $model->Name = $formData["Name"];
                $mysqli->query("INSERT INTO `city` (`id`, `name`) VALUES (NULL, '$model->Name')");
                header('HTTP/1.0 200 OK');
                echo json_encode(array(
                    'HTTP/1.0' => '200 OK'
                ));
                return;
            } else {
                header('HTTP/1.0 204 No Content');
                echo json_encode(array(
                    'HTTP/1.0' => "204 No Content"
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

function patch($formData, $urlData, $mysqli)
{
    if (count($urlData) == 1) {
        if (!is_numeric($urlData[0])) {
            header('X-PHP-Response-Code: 404', true, 404);
            return;
        } else {
            $headers = getallheaders();
            $per = checkPermission($headers["Authorization"]);
            if ($per == 1) {
                $model = new City();
                if ($model->setName($formData["Name"])) {
                    $model->Name = $formData["Name"];
                    $mysqli->query("UPDATE `city` SET `Name` = '$model->Name' WHERE `city`.`Id` = '$urlData[0]'");
                    header('HTTP/1.0 200 OK');
                    echo json_encode(array(
                        'HTTP/1.0' => "200 OK"
                    ));
                    return;
                } else {
                    header('HTTP/1.0 204 No Content');
                    echo json_encode(array(
                        'HTTP/1.0' => "204 No Content"
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
}

function delete($urlData, $mysqli)
{
    if (count($urlData) == 1) {
        if (!is_numeric($urlData[0])) {
            header('X-PHP-Response-Code: 404', true, 404);
            return;
        } else {
            $headers = getallheaders();
            $per = checkPermission($headers["Authorization"]);
            if ($per == 1) {
                $mysqli->query("DELETE FROM `city` WHERE `city`.`Id` = '$urlData[0]'");
                header('HTTP/1.0 200 OK');
                echo json_encode(array(
                    'HTTP/1.0' => "200 OK"
                ));
                return;
            } else {
                header('HTTP/1.0 403 Forbidden');
                echo json_encode(array(
                    'HTTP/1.0' => "403 Forbidden"
                ));
                return;
            }
        }
    }
}
