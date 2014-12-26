<?php

include '../classes/module.php';

error_reporting(null);

$db = new DB();
$db->defaultConnect();

$fields = array('order_id', 'date', 'price', 'currency', 'tax', 'delivery', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'type');
$fields = implode(',', $fields);
$fieldsGoods = 'order_id, product_id, product_name, product_price, product_quantity';

$utm_source = array('google','yandex','mail','price');
$utm_medium = array('cpc','display','price','retargeting','affiliate','social_cpc','special');
$utm_campaign = array('detskye_tovary','mashiny','kvartiry');
$utm_term = array('kupyt_a4','kupyt_kolyasky','kupyt_traktor');
$utm_content = array('12-12-2014','23-12-2014');
$product_name = array('Пылесос','Планшет','Деревяшка','Телефон','Мышка','Компьютер');

for ($i=0; $i < 2000 ; $i++) { 
    echo $i.'<br/>';
    $order_id_random = rand(1, 999999);
    $cur_time_posix_random = rand(1356381130, 1450989130);
    $order_price_random = rand(200, 20000);
    $tax_random = rand(0, 400);
    $delivery_random = rand(0, 400);
    $utm_source_random = $utm_source[array_rand($utm_source)];
    $utm_medium_random = $utm_medium[array_rand($utm_medium)];
    $utm_campaign_random = $utm_campaign[array_rand($utm_campaign)];
    $utm_term_random = $utm_term[array_rand($utm_term)];
    $utm_content_random = $utm_content[array_rand($utm_content)];
    $values = "$order_id_random, $cur_time_posix_random, $order_price_random, 'RUR', $tax_random, $delivery_random, '$utm_source_random', '$utm_medium_random', '$utm_campaign_random', '$utm_term_random', '$utm_content_random', 'phone'";
    $query = mysql_query("INSERT INTO `moiai_orderGoals` ($fields) VALUES ($values)");

    $itemCount = rand(1,3);
    for ($j=0; $j < $itemCount ; $j++) {
        $product_id_random = rand(1, 999999);
        $product_name_random = $product_name[array_rand($product_name)];
        $product_price_random = rand(100, 10000);
        $product_quantity_random = rand(1, 10);
        $valuesGoods = array();
        $valuesGoods[] = '('.$order_id_random.','.$product_id_random.',"'.$product_name_random.'",'.$product_price_random.','.$product_quantity_random.')'; 
        $valuesGoods = implode(',', $valuesGoods);
        $queryGoods = mysql_query("INSERT INTO `moiai_orderGoods` ($fieldsGoods) VALUES $valuesGoods");
    }

}

?>