<?php

require "../bootstrap.php";

use Src\App\Controllers\PersonController;
use Src\App\Controllers\SaleController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$requestMethod = $_SERVER["REQUEST_METHOD"];

$saleId = null;

$controller = null;

switch($uri[1]) {
    case 'sale':
        if (isset($uri[2])) {
            $saleId = (int) $uri[2];
        }
        $controller = new SaleController($dbConnection, $requestMethod, $saleId);
    break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(array("error" => "Endpoint inválido! Consulte a documentação da API."));
        exit;
    break;
}

$controller->processRequest();