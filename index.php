<?php
//session_save_path('/home/content/11/12262211/html/sessions');
ini_set('session.gc_probability', 1);
ini_set('memory_limit', '128M');
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the admin root now.
 */
chdir(__DIR__);
define('ROOT_PATH', __DIR__);
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV',
(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
    : 'production'));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

// Run the admin!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();