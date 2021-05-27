<?php
include_once 'classes/User.php';
include_once 'services/print.php';
include_once 'services/checkPermission.php';
include_once 'classes/Message.php';
include_once 'services/databaseConnection.php';


function route($method, $urlData, $formData)
{
    $mysqli = connectToDB();
    if ($method === 'GET') {
        if (count($urlData) == 2) {
            if ($urlData[0] == "photos" && is_numeric($urlData[1])) {
                getPhotos($mysqli, $urlData);
            }
            if ($urlData[1] == "posts" && is_numeric($urlData[0])) {
                getPosts($mysqli, $urlData);
            }
            if ($urlData[1] == "messages" && is_numeric($urlData[0])) {
                getMessages($formData, $urlData, $mysqli);
            }
        } else {
            get($urlData, $mysqli);
        }
    }
    if ($method == 'POST') {
        if (count($urlData) == 2) {
            if ($urlData[1] == "avatar" && is_numeric($urlData[0])) {
                postAvatar($urlData, $mysqli);
            }
            if ($urlData[1] == "messages" && is_numeric($urlData[0])) {
                postMessages($urlData, $mysqli, $formData);
            }
        } else {
            post($formData, $mysqli);
        }
    }
    if ($method === 'PATCH') {
        if (count($urlData) == 2) {
            if ($urlData[1] == "status") {
                patchStatus($mysqli, $formData, $urlData);
            }
            if ($urlData[1] == "city") {
                patchCity($formData, $urlData, $mysqli);
            }
            if ($urlData[1] == "role") {
                patchRole($formData, $urlData, $mysqli);
            }
        }
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
            printjsonUser($mysqli->query("SELECT `user`.* , `post`.`Text`, `post`.`Date` FROM `user` LEFT JOIN `post` ON `post`.`UserId` = `user`.`Id`"));
            die(0);
        }
        if (count($urlData) == 1) {
            printjsonUser($mysqli->query("SELECT `user`.* , `post`.`Text`, `post`.`Date` FROM `user` LEFT JOIN `post` ON `post`.`UserId` = `user`.`Id` WHERE `user`.`Id` = '$urlData[0]'"));
            die(0);
        }
    }
}

function getPhotos($mysqli, $urlData)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        printjsonPhotos($mysqli->query("SELECT * FROM `photo` WHERE `photo`.`UserId` = '$urlData[1]'")) or die(mysqli_error($mysqli));
    }
}

function getPosts($mysqli, $urlData)
{
    printjsonUserPosts($mysqli->query("SELECT * FROM `post` WHERE `post`.`UserId` = '$urlData[0]'"));
}

function getMessages($formData, $urlData, $mysqli)
{
    $limit = $formData['limit'];
    $offset = $formData['offset'];
    $res = $mysqli->query("SELECT * FROM `message` 
                           WHERE `message`.`UserAimId` = '$urlData[0]' OR `message`.`UserOwnerId` = '$urlData[0]' LIMIT '$limit' OFFSET '$offset'");
    printjsonMessages($res);
}

function post($formData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        if ($per == 0 || $per == 1) {
            $model = new User();
            $newUserName = $formData["Username"];
            $sameUsername = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Username` = '$newUserName'");
            if ($sameUsername->num_rows > 0) {
                print418('User with the same name already exists');
                die(0);
            }
            if ($model->setName($formData["Name"])
                && $model->setSurname($formData["Surname"])
                && $model->setBirthday($formData["Birthday"])
                && $model->setUsername($formData["Username"])
                && $model->setPassword($formData["Password"])) {
                $model->Name = $formData["Name"];
                $model->Surname = $formData["Surname"];
                $model->Password = strToHex($formData["Password"]);
                $model->Username = $formData["Username"];
                $model->Birthday = $formData["Birthday"];
                $mysqli->query("INSERT INTO `user` (`id`, `name`, `surname`, `password`, `birthday`, `avatar`, `status`, `username`, `roleId`) VALUES (NULL, '$model->Name', '$model->Surname', '$model->Password', '$model->Birthday', NULL, NULL, '$model->Username', '3')") or die(mysqli_error($mysqli));
                if ($per == 0) {
                    $token = gen_token();
                    $mysqli->query("UPDATE `user` SET `Token` = '$token' WHERE `user`.`Username` = '$model->Username'") or die(mysqli_error($mysqli));
                    $mysqli->query("UPDATE `user` SET `Status` = 'Online' WHERE `user`.`Username` = '$model->Username'") or die(mysqli_error($mysqli));
                    echo json_encode(array(
                        'Token' => $token
                    ));
                }
                header('HTTP/1.0 200 OK');
                die(0);
            } else {
                print204('There is no content to insert');
                die(0);
            }
        }
        print403('Forbidden operation');
        die(0);
    }
}

function postAvatar($urlData, $mysqli)
{

    $oldPhoto = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Id` = '$urlData[0]'");
    if ($oldPhoto->num_rows > 0) {
        while ($row = $oldPhoto->fetch_assoc()) {
            $photo = $row['avatar'];
        }
    }
    if (!empty($photo)) {
        unlink($photo);
    }

    if ($_FILES && $_FILES["filename"]["error"] == UPLOAD_ERR_OK ) {
        $name = htmlspecialchars(basename($_FILES["File"]["name"]));
        $path =  "uploads/" . time() . $name;
        if (move_uploaded_file($_FILES["File"]["tmp_name"], $path)) {
            $mysqli->query("UPDATE `user` SET `avatar` = '$path' WHERE `user`.`Id` = $urlData[0]");
            echo json_encode($mysqli->query("SELECT * FROM `user` WHERE `user`.`Id` = '$urlData[0]'"));
        } else {
            header('HTTP/1.0 409 Conflict');
            return;
        }
    }
}

function postMessages($urlData, $mysqli, $formData)
{
    if ($mysqli->connect_error) {
        echo 'Error №' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $messageAim = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Id` = '$urlData[0]'"))[0] or die(mysqli_error($mysqli));
        if ($messageAim) {
            if (isset($headers["Authorization"])) {
                $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
                $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0] or die(mysqli_error($mysqli));
                $model = new Message();
                if ($model->setText($formData["Text"])) {
                    $model->Text = $formData["Text"];
                    $date = date('m/d/Y h:i:s a', time());
                    $model->Text = mysqli_real_escape_string($mysqli, $model->Text);
                    $mysqli->query("INSERT INTO `message` (`id`, `userownerid`, `useraimid`, `text`, `date`) VALUES (NULL, '$UserId', '$urlData[0]','$model->Text', '$date')") or die(mysqli_error($mysqli));
                    echo json_encode(mysqli_fetch_array($mysqli->query("SELECT Id FROM `message` WHERE `message`.`UserAimId` = '$urlData[0]' AND `message`.`UserOwnerId` = '$UserId'"))[0] or die(mysqli_error($mysqli))) ;
                    return;
                } else {
                    header('HTTP/1.0 204 No Content');
                    return;
                }
            } else {
                header('HTTP/1.0 401 Unauthorized');
                return;
            }
        } else {
            header('HTTP/1.0 404 Not found');
            return;
        }
    }
}

function patch($formData, $urlData, $mysqli)
{
    $headers = getallheaders();
    $per = checkPermission($headers["Authorization"]);
    $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
    $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0] or die(mysqli_error($mysqli));
    if ($per == 1 || $urlData[0] == $UserId) {
        if (count($urlData) == 1) {
            if (!is_numeric($urlData[0])) {
                header('X-PHP-Response-Code: 404', true, 404);
                return;
            }
            $model = new User();
            if ($model->setName($formData["Name"])
                && $model->setSurname($formData["Surname"])
                && $model->setBirthday($formData["Birthday"])
                && $model->setUsername($formData["Username"])
                && $model->setPassword($formData["Password"])) {
                $model->Name = $formData["Name"];
                $model->Surname = $formData["Surname"];
                $model->Password = strToHex($formData["Password"]);
                $model->Username = $formData["Username"];
                $model->Birthday = $formData["Birthday"];
                $mysqli->query("UPDATE `user` SET `Name` = '$model->Name',`Surname` = '$model->Surname', `Birthday` = '$model->Birthday', `Username` = '$model->Username', `Password` = '$model->Password' WHERE `user`.`Id` = '$urlData[0]'") or die(mysqli_error($mysqli));
                header('HTTP/1.0 200 OK');
                return;
            } else {
                header('HTTP/1.0 204 No Content');
                return;
            }
        }
    } else {
        header('HTTP/1.0 403 Forbidden');
        return;
    }
}

function patchStatus($mysqli, $formData, $urlData)
{
    if ($mysqli->connect_error) {
        echo 'Error number:' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        $UserToken = str_replace("Bearer ", "", $headers["Authorization"]);
        $UserId = mysqli_fetch_array($mysqli->query("SELECT Id FROM `user` WHERE `user`.`Token` = '$UserToken'"))[0];
        if ($per == 1 || $urlData[0] == $UserId) {
            $status = $formData["Status"];
            if (validateStatusEnum($formData["Status"])) {
                $mysqli->query("UPDATE `user` SET `Status` = '$status' WHERE `user`.`Id` = '$urlData[0]'");
                header('HTTP/1.0 200 OK');
                return;
            } else {
                header('HTTP/1.0 406 Not acceptable');
                return;
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
            return;
        }
    }
}

function patchCity($formData, $urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error number:' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $cityId = $formData["CityID"];
        $userCity = $mysqli->query("SELECT * FROM `city` WHERE `city`.`Id` = $cityId") or die(mysqli_error($mysqli));
        if ($userCity->num_rows > 0) {
            $mysqli->query("UPDATE `user` SET `CityId` = '$cityId' WHERE `user`.`Id`='$urlData[0]'") or die(mysqli_error($mysqli));
            header('HTTP/1.0 200 OK');
        } else {
            header('HTTP/1.0 404 Not found');
        }
    }
}

function patchRole($formData, $urlData, $mysqli)
{
    if ($mysqli->connect_error) {
        echo 'Error number:' . $mysqli->connect_errno . '<br>';
        echo $mysqli->connect_error;
    } else {
        $headers = getallheaders();
        $per = checkPermission($headers["Authorization"]);
        if ($per == 1) {
            $roleId = $formData["RoleID"];
            $userRole = $mysqli->query("SELECT * FROM `role` WHERE `role`.`Id` = $roleId") or die(mysqli_error($mysqli));
            if ($userRole->num_rows > 0) {
                $mysqli->query("UPDATE `user` SET `RoleId` = '$roleId' WHERE `user`.`Id`='$urlData[0]'") or die(mysqli_error($mysqli));
                header('HTTP/1.0 200 OK');
                return;
            } else {
                header('HTTP/1.0 404 Not found');
                return;
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
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
        if ($per == 1) {
            if (count($urlData) == 1) {
                if (!is_numeric($urlData[0])) {
                    header('X-PHP-Response-Code: 404', true, 404);
                    return;
                }
                $mysqli->query("DELETE FROM `user` WHERE `user`.`Id` = '$urlData[0]'") or die(mysqli_error($mysqli));
                header('HTTP/1.0 200 OK');
                return;
            }
        } else {
            header('HTTP/1.0 403 Forbidden');
            return;
        }
    }
}

