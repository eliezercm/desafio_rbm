<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\Database\Connector;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbConnection = (new Connector())->getConnection();