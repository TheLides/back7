<?php
include_once 'services/databaseConnection.php';

function checkPermission($token) : Int
{
    $mysqli = connectToDB();
    if (isset($token)) {
        $UserToken = str_replace("Bearer ", "", $token);
        $loginUser = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Token` = '$UserToken'");
        if (isset($loginUser)) {
            $roleId = mysqli_fetch_array($mysqli->query("SELECT RoleId FROM `user` WHERE `user`.`token` = '$UserToken'"))[0] or die(mysqli_error($mysqli));
            if (isset($roleId)) {
                return $roleId;
            }
        }
        return 0;
    }
    return 0;
}

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
