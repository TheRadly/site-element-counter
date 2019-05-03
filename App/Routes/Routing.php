<?php

return array(

    'get' => [
                '/' => 'HomeController@InitializeMainPage'
            ],
    'post' => [
                '/' => 'HomeController@ParseDataToAJAX'
            ]

); // return array
