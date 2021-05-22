<?php

function checkPermission($token) : Int
{
    $mysqli = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if (isset($token)) {
        $UserToken = str_replace("Bearer ", "", $token);
        $loginUser = $mysqli->query("SELECT * FROM `user` WHERE `user`.`Token` = `$UserToken`");
        if (isset($loginUser)) {
            $roleId = mysqli_fetch_array($mysqli->query("SELECT RoleId FROM `user` WHERE `user`.`token` = '$UserToken'"))[0];
            if (isset($roleId)) {
                return $roleId;
            }
        }
        return 0;
    }
    return 0;
}
