<?php

class NewWords
{
	public function getAllNewWords()
	{	
		$result = array();
		$query = mysql_query("SELECT * FROM `moiai_utm` WHERE `new` = 'true'");
		
		while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
			array_push($result, $row);
		}

		return $result;
	}
}

?>
