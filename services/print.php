<?php
include_once "classes/City.php";
include_once "classes/Role.php";
include_once "classes/User.php";
include_once "classes/Post.php";
include_once "classes/Photo.php";
include_once "classes/Message.php";
include_once "services/checkPermission.php";

function printjsonUser($res)
{
    $headers = getallheaders();
    $per = checkPermission($headers["Authorization"]);
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            if ($per == 1){
                array_push($resArray, new UserForAdminViewModel($row['Id'], $row['Name'], $row['Surname'], $row['Status'], $row['Birthday'], $row['Role']));
            } else {
                array_push($resArray, new UserViewModel($row['Id'], $row['Name'], $row['Surname'], $row['Status']));
            }
        }
    }
    echo json_encode($resArray);
}

function printjsonCity($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new CityViewModel($row['Id'], $row['Name']));
        }
    }
    echo json_encode($resArray);
}

function printjsonCityPeople($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new UserViewCityModel($row['Id'], $row['Name'], $row['Surname'], $row['Status'], $row['CityId']));
        }
    }
    echo json_encode($resArray);
}

function printjsonRole($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new RoleViewModel($row['Id'], $row['Name']));
        }
    }
    echo json_encode($resArray);
}

function printjsonUserPosts($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new UserPostViewModel($row['UserId'], $row['Text'], $row['Date']));
        }
    }

    echo json_encode($resArray);
}

function printjsonPhotos($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new PhotoViewModel($row['Link'], $row['UserId']));
        }
    }

    echo json_encode($resArray);
}

function printjsonPosts($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new PostViewModel($row['Text'], $row['Date']));
        }
    }
    echo json_encode($resArray);
}

function printjsonMessages($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new MessageViewModel($row['UserAimId'], $row['UserOwnerId'], $row['Text'], $row['Date']));
        }
    }
    echo json_encode($resArray);
}

function printjsonMessagesWithLimit($res, $off, $lim)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        $count = 0;
        while ($row = $res->fetch_assoc()) {
            if($count >= $off){
                if($count < $lim){
                    array_push($resArray, new MessageViewModel($row['UserAimId'], $row['UserOwnerId'], $row['Text'], $row['Date']));
                }
            }
        }
    }
    echo json_encode($resArray);
}