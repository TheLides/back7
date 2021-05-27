<?php
include_once 'services/print.php';
include_once 'services/checkPermission.php';
include_once 'services/databaseConnection.php';


function route($method, $urlData, $formData)
{
    $mysqli = connectToDB();
    if ($method == 'POST') {
        if ($mysqli->connect_error) {
            echo 'Error №' . $mysqli->connect_errno . '<br>';
            echo $mysqli->connect_error;
        } else {
            $headers = getallheaders();
            if (isset($headers["Authorization"])){
                print409('System already get token');
                return;
            }
            $username = $formData["Username"];
            $password = strToHex($formData["Password"]);
            $userExist = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Username` = '$username'");
            if($userExist->num_rows == 0){
                header('HTTP/1.0 404 Not found');
                die(0);
            } else {
                $pwd = mysqli_fetch_array($mysqli->query("SELECT Password FROM `user` WHERE `user`.`Username` = '$username'"))[0];
                if ($pwd != $password){
                    print403('Wrong password');
                    return;
                } else {
                    $token = gen_token();
                    $mysqli->query("UPDATE `user` SET `Token` = '$token' WHERE `user`.`Username` = '$username'");
                    $mysqli->query("UPDATE `user` SET `Status` = 'Online' WHERE `user`.`Username` = '$username'");
                    header('HTTP/1.0 200 OK');
                    echo json_encode(array(
                        'Token' => $token
                    ));
                }
            }

        }
    }

    $mysqli->close();


}