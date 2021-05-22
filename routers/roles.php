<?php
include_once 'classes/Role.php';
include_once 'services/print.php';

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
            printjsonRole($mysqli->query("SELECT * FROM `role`"));
        }
        if (count($urlData) == 1) {
            printjsonRole($mysqli->query("SELECT * FROM `role` WHERE `id` = '$urlData[0]'"));
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
            $model = new Role();
            if ($model->setName($formData["Name"])) {
                $model->Name = $formData["Name"];
                $mysqli->query("INSERT INTO `role` (`id`, `name`) VALUES (NULL, '$model->Name')");
                header('HTTP/1.0 200 OK');
                echo json_encode(array(
                    'HTTP/1.0' => '200 OK'
                ));
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
        }
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        if ($per == 1) {
            $model = new Role();
            if ($model->setName($formData["Name"]) && $urlData[0] != 1 && $urlData[0] != 2 && $urlData[0] != 3) {
                $model->Name = $formData["Name"];
                $mysqli->query("UPDATE `role` SET `Name` = '$model->Name' WHERE `role`.`Id` = '$urlData[0]'");
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
    if (count($urlData) == 1) {
        if (!is_numeric($urlData[0])) {
            header('X-PHP-Response-Code: 404', true, 404);
            return;
        }
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        if ($per == 1) {
            if ($urlData[0] != 1 && $urlData[0] != 2 && $urlData[0] != 3) {
                $mysqli->query("DELETE FROM `role` WHERE `role`.`Id` = '$urlData[0]'");
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
        } else {
            header('HTTP/1.0 403 Forbidden');
            echo json_encode(array(
                'HTTP/1.0' => "403 Forbidden"
            ));
            return;
        }
    }
}