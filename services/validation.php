<?php


function validateString($res)
{
    return (!is_null($res) && is_string($res) && !empty($res));
}

function validateNumber($res)
{
    return (!is_null($res) && is_numeric($res) && !empty($res));
}

function validateStatusEnum($res){
    $statusEnum = array("Online", "Offline", "Do not disturb", "In panic", "Want to die");
    return (in_array($res, $statusEnum));
}