<?php
include_once 'services/print.php';
include_once 'services/checkPermission.php';

function route($method, $urlData, $formData)
{
    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method === 'GET') {
        get($mysqli, $urlData);
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

function get($mysqli, $urlData)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        if (count($urlData) == 0) {
            printjsonPosts($mysqli->query("SELECT * FROM `post`"));
        }
        if (count($urlData) == 1) {
            printjsonPosts($mysqli->query("SELECT * FROM `post` WHERE `id` = '$urlData[0]'"));
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
        if (isset($headers["Authorization"])) {
            $per = checkPermission($headers["Authorization"]);
            if ($per != 0) {
                $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
                $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
                $model = new Post();
                if ($model->setText($formData["Text"])) {
                    $model->Text = $formData["Text"];
                    $date = date('m/d/Y h:i:s a', time());
                    $model->Text = mysqli_real_escape_string($mysqli, $model->Text);
                    $mysqli->query("INSERT INTO `post` (`userid`, `text`, `date`) VALUES ($UserId,'$model->Text', '$date')") or die(mysqli_error($mysqli));
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
                header('HTTP/1.0 401 Unauthorized');
                echo json_encode(array(
                    'HTTP/1.0' => "401 Unauthorized"
                ));
                return;
            }
        } else {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array(
                'HTTP/1.0' => "401 Bad Request"
            ));
            return;
        }
    }

}

function patch($formData, $urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        if (isset($headers["Authorization"])) {
            $per = checkPermission($headers["Authorization"]);
            $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
            $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
            $postOwnerId = mysqli_fetch_array($mysqli->query("SELECT UserId FROM `post` WHERE `post`.`Id` = '$urlData[0]'"));
            if ($per == 1 || $per == 2 || $UserId == $postOwnerId) {
                if (count($urlData) == 1) {
                    if (!is_numeric($urlData[0])) {
                        header('X-PHP-Response-Code: 404', true, 404);
                        return;
                    }
                    $model = new Post();
                    if ($model->setText($formData["Text"])) {
                        $model->Text = $formData["Text"];
                        $model->Text = mysqli_real_escape_string($mysqli, $model->Text);
                        $mysqli->query("UPDATE `post` SET `Text` = '$model->Text' WHERE `post`.`Id` = '$urlData[0]'") or die(mysqli_error($mysqli));
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

function delete($urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        if (isset($headers["Authorization"])) {
            $per = checkPermission($headers["Authorization"]);
            $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
            $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
            $postOwnerId = mysqli_fetch_array($mysqli->query("SELECT UserId FROM `post` WHERE `post`.`Id` = '$urlData[0]'"));
            if ($per == 1 || $per == 2 || $UserId == $postOwnerId) {
                if (count($urlData) == 1) {
                    if (!is_numeric($urlData[0])) {
                        header('X-PHP-Response-Code: 404', true, 404);
                        return;
                    }
                    $mysqli->query("DELETE FROM `post` WHERE `post`.`Id` = '$urlData[0]'");
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