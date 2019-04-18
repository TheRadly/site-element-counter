<?php

namespace App\Controllers;

use \Twig_Loader_Filesystem;
use \Twig_Environment;
use App\Utils\MySQL;

class BaseController {

    // Variables for the Twig Library
    protected $loader;
    protected $twig;

    public function __construct(){

      // User name for DB connect
      $user = 'root';
      // User password for DB connect
      $pass = 'root';
      // Data for connect to DB
      $dsn = 'mysql:dbname=colnect;host=localhost;port=8889;charset=utf8;';

      try {

        // Call class PDO for connection to DB
        MySQL::$db = new \PDO(
            $dsn,
            $user,
            $pass
        );

      } // try
      catch(PDOException $ex){
          echo "Connection error.\n Info: $ex->getMessage()";
      } // catch

        // Connecting the folder with templates for twig
        $this->loader = new Twig_Loader_Filesystem('./App/Templates');
        $this->twig = new Twig_Environment($this->loader);

    } // __construct

} // BaseController
