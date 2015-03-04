<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

return array(
   'db' => array(
      'driver'         => 'Pdo',
      'dsn'            => 'mysql:dbname=xiaolajiao;host=192.168.1.219:3306',
      'driver_options' => array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
      ),
   ),
   'service_manager' => array(
      'factories' => array(
         'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
      ),
   ),
    'php_settings' => array(
		'display_startup_errors'     => false,
		'display_errors'             => true,
// 		'max_execution_time'         => 30,
		'date.timezone'              => 'Asia/Shanghai',
		'memory_limit'               => '500M',
    ),
);