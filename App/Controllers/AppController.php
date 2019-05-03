<?php

namespace App\Controllers;

use App\Controllers\BaseController as BaseController;
use Bramus\Router\Router;


class AppController extends BaseController {

    public function Start() {

        // Call new Router class for using
        $router = new Router();

        // Connecting a file with routes
        $routes = include_once('./App/Routes/Routing.php');

        // Search GET and POST methods in array
        foreach ($routes as $key => $path) {

              foreach ($path as $subKey => $value) {

                  $router->$key( $subKey , $value );

              } // foreach

        } // foreach

        // Set namespace for Router
        $router->setNamespace('App\\Controllers');

        // Run plugin router
        $router->run();

    } // Start

} // AppController
