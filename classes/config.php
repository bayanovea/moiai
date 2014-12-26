<?php

/**
 * Класс работы с файлами настроек
 */
class Config implements iConfig
{
	
    /**
     * Получить значения из ini-файла
     * @param string $file путь до ini файла
     * @param string $section выбор секции
     * @param string $subsection выбор значения внутри секции
     * @return array, string 
     */
    public function getIniConfig($file, $section = false, $subsection = false){
    	
    	$ini_array = parse_ini_file($file, true);

    	if(!$ini_array){
    		echo "Ошибка доступа к конфигурационному файлу";
    		return false;
    	}

    	if($section && $subsection)
    		return $ini_array[$section][$subsection];
    	elseif($section)
    		return $ini_array[$section];
    	else
    		return $ini_array;
    }

}

?>