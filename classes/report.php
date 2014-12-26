<?php

class Report
{
	public function getAllOrderGoals($num)
	{
    	$query = mysql_query("SELECT * FROM `moiai_orderGoals` ORDER BY `date` DESC LIMIT 1,".$num);
		if($query) {
          while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            foreach ($row as $key => $value) {
              $order[$row['id']][$key] = $value;
              $queryGoods = mysql_query("SELECT * FROM `moiai_orderGoods` WHERE `moiai_orderGoods`.`order_id` = ".$row['order_id']);
              
                while ($rowGoods = mysql_fetch_array($queryGoods, MYSQL_ASSOC)) {
                  foreach ($rowGoods as $keyGoods => $valueGoods) {
                    $order[$row['id']]['goods'][$rowGoods['id2']][$keyGoods] = $valueGoods;
                  }
                }
            }              
          }
  
        }   

        return $order;  
	}
	
}

?>