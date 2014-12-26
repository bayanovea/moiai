<?php

/**
 * Класс работы с utm-метками
 */
class Utm implements IUtm
{    
    
    /**
     * Парсинг utm_get Cookie
     * @return array
     */
    public function utmGetParse(){

        $utm = $_COOKIE['utm_get'];
        $utm = explode('&', $_COOKIE['utm_get']);

        foreach ($utm as $key => $value) {
            $utm = explode('=', $value);
            if ( ($utm[1][0] == '(') && ($utm[1][strlen($utm[1])-1] == ')') )     // Обрезаем начальные и конечные скобки
                $utm[1] = substr($utm[1], 1, strlen($utm[1])-2);

            if($utm[1] == 'undefined')
                $utm[1] = 'NULL';

            $utm_array[$utm[0]] = $utm[1];           
        }

        return $utm_array;
    }

    /**
     * Парсинг __utmz Cookie
     * @return array
     */
    public function utmzParse(){

        $utm = $_COOKIE['__utmz'];
        $utm = explode('.', $utm);
        $utm = explode('|', $utm[count($utm)-1]);
    
        foreach ($utm as $key => $value) {
            $utm = explode('=', $value);
            if ( ($utm[1][0] == '(') && ($utm[1][strlen($utm[1])-1] == ')') )     // Обрезаем начальные и конечные скобки
                $utm[1] = substr($utm[1], 1, strlen($utm[1])-2);

            if($utm[1] == 'undefined')
                $utm[1] = 'NULL';

            $utm_array[$utm[0]] = $utm[1];           
        }

        return $utm_array;
    }
}

?>