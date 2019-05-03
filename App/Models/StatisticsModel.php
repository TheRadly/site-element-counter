<?php


namespace App\Models;

use App\Utils\MySQL;

class StatisticsModel {

    public static function SaveData($data, $table){

        $stm = MySQL::$db->prepare("INSERT INTO $table VALUES( DEFAULT, :data)");

        $stm->bindParam(":data" , $data , \PDO::PARAM_STR);
        $stm->execute();

        return MySQL::$db->lastInsertId();

    } // SaveData
    public static function GetData($data, $table){

        $stm = MySQL::$db->prepare("SELECT * FROM $table WHERE name = :data");

        $stm->bindParam(':data', $data, \PDO::PARAM_STR);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);

    } // GetData

    public function SaveUrl($url){

        return $this->SaveData($url, 'url');

    } // SaveUrl
    public function GetUrl($url){

        return self::GetData($url, 'url');

    } // GetUrl

    public function SaveDomain($url){

        $pUrl = parse_url($url);
        $domain = $pUrl['host'];

        return $this->SaveData($domain, 'domain');

    } // SaveDomain
    public function GetDomain($url){

        $pUrl = parse_url($url);
        $domain = $pUrl['host'];

        return self::GetData($domain, 'domain');

    } // GetDomain

    public function SaveElement($element){

         return $this->SaveData($element, 'element');

    } // SaveElement
    public function GetElement($element){

        return self::GetData($element, 'element');

    } // GetElement

    public function SaveRequest($url, $time, $duration, $countElements, $element = 'img'){

        $requestUrl = $this->GetUrl($url);
        $requestDomain = $this->GetDomain($url);
        $requestElement = $this->GetElement($element);

        $urlID = !$requestUrl ? $this->SaveUrl($url) : $requestUrl->id;
        $domainID = !$requestDomain ? $this->SaveDomain($url) : $requestDomain->id;
        $elementID = !$requestElement ? $this->SaveElement($element) : $requestElement->id;

        $stm = MySQL::$db->prepare("INSERT INTO request VALUES( DEFAULT, :domainID, :urlID, :elementID, :countElements, :timeFetch, :duration)");

        $stm->bindParam(":domainID" , $domainID , \PDO::PARAM_INT);
        $stm->bindParam(":urlID" , $urlID , \PDO::PARAM_INT);
        $stm->bindParam(":elementID" , $elementID , \PDO::PARAM_INT);
        $stm->bindParam(":countElements" , $countElements , \PDO::PARAM_INT);
        $stm->bindParam(":timeFetch" , $time , \PDO::PARAM_STR);
        $stm->bindParam(":duration" , $duration , \PDO::PARAM_STR);

        $stm->execute();

        return MySQL::$db->lastInsertId();

    } // SaveRequest

    public function GetCountDataFromDB($url, $countCol){

        $getDomain = $this->GetDomain($url);
        $domainID = !$getDomain ? $this->SaveDomain($url) : $getDomain->id;

        $stm = MySQL::$db->prepare("SELECT COUNT($countCol) FROM request WHERE :domainID = domain_id");

        $stm->bindParam(":domainID" , $domainID , \PDO::PARAM_INT);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_NUM);

    } // GetCountDataFromDB

    public function GetCountElementsFromDomain($url, $element){

        $getDomain = $this->GetDomain($url);
        $domainID = !$getDomain ? $this->SaveDomain($url) : $getDomain->id;

        $getElement = $this->GetElement($element);
        $elementID = !$getElement ? $this->SaveElement($element) : $getElement->id;

        $stm = MySQL::$db->prepare("SELECT SUM(count_elements) FROM request WHERE domain_id = :domainID AND element_id = :elementID");

        $stm->bindParam(":domainID" , $domainID , \PDO::PARAM_INT);
        $stm->bindParam(":elementID" , $elementID , \PDO::PARAM_INT);

        $stm->execute();

        return $stm->fetch(\PDO::FETCH_NUM);

    } // GetCountElementsFromDomain

    public function GetCountAllElements($element){

        $getElement = $this->GetElement($element);
        $elementID = !$getElement ? $this->SaveElement($element) : $getElement->id;

        $stm = MySQL::$db->prepare("SELECT SUM(count_elements) FROM request WHERE element_id = :elementID");
        $stm->bindParam(":elementID" , $elementID , \PDO::PARAM_INT);

        $stm->execute();

        return $stm->fetch(\PDO::FETCH_NUM);

    } // GetCountAllElements

    public function GetCountAllDuration($prevDate, $realDate){

        $stm = MySQL::$db->prepare("SELECT SUM(duration) FROM request WHERE time >= '$prevDate' AND time <= '$realDate'");
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_NUM);

    } // GetCountAllFetch

} // StatisticsModel