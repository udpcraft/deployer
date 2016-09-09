<?php

define('DS', DIRECTORY_SEPARATOR);

define('ROOT_DIR', dirname(__DIR__));

define('APP_LOADER_PREFIX', '_');

$enableModules = ['site', 'user', 'deploy'];

require ROOT_DIR . DS . 'vendor/afanty/loader.php';

Afanty\Loader\Autoloader::addnamespace('Afanty', ROOT_DIR . DS . 'vendor' . DS . 'afanty');

Afanty\Loader\Autoloader::register();


Afanty\Events\Manager::add('Event\Request\Login');
Afanty\Events\Manager::add('Event\Request\Auth');

$application = Afanty\Web\Application::getInstance();
$application->setEnableModules($enableModules);


return $application;
