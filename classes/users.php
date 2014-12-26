<?php

/**
 * Класс работы с пользователями
 */
class Users implements IUsers
{
    /**
     * Проверить правильность установленных админских кук
     * @return string
     */
    public function checkAdminCookie(){
        $config = new Config();
        $ini_array = $config->getIniConfig('../config.ini', 'sign-in');

        if( !$_COOKIE["admin_check"] || $_COOKIE["admin_check"] != "login=".$ini_array['module_login']."&password=".$ini_array['module_password']."&salt=".$ini_array['admin_salt'] )
            return "false";
        else
            return "true";        
    }

    public function test(){
        return "test";
    }
}

?>