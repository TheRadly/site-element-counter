<?php

namespace App\Services;

use App\Utils\MySQL;

class UserService {

    public function GetUser($login){

        $stm = MySQL::$db->prepare("SELECT * FROM users WHERE userLogin = :userLogin");

        $stm->bindParam(':userLogin', $login,\PDO::PARAM_STR);
        $stm->execute();

        return $stm->fetch(\PDO::FETCH_OBJ);

    } // GetUser

    public function UpdateHash($login, $hash){

        $stm = MySQL::$db->prepare("UPDATE users SET userHash = :hash WHERE userLogin = :userLogin");

        $stm->bindParam(':userLogin', $login,\PDO::PARAM_STR);
        $stm->bindParam(':hash', $hash,\PDO::PARAM_STR);

        $stm->execute();

    } // UpdateHash

} // UserService
