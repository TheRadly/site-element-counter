<?php

return array(

    'get' => [
                '/' => 'HomeController@InitializeMainPage'
            ],
    'post' => [
                '/' => 'HomeController@ParseURL'
            ]
);
