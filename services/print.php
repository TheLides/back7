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
    $resArrayPost = array();
    $previousId = 0;
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            if($previousId != $row['Id']){
                $resArrayPost = array();
                if ($row['Text'] != null) {
                    array_push($resArrayPost, new PostUserViewModel($row['Text'], $row['Date']));
                }
                $previousId = $row['Id'];
                array_push($resArray, new UserForAdminViewModel($row['Id'], $row['Name'], $row['Surname'], $row['Status'], $row['Birthday'], $row['RoleId'], $resArrayPost));

            } else {
                if ($row['Text'] != null) {
                    array_push($resArray[count($resArray) - 1]->postArray, new PostUserViewModel($row['Text'], $row['Date']));
                }
            }
//            if ($per == 1){
//                array_push($resArrayPost, new PostUserViewModel($row['Text'], $row['Date']));
//            }
//            else {
//                array_push($resArray, new UserViewModel($row['Id'], $row['Name'], $row['Surname'], $row['Status']));
//            }

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

function printjsonUserPosts($res, $name)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new UserPostViewModel($row['Id'], $row['UserId'], $row['Text'], $row['Date']));
        }
    }

    echo json_encode($resArray);
}

function printjsonPhotos($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new PhotoViewModel($row['Id'], $row['Link'], $row['UserId']));
        }
    }

    echo json_encode($resArray);
}

function printjsonPosts($res)
{
    $resArray = array();
    $resPostArray = array();
    if ($res->num_rows > 0) {

        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new PostViewModel($row['Id'], $row['Text'], $row['Date']));
        }
    }
    echo json_encode($resArray);
}

function printjsonMessages($res)
{
    $resArray = array();
    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            array_push($resArray, new MessageViewModel($row['Id'], $row['UserAimId'], $row['UserOwnerId'], $row['Text'], $row['Date']));
        }
    }
    echo json_encode($resArray);
}



function print418($message){
    header('HTTP/1.0 418 I`m a teapot');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print404($message){
    header('HTTP/1.0 404 Not found');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print403($message){
    header('HTTP/1.0 403 Forbidden');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print204($message){
    header('HTTP/1.0 204 No Content');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print409($message){
    header('HTTP/1.0 409 Conflict');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print400($message){
    header('HTTP/1.0 400 Bad request');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}

function print406($message){
    header('HTTP/1.0 406 Not acceptable');
    $obj = new stdClass();
    $obj->message = $message;
    echo json_encode($obj);
}