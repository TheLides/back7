<?php

function connectToDB()
{
    $connection = new mysqli("127.0.0.1", "root", "root", "lab7bd");
    if(!$connection){
        return null;
    }
    return $connection;

}

