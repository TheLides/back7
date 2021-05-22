<?php
header('Content-Type: application/json');
function getFormData($method) {
    if ($method === 'GET') return $_GET;
    if ($method === 'POST' && !empty($_POST)) return $_POST;

    $incomingData = file_get_contents('php://input');
    $decodedJSON = json_decode($incomingData, true); //пытаемся преобразовать то, что нам пришло из JSON в объект PHP
    if ($decodedJSON)
    {
        $data = $decodedJSON;
    }
    else
    {
        $data = array();
        $exploded = explode('&', file_get_contents('php://input'));
        foreach($exploded as $pair)
        {
            $item = explode('=', $pair);
            if (count($item) == 2)
            {
                $data[urldecode($item[0])] = urldecode($item[1]);
            }
        }
    }
    return $data;
}

$method = $_SERVER['REQUEST_METHOD'];

$formData = getFormData($method);

$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/'); //Удаляет "/" из конца строки
$urls = explode('/', $url);

// Определяем роутер и url data
$router = $urls[0];
$urlData = array_slice($urls, 1); //удаляем роутер из запроса

// Подключаем файл-роутер и запускаем главную функцию
include_once 'routers/' . $router . '.php'; //include_once подключает внешний файл с кодом
route($method, $urlData, $formData);
