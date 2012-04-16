<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

define('START_APP', TRUE);
define('ROOT_PATH', dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR);
define('LIB_PATH', ROOT_PATH . 'library' . DIRECTORY_SEPARATOR);

require_once ROOT_PATH . 'application.php';
require_once ROOT_PATH . 'settings.php';

$app = Application::getInstance();

$app->registryAutoload(ROOT_PATH);
$app->registyLib('captcha');
$app->includeLib('activerecords', 'ActiveRecord.php');
$app->includeLib('database', 'setup.php');

echo $app->create(Request::getInstance(Router::detect_uri()))
    ->run();