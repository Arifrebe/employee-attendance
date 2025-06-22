<?php
session_start();

function loadPage($page, $id= null){
    $file = $page . ".php";
    
    if ($id !== null) {
        $_GET['id'] = $id;
    }
    if (!file_exists($file)) {
        require "404.php";
        return;
    }
    
    require $file;
}

function sendResponse($response) {
    file_put_contents('last_response.json', json_encode($response));
    echo json_encode($response);
    exit;
}

$requestUri = trim($_SERVER['REQUEST_URI'],'/');
$baseDir = 'employee-attendances';

if (str_starts_with($requestUri, $baseDir)) {
    $requestUri = substr($requestUri , strlen($baseDir));
}

$segment = explode('/', $requestUri);

$page = isset($segment[1]) ? $segment[1] : 'home';
$id = isset($segment[2]) ? $segment[2] : null;

loadPage($page, $id);