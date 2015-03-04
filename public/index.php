<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));
error_reporting(E_ALL);
// Setup autoloading
require 'init_autoloader.php';

// Run the application!
$application = Zend\Mvc\Application::init(require 'config/application.config.php')->run();
