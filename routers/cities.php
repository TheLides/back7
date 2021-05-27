<?php
include_once 'classes/City.php';
include_once 'services/print.php';
include_once 'services/checkPermission.php';
include_once 'services/databaseConnection.php';


function route($method, $urlData, $formData)
{
    $mysqli = connectToDB();
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
            if (is_numeric($urlData[0])) {
                printjsonCity($mysqli->query("SELECT * FROM `city` WHERE `id` = '$urlData[0]'"));
            } else {
                print400('Id is not numeric');
            }
        }
        if (count($urlData) == 2) {
            if (is_numeric($urlData[0]) && $urlData[1] == "peoples") {
                printjsonCityPeople($mysqli->query("SELECT * FROM `user` WHERE `user`.`CityId` = '$urlData[0]'"));
            } else {
                print400('Id is not numeric or there is no "peoples" ');
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
                return;
            } else {
                print204('No content to insert');
                die(0);
            }
        } else {
            print403('Not enough rights');
            die(0);
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
                    $mysqli->query("UPDATE `city` SET `Name` = '$model->Name' WHERE `city`.`Id` = '$urlData[0]'") or die(mysqli_error($mysqli));
                    header('HTTP/1.0 200 OK');
                    return;
                } else {
                    print204('No content to update');
                    die(0);
                }
            } else {
                print403('Not enough rights');
                die(0);
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
                $mysqli->query("DELETE FROM `city` WHERE `city`.`Id` = '$urlData[0]'") or die(mysqli_error($mysqli));
                header('HTTP/1.0 200 OK');
                return;
            } else {
                print403('Not enough rights');
                die(0);
            }
        }
    }
}
