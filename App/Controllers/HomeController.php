<?php

namespace App\Controllers;

use App\Controllers\BaseController as BaseController;
use App\Models\StatisticsModel;
use phpQuery;

require('./App/Libs/phpQuery.php');

class HomeController extends BaseController {

    // Public $data for AJAX response
    public $data = [];

    public function InitializeMainPage() {

        // Load twig page Home.twig
        $template = $this->twig->load("Home.twig");

        // Render Home.twig page
        echo $template->render();

    } // InitializeMainPage

    public function ParseDataToAJAX(){

      // Get url and tag from POST
      $url = $_POST['searchUrl'];
      $tag = $_POST['tag'];

      if(isset($url) && $url != '' || isset($tag) && $tag != ''){

        try {

            $statisticsModel = new StatisticsModel();

            // Start timer
            $start = microtime(true);

            // Save data array for AJAX
            $this->data['siteUrl'] = $url;
            $this->data['fetched'] = $this->GetRealTime();

            // Validation check HTML tag
            $checkedTags = $this->ValidateElements($tag);
            // Calling a function that returns the number of elements, and checking for emptiness
            $countElements = $this->ValidateUrl($url);

            
            $checkedTags == 'Element not found' ? $this->data['invalidTag'] = 'Invalid' : $checkedTags;
            $countElements == 'Search error' ? $this->data['invalidUrl'] = 'Invalid' : $this->data['countImg'] = $countElements;

            // Stop timer
            $this->data['timeTook'] = round(microtime(true) - $start, 4);

            // Save data to DB
            $statisticsModel->SaveRequest($this->data['checkedUrl'], $this->data['fetched'], $this->data['timeTook'], $this->data['countImg'], $this->data['checkedElement']);
            // Get count URL from current domain
            $this->data['countUrl'] = $statisticsModel->GetCountDataFromDB($this->data['checkedUrl'], 'url_id');
            // Get domain to data
            $this->data['domain'] = $statisticsModel->GetDomain($this->data['checkedUrl'])->name;
            // Get count elements
            $this->data['countElements'] = $statisticsModel->GetCountElementsFromDomain($this->data['checkedUrl'], $this->data['checkedElement']);
            // Get count all elements
            $this->data['countAllElements'] = $statisticsModel->GetCountAllElements($this->data['checkedElement']);
            // Get count duration
            $this->data['countDuration'] = $statisticsModel->GetCountAllDuration($this->GetPrevDay(), $this->GetRealTime());


            // Sending data in JSON format to AJAX
            echo json_encode($this->data);
            
            return $this->data;

        } // try
        catch(\Exception $ex){

          return "Exception error: $ex";

        } // catch

      } // if
      else {

          echo 'Search input empty';

          return 0;

      } // else

      return 0;

    } // ParseURL

    public function GetRealTime(){

        // Getting Moscow time zone
        date_default_timezone_set('Europe/Moscow');

        // Get current date
        $date = date('Y-m-d H:i:s');

        return $date;

    } // GetRealTime

    public function GetPrevDay(){

        // Get previous date, to DB
        return date('Y-m-d H:i:s', strtotime($this->GetRealTime() . ' -1 day'));

    } // GetPrevDay

    public function FindElementTag($url, $tag, $count = false){

        try {

            $url_p = parse_url($url);

            // Check for the existence of the site
            if (!empty ($url_p ['host']) and checkdnsrr($url_p ['host'])){

                // Getting data from the site in the form of - phpQuery_object
                $html = file_get_contents($url);
                $document = phpQuery::newDocument($html);

                // Save data to data-array
                $this->data['checkedUrl'] = $url;

                if($count == true){

                    // Get array elements
                    $element = $document->find($tag);

                    if($element == null){

                        return 'Search error';

                    } // if

                    // Search for a certain number of tags, in our case, this is <img>
                    $searchImg = $element->count();

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
        catch (\Exception $ex){

            return 'Search error';

        } // catch

    } // FindElementTag

    public function ValidateUrl($url){

        try {

            // Check for content in the string of the word http, it is necessary for the library,
            // because if the string for the site is raw, without a host, then it will not work.
            // And in this case, this check was developed to check the validity of the URL.

            if(stristr($url, 'http') === false){

                // Adding to the existing http prefix string
                $new_url = 'http://' . $url;

                return $this->FindElementTag($new_url, $this->data['checkedElement'], true);

            } // if
            else {

                return $this->FindElementTag($url, $this->data['checkedElement'], true);

            } // else

        } // try

        catch (\Exception $ex){

            return 'Search error';

        } // catch

    } // GetCountImgElements

    public function ValidateElements($element){

        // Include const elements array from Elements.php
        $constantsElements = include_once('./App/Validate/Elements.php');

        // Match search
        foreach ($constantsElements as $elem){

            if($element == $elem){
                
                return $this->data['checkedElement'] = $element;
                
            } // if

        } // foreach

        return 'Element not found';

    } // ValidateElements

} // HomeController
