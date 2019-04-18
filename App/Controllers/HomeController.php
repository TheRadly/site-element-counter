<?php

namespace App\Controllers;

use App\Controllers\BaseController as BaseController;
use mysql_xdevapi\Exception;
use phpQuery;

require('./App/Libs/phpQuery.php');

class HomeController extends BaseController {

    public function InitializeMainPage() {

        echo $this->LoadTwigPage('Home.twig');

    } // InitializeMainPage

    public function LoadTwigPage($name, $args = []){

        $template = $this->twig->load($name);

        return $template->render($args);

    } // LoadPage

    public function ParseURL(){

      if(isset($_POST['searchUrl']) && $_POST['searchUrl'] != ''){

        try {

            // Start timer
            $start = microtime(true);

            // Get url from POST
            $url = $_POST['searchUrl'];

            // Create array for AJAX
            $data = [];
            $data['siteUrl'] = $url;
            $data['fetched'] = $this->GetRealTime();

            $countElements = $this->GetCountImgElements($url);
            $countElements == 'Search error' ? $data['invalid'] = 'Invalid' : $data['countImg'] = $countElements;

            $data['timeTook'] = round(microtime(true) - $start, 4);

            echo json_encode($data);

            return $countElements;

        } // try
        catch(Exception $ex){

          return "Exception error: $ex";

        } // catch

      } // if

        return 0;

    } // ParseURL

    public function GetRealTime(){

        date_default_timezone_set('Europe/Moscow');
        $date = date('m/d/Y h:i:s', time());

        return $date;

    } // GetRealTime

    public function FindElementTag($url, $tag, $count = false){

        try {

            $url_p = parse_url($url);

            if (!empty ($url_p ['host']) and checkdnsrr($url_p ['host'])){

                $html = file_get_contents($url);
                $document = phpQuery::newDocument($html);

                if($count == true){

                    $searchImg = $document->find($tag)->count();
                    return $searchImg === 0 ? 'empty' : $searchImg;

                } // if

                else{

                    $document->find($tag);
                    return $document;

                } // else

            } // if

            else {
                return 'Search error';
            } // else


        } // try
        catch (Exception $ex){

            return 'Search error';

        } // catch

    } // FindElementTag

    public function GetCountImgElements($url){

        try {

            if(stristr($url, 'http') === false ||
                stristr($url, 'https') === false){

                $new_url = 'http://' . $url;

                return $this->FindElementTag($new_url, 'img', true);

            } // if
            else {

                return $this->FindElementTag($url, 'img', true);

            } // else

        } // try

        catch (Exception $ex){

            return 'Search error';

        } // catch

    } // GetCountImgElements

} // HomeController
