<?php
namespace namche;



require_once dirname(__FILE__) . '/../vendor/autoload.php';

class Bootstrap {

    const DB_HOST = 'localhost';
    const DB_NAME = 'namche_db'; 
    const DB_USER = 'root'; 
    const DB_PASS = ''; 
    const DB_TYPE = 'mysql';

    const APP_DIR ='C:/xampp/htdocs/DT/';

    const NAMCHE_DIR = self::APP_DIR . 'namche/' ;
    const IMAGE_DIR = self::NAMCHE_DIR . 'images/';

    const TEMPLATE_DIR = self::APP_DIR . 'templates/';
    const CACHE_DIR = false;
    

    const APP_URL = 'http://localhost/DT/';

    const ENTRY_URL = self::APP_URL . 'namche/';

    public static function loadClass($class)
    {

       
        $path = str_replace('\\', '/', self::APP_DIR. $class . '.class.php'); 
        require_once $path;
    }
   
}

spl_autoload_register([
    'namche\Bootstrap',
    'loadClass'
]);







?>
