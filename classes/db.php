<?

/**
 * Класс работы с БД
 * 
 */
class DB implements IDB
{

    /**
     * Соедениться с БД
     * @param string $host имя хоста
     * @param string $user пользователь
     * @param string $password пароль
     * @param string $db название БД
     * @return bool
     */
    public function connect($host, $user, $password, $db){
    	
    	if(!mysql_connect($host, $user, $password)){
    		echo "mysql connect error";
    		return false;
    	}

		if(!mysql_select_db($db)){
			echo "mysql select db error";
    		return false;
		}
		else 
			return true;
    	
    }

    /**
     * Соедениться к БД со стандартными настройками
     * @return bool
     */
    public function defaultConnect(){   
        
        $config = new Config();
        $ini_array = $config->getIniConfig('../config.ini', 'database');

        if(!mysql_connect($ini_array['host'], $ini_array['user'], $ini_array['password'])){
            echo "mysql connect error";
            return false;
        }

        if(!mysql_select_db($ini_array['db'])){
            echo "mysql select db error";
            return false;
        }
        else 
            return true;

    }
    
}

?>