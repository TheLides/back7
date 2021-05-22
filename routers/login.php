<?php
include_once 'services/print.php';

function gen_token() {
    $bytes = openssl_random_pseudo_bytes(15, $cstrong);
    return bin2hex($bytes);
}

function strToHex($string)
{
    $hex = '';
    for ($i = 0; $i < strlen($string); $i++) {
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0' . $hexCode, -2);
    }
    return strToUpper($hex);
}

function route($method, $urlData, $formData)
{
    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if ($method == 'POST') {
        if ($mysqli->connect_error) {
            echo 'Error №' . $mysqli->connect_errno . '<br>';
            echo $mysqli->connect_error;
        } else {
            $headers = getallheaders();
            if (isset($headers["Authorization"])){
                header('HTTP/1.0 409 Conflict');
                echo json_encode(array(
                    'HTTP/1.0' => "409 Conflict"
                ));
                return;
            }
            $username = $formData["Username"];
            $password = strToHex($formData["Password"]);
            $userExist = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Username` = '$username'");
            if($userExist->num_rows == 0){
                header('HTTP/1.0 404 Not found');
                echo json_encode(array(
                    'HTTP/1.0' => "404 Not found"
                ));
                return;
            } else {
                $pwd = mysqli_fetch_array($mysqli->query("SELECT Password FROM `user` WHERE `user`.`Username` = '$username'"))[0];
                if ($pwd != $password){
                    header('HTTP/1.0 403 Forbidden');
                    echo json_encode(array(
                        'HTTP/1.0' => "403 Forbidden"
                    ));
                    return;
                } else {
                    $token = gen_token();
                    $mysqli->query("UPDATE `user` SET `Token` = '$token' WHERE `user`.`Username` = '$username'");
                    $mysqli->query("UPDATE `user` SET `Status` = 'Online' WHERE `user`.`Username` = '$username'");
                    header('HTTP/1.0 200 OK');
                    echo json_encode(array(
                        'HTTP/1.0' => "200 OK"
                    ));
                    echo $token;
                }
            }

        }
    }

    $mysqli->close();


}