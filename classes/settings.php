<?php

class Settings
{
	public function getUtmSection()
	{	
		$query = mysql_query("SELECT * FROM moiai_utm WHERE `new` <> 'true'");

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
          if($row['section'] == 'main_section')
            $utm_section[$row['name']] = $row;
          else{
              $utm_section[$row['section']]['SUBSECTION'][] = $row;
          }
        }

        return $utm_section;
	}

	public function getOtherGoalsQueryHeads()
	{
		$result = array();
		$query = mysql_query("SELECT * FROM `moiai_otherGoalsDescr`");
        $queryHeads = mysql_query("SHOW columns FROM `moiai_otherGoalsDescr`");

        while ($row = mysql_fetch_array($queryHeads, MYSQL_ASSOC)) {
        	array_push($result, $row);
        }

        return $result;
	}
}

?>
