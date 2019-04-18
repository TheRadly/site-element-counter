<?php

/**
 * Created by PhpStorm.
 * User: Radly
 * Date: 22.02.2019
 * Time: 23:32
 */

require_once './vendor/autoload.php';

use App\Controllers\AppController;

$app = new AppController();
$app->Start();
