<?php

class Check
{
	public function getUtmSectionCount()
	{
		$query = mysql_query("SELECT COUNT(*) FROM `moiai_utm` WHERE `section` LIKE 'main_section'");
      
        $result = mysql_fetch_array($query);
        $utm_section_count = $result[0];

        return $utm_section_count;
	}
}

?>
