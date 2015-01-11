<? 
    include '../classes/module.php'; 
?>

<?

switch ($_POST['type']) {

/* Проверка логина и пароля */
case 'sign-in-check':

  $config = new Config();
    $ini_array = $config->getIniConfig('../config.ini', 'sign-in');

  if(($_POST['module_login'] == $ini_array['module_login']) && ($_POST['module_password'] == $ini_array['module_password'])){
        $cookie_string = "login=".$_POST['module_login']."&password=".$_POST['module_password']."&salt=".$ini_array['admin_salt'];
        echo json_encode($cookie_string);
    }
  else
    echo json_encode("false");

    break;

/* Удаление utm-метки */
case 'utm-tag-delete':
       
    $db = new DB();
    $db->defaultConnect();

    $delete_string = $_POST['delete_string'];

    $delete_string = substr($delete_string, 0, strlen($delete_string)-1 ); 
    $delete_string = str_replace(',', ' OR `id` = ', $delete_string);      

    $query = mysql_query("DELETE FROM `moiai_utm` WHERE `id` = ".$delete_string);     
    
    if($query)
        echo json_encode("true");
    else
        echo json_encode("false");

        echo json_encode($delete_string);

    break;

/* Добавление utm-метки */
case 'utm-tag-add':
   
    $db = new DB();
    $db->defaultConnect();

    if($_POST["show"] == 'true')
        $show = 1;
    else
        $show = 0;

    $insert = '`name`, `rus_name`, `show`, `priority`, `section`';
    $values = "'{$_POST["name"]}', '{$_POST["rus_name"]}', {$show}, {$_POST["priority"]}, '{$_POST["section"]}'";

    $query = mysql_query("INSERT INTO `moiai_utm` (".$insert.") VALUES (".$values.")");
    
    if($query)
        echo json_encode("true");
    else
        echo json_encode("false");

    break;

/* Установка id корзины */
case 'set-basket-id':

    $basket_id = array();
    $utmz_array = array();

    // Получаем из БД список существующих в системе меток  
    $db = new DB();
    $db->defaultConnect();
    $query = mysql_query("SELECT id,name,alt_name,section,new FROM moiai_utm");

    // Структурируем список существующих мето
    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
      if($row['section'] == 'main_section')
          $utm_section[$row['name']] = $row;
      else{
          $utm_section[$row['section']]['SUBSECTION'][$row['name']] = $row;
      }
    }

    if($_COOKIE['utm_get']) {                                   // Ищем utm-метки в cookie GET-параметров
        $utm = $_COOKIE['utm_get'];
        $utm = explode('&', $utm);

        foreach ($utm as $key => $value) {
            $utm_array = explode('=', $value);
            $utmz_array[$utm_array[0]] = $utm_array[1];
        }

    } elseif($_COOKIE['__utmz']) {                              // Ищем utm-метки в __utmz-cookies
        $utmz = $_COOKIE['__utmz'];
        $utmz = explode('.', $utmz);
        $utmz = explode('|', $utmz[count($utmz)-1]);
    
        foreach ($utmz as $key => $value) {
            $utmz = explode('=', $value);
            $utmz_array[$utmz[0]] = $utmz[1];
        }

    }  

    // Находим соотвесвующее id, которое будет отображаться в корзине. 
    // Для этого сопоставляем два массива: массив с разбитой utmz_array и массив с существующими в БД метками.
   
    foreach ($utm_section as $name => $section) {

        $utm_tag_name = $utmz_array[$section['name']];
       
        if ( ($utm_tag_name[0] == '(') && ($utm_tag_name[strlen($utm_tag_name)-1] == ')') )     // Обрезаем начальные и конечные скобки
            $utm_tag_name = substr($utm_tag_name, 1, strlen($utm_tag_name)-2);

        $utm_tag_id = $section['SUBSECTION'][$utm_tag_name]['id'];   
        
        // Если совпадает с именем
        if($utm_tag_id){
            if($section['SUBSECTION'][$utm_tag_name]['new'] != 'true')      // Проверка на новое ли это слово
                array_push($basket_id, $utm_tag_id);
            else
                array_push($basket_id, 0);
        }

        // Если не совпадает с именем, пробуем найти совпадения в alt_name
        else {
            $utm_tag_name = $utmz_array[$section['alt_name']];
        
            if ( ($utm_tag_name[0] == '(') && ($utm_tag_name[strlen($utm_tag_name)-1] == ')') )     // Обрезаем начальные и конечные скобки
                $utm_tag_name = substr($utm_tag_name, 1, strlen($utm_tag_name)-2);
    
            $utm_tag_id = $section['SUBSECTION'][$utm_tag_name]['id'];

            if($utm_tag_id){
                if($section['SUBSECTION'][$utm_tag_name]['new'] != 'true')      // Проверка на новое ли это слово
                    array_push($basket_id, $utm_tag_id);
                else
                    array_push($basket_id, 0);
            }

            // Если нету и в alt_name, то выводим добавляем в непроверенные
            else{
                // Добавляем неизвестные слова/фразы в отдельную таблицу
                if( $utmz_array[$section['name']] && $name && $utmz_array[$name] != 'undefined') {
                    
                    $queryNewValues = mysql_query("INSERT INTO `moiai_utm`(`name`, `section`, `new`) VALUES ('".$utmz_array[$name]."', '".$name."', 'true')");
                }

                // И в id корзины добавляем 0
                array_push($basket_id, 0);
            }

        }
            

    }

    // Склеиваем для получения строки, передаём для вывода в корзине
    echo json_encode(implode('.', $basket_id));

    break;

/* Совершение заказа через сайт */
case 'make-order':
    
    $db = new DB();
    $db->defaultConnect();

    $orderParams = $_POST['orderParams'];

    $fields = array('order_id', 'date', 'price', 'currency', 'tax', 'delivery', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'type');
    $fields = implode(',', $fields);

    $utm = new Utm();

    $cur_time_posix = time();

    if($_COOKIE['utm_get']){
        $utm_array = $utm->utmGetParse();
        $values = array('"'.$orderParams['order_id'].'"', $cur_time_posix, $orderParams['order_price'], '"'.$orderParams['currency'].'"', $orderParams['tax'], $orderParams['delivery'], '"'.$utm_array['utm_source'].'"', '"'.$utm_array['utm_medium'].'"', '"'.$utm_array['utm_campaign'].'"', '"'.$utm_array['utm_term'].'"', '"'.$utm_array['utm_content'].'"', '"site"');
    }
    elseif($_COOKIE['__utmz']){
        $utm_array = $utm->utmzParse();
        $values = array('"'.$orderParams['order_id'].'"', $cur_time_posix, $orderParams['order_price'], '"'.$orderParams['currency'].'"', $orderParams['tax'], $orderParams['delivery'], '"'.$utm_array['utmcsr'].'"', '"'.$utm_array['utmcmd'].'"', '"'.$utm_array['utmccn'].'"', '"'.$utm_array['utmctr'].'"', '""', '"site"');
    }
     
    foreach ($values as $key => $value) {
        if( (!$value)  || ($value == '""') )
            $values[$key] = 0;
    }
    $values = implode(',', $values);

    $query = mysql_query("INSERT INTO `moiai_orderGoals` ($fields) VALUES ($values)");

    
    $fieldsGoods = 'order_id, product_id, product_name, product_price, product_quantity';

    foreach ($orderParams['goods'] as $good) {
        $valuesGoods[] = '('.$orderParams['order_id'].','.$good['id'].',"'.$good['name'].'",'.$good['price'].','.$good['quantity'].')';
    }

    $valuesGoods = implode(',', $valuesGoods);
      
    $queryGoods = mysql_query("INSERT INTO `moiai_orderGoods` ($fieldsGoods) VALUES $valuesGoods");

    if($query && $queryGoods)
        echo json_encode("true");
    else
        echo json_encode("false");

    break;

/* Добавление заказа вручную */
case 'do-order-manually':
    
    $db = new DB();
    $db->defaultConnect();

    $fields = array('order_id', 'date', 'price', 'currency', 'tax', 'delivery', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'type');
    $fields = implode(',', $fields);

    $order_id = '0'.rand(1, 999999);

    $basket_id = explode('.', $_POST['basket_id']);
    
    $utm_source = $basket_id[0];
    $utm_medium = $basket_id[1];
    $utm_campaign = $basket_id[2];
    $utm_term = $basket_id[3];
    $utm_content = $basket_id[4];

    $queryUtm = mysql_query("SELECT * FROM `moiai_utm` WHERE 
        (`id` = ".$utm_source." AND `section` = 'utm_source') OR
        (`id` = ".$utm_medium." AND `section` = 'utm_medium') OR
        (`id` = ".$utm_campaign." AND `section` = 'utm_campaign') OR
        (`id` = ".$utm_term." AND `section` = 'utm_term') OR
        (`id` = ".$utm_content." AND `section` = 'utm_content')");

    while ($row = mysql_fetch_array($queryUtm, MYSQL_ASSOC)){
        if($row['section'] == 'utm_source')
            $utm_source = $row['name'];
        elseif($row['section'] == 'utm_medium')
            $utm_medium = $row['name'];
        elseif($row['section'] == 'utm_campaign')
            $utm_campaign = $row['name'];
        elseif($row['section'] == 'utm_term')
            $utm_term = $row['name'];
        elseif($row['section'] == 'utm_content')
            $utm_content = $row['name'];
    }

    $cur_time_posix = time();

    if(!$_POST['tax'])
        $_POST['tax'] = 0;
    if(!$_POST['delivery'])
        $_POST['delivery'] = 0;
    
    $values = array($order_id, $cur_time_posix, $_POST['order_price'], '"'.$_POST['currency'].'"', $_POST['tax'], $_POST['delivery'], '"'.$utm_source.'"', '"'.$utm_medium.'"', '"'.$utm_campaign.'"', '"'.$utm_term.'"', '"'.$utm_content.'"', '"phone"');
    $values = implode(',', $values);

    $query = mysql_query("INSERT INTO `moiai_orderGoals` ($fields) VALUES ($values)");

    $fieldsGoods = 'order_id, product_id, product_name, product_price, product_quantity';

    foreach ($_POST['goods'] as $good) {
        $good = explode('&&', $good);
        $valuesGoods[] = '('.$order_id.','.$good[0].',"'.$good[1].'",'.$good[2].','.$good[3].')';
    }

    $valuesGoods = implode(',', $valuesGoods);
      
    $queryGoods = mysql_query("INSERT INTO `moiai_orderGoods` ($fieldsGoods) VALUES $valuesGoods");

    if($query && $queryGoods)
        echo json_encode("true");
    else
        echo json_encode("false");

    break;

/* Применени фильтров в отчётах */
case 'filter-apply':

    $db = new DB();
    $htmlElements = new HtmlElements;
    $db->defaultConnect();

    $num = 40;
    $queryAdd = "";
    $utmArr = array('utm_source','utm_medium','utm_campaign','utm_term','utm_content','goal_type');
    $utmArrQuery = array();

    foreach ($utmArr as $keySection => $valueSection) {
        if($_POST[$valueSection]){
            if(count($_POST[$valueSection]) > 1){
                $queryAdd = "(";
                foreach ($_POST[$valueSection] as $key => $value) {
                    if($value != end($_POST[$valueSection]))
                        $queryAdd .= " `".$valueSection."` = '".$value."' OR";
                    else
                        $queryAdd .= " `".$valueSection."` = '".$value."'";
                }
                $queryAdd .= ")";
                
            }
            else{
                $queryAdd = " `".$valueSection."` = '".$_POST[$valueSection][0]."' ";
            }
            array_push($utmArrQuery, $queryAdd);
        }
    }

    if($_POST['from'] || $_POST['to']){
        $from = strtotime( str_replace('.', '/', $_POST['from']) );
        $to = strtotime( str_replace('.', '/', $_POST['to']) ) + 86399;

        if($queryAdd){
            $queryAdd .= " AND ";
        }

        if($_POST['from'] && $_POST['to'])
            $queryAdd .= "( `date` >= ".$from." AND `date` <= ".$to.")";
        elseif($_POST['from'])
            $queryAdd .= "`date` >= ".$to;
        elseif ($_POST['to'])  
            $queryAdd .= "`date` <= ".$to;

        array_push($utmArrQuery, $queryAdd);
    }

    if($_POST['price_from'] || $_POST['price_to']){
        $price_from = $_POST['price_from'];
        $price_to = $_POST['price_to'];  

        if($queryAdd){
            $queryAdd .= " AND ";
        }
       
        if($price_from && $price_to)
            $queryAdd .= "( `price` >= ".$price_from." AND `price` <= ".$price_to.")";
        elseif($price_from)
            $queryAdd .= "`price` >= ".$price_from;
        elseif ($price_to)  
            $queryAdd .= "`price` <= ".$price_to;

        array_push($utmArrQuery, $queryAdd);

    }

    $queryAdd = implode(' AND ', $utmArrQuery);

    if($queryAdd)
        $query_string .= "SELECT * FROM `moiai_orderGoals` WHERE ".$queryAdd." ORDER BY `moiai_orderGoals`.`date` DESC LIMIT 1, ".$num;
    else
        $query_string .= "SELECT * FROM `moiai_orderGoals` ORDER BY `moiai_orderGoals`.`date` DESC LIMIT 1, ".$num;

    $query = mysql_query($query_string);
    
    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){

        foreach ($row as $key => $value) {
            $order[$row['id']][$key] = $value;
            
            $queryGoods = mysql_query("SELECT * FROM `moiai_orderGoods` WHERE `moiai_orderGoods`.`order_id` = ".$row['order_id']);
            
            while ($rowGoods = mysql_fetch_array($queryGoods, MYSQL_ASSOC)){
                foreach ($rowGoods as $keyGoods => $valueGoods) {
                  $order[$row['id']]['goods'][$rowGoods['id2']][$keyGoods] = $valueGoods;
                }              
            }

        } 

    }

    $table = '

    <thead>
        <tr>
            <th>id</th>
            <th>id Заказа</th>
            <th>Дата</th>          
            <th>Заказ</th>
            <th>Рекламная площадка</th>
            <th>Тип рекламы</th>
            <th>Название рекламной кампании</th>
            <th>Ключевая фраза</th>
            <th>Контейнер для доп. информации</th>
            <th>Тип</th>
        </tr>
    </thead>';

    $table .= '<tbody>';
    foreach ($order as $id => $value){
        
        $table .= '<tr>
            <td>'.$value['id'].'</td>
            <td>'.$value['order_id'].'</td>
            <td>'.date('d.m.y', $value['date']).'</td>
            <td>
                <img src="http://www.iconsearch.ru/uploads/icons/bnw/48x48/add.png" width="24" class="open-order">
                <span class="general-summ">Общая сумма: <b>'.$value['price'].'</b></span>
                <div class="order-composition">
                    <ul>
                        <li>
                            <b>Состав</b>: <br/>';

                            foreach ($value['goods'] as $key => $good) {
                                
                                if ($good == reset($value['goods']))
                                    $table .= '<div class="product-item product-item-first">';
                                elseif ($good == end($value['goods']))
                                    $table .= '<div class="product-item product-item-last">';                    
                                else
                                    $table .= '<div class="product-item">';
            
                                $table .= '<p>id: '.$good['product_id'].'</p>
                                <p>Название: '.$good['product_name'].'</p>
                                <p>Цена: '.$good['product_price'].'</p>
                                <p>Количество: '.$good['product_quantity'].'</p>
                                </div>';
                            }
                        $table .= '</li>
                        <li><b>Налог</b>: '.$row['tax'].'</li>
                        <li><b>Доставка</b>: '.$row['delivery'].'</li>
                        <li><b>Валюта</b>: '.$row['currency'].'</li>
                    </ul>
                </div>
          </td>
          <td>'.$value['utm_source'].'</td>
          <td>'.$value['utm_medium'].'</td>
          <td>'.$value['utm_campaign'].'</td>
          <td>'.$value['utm_term'].'</td>
          <td>'.$value['utm_content'].'</td>
          <td>'.$value['type'].'</td>
        </tr>';
      
    }
    $table .= '</tbody>';

    $pagination = $htmlElements->buildPagination($num, $queryAdd);
          
    mysql_free_result($query);

    $return[0] = $table;
    $return[1] = $pagination;

    echo json_encode($return);

    break;

/* Расшифровка id корзины */
case 'do-decryption':

    $db = new DB();
    $db->defaultConnect();

    $basket_string = '';
    $queryArr = array();
    $basket_id = explode('.', $_POST['basket_id']);

    $query_string = "SELECT * FROM `moiai_utm` WHERE `id` = ";
    foreach ($basket_id as $value) {
        if( !array_search($value, $queryArr) )
            array_push($queryArr, $value);
        /*if( $value == end($basket_id) )
            $query_string .= "`id` = ".$value;
        else
            $query_string .= "`id` = ".$value." OR ";*/
    }

    $query_string .= implode(" OR `id` = ", $queryArr);

    $query = mysql_query($query_string);

    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
        $basket_string .= "<b>".$row['section']."</b>: ".$row['name']."<br/>";
    }

    echo json_encode($basket_string);

    break;

/* Добавление новых ключевых слов */
case 'confirm-new-word':
    
    $db = new DB();
    $db->defaultConnect();

    $query = mysql_query("UPDATE moiai_utm
        SET `alt_name`='".$_POST['alt_name']."', `rus_name`='".$_POST['rus_name']."', `priority`=".$_POST['priority']." ,`new` = 'false'
        WHERE id=".$_POST['id']);

    if($query)
        echo json_encode("true");

    break;

/* Отклонение новых ключевых слов */
case 'reject-new-word':
    
    $db = new DB();
    $db->defaultConnect();
    
    $query = mysql_query("DELETE FROM `moiai_utm` WHERE `id` = ".$_POST['id']);

    if($query)
        echo json_encode("true");

    break;

/* Форма обратного звонка */
case 'box-conversion-send':

    $db = new DB();
    $db->defaultConnect();

    $form = explode('&', $_POST['form']);

    $formArray = Array();
    foreach ($form as $value) {
        $value = explode('=', $value);
        $formArray[$value[0]] = $value[1];
    }

    $query = mysql_query("INSERT INTO `moiai_otherGoals` (`date`,`utm_source`,`utm_medium`,`utm_campaign`,`utm_term`,`utm_content`,`custom_name`,`custom_phone`,`custom_email`,`passport`,`bank`) 
        VALUES (".time().", '".$formArray['utm_source']."', '".$formArray['utm_medium']."', '".$formArray['utm_campaign']."', '".$formArray['utm_term']."', 
            '".$formArray['utm_content']."','".$formArray['name']."', '".$formArray['phone']."', '".$formArray['email']."', '','')");

    if($query)
        echo json_encode(true);
    else
        echo json_encode(false);

    break;

/* Сохранение настроек подключения */
case 'connection-settings-save':

    $text = '';

    $array = array('sign_in', 'database');

    foreach ($array as $array_value) {        
        $val_section = explode('&', $_POST[$array_value]);
        $text .= '['.$array_value.']'."\n";
        foreach ($val_section as $section_value) {
            $val_subsection = explode('=', $section_value);
            $text .= $val_subsection[0].' = '.$val_subsection[1]."\n";           
        }
        $text .= "\n";
    }

    $check = file_put_contents ('../config.ini', $text);

    echo json_encode("true");
  
    break;

/* Перестроение паджинации при клике */
case 'pagination-page-click':

    $db = new DB();
    $db->defaultConnect();

    $page = $_POST['page'];
    $start = ($page-1)*40 + 1;
   
    $queryAdd = "";
    $utmArr = array('utm_source','utm_medium','utm_campaign','utm_term','utm_content','goal_type');
    $utmArrQuery = array();

    foreach ($utmArr as $keySection => $valueSection) {
        if($_POST[$valueSection]){
            if(count($_POST[$valueSection]) > 1){
                $queryAdd = "(";
                foreach ($_POST[$valueSection] as $key => $value) {
                    if($value != end($_POST[$valueSection]))
                        $queryAdd .= " `".$valueSection."` = '".$value."' OR";
                    else
                        $queryAdd .= " `".$valueSection."` = '".$value."'";
                }
                $queryAdd .= ")";
                
            }
            else{
                $queryAdd = " `".$valueSection."` = '".$_POST[$valueSection][0]."' ";
            }
            array_push($utmArrQuery, $queryAdd);
        }
    }

    $queryAdd = implode(' AND ', $utmArrQuery);

    $queryString = "SELECT * FROM `moiai_orderGoals` WHERE ".$queryAdd." ORDER BY `date` DESC LIMIT ".$start.", 40";

    if($queryAdd)
        $query = mysql_query("SELECT * FROM `moiai_orderGoals` WHERE ".$queryAdd." ORDER BY `date` DESC LIMIT ".$start.", 40");
    else
        $query = mysql_query("SELECT * FROM `moiai_orderGoals` ORDER BY `date` DESC LIMIT ".$start.", 40");

    while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){

        foreach ($row as $key => $value) {
            $order[$row['id']][$key] = $value;

            $queryGoods = mysql_query("SELECT * FROM `moiai_orderGoods` WHERE `moiai_orderGoods`.`order_id` = ".$row['order_id']);
            
            while ($rowGoods = mysql_fetch_array($queryGoods, MYSQL_ASSOC)){
              foreach ($rowGoods as $keyGoods => $valueGoods) {
                $order[$row['id']]['goods'][$rowGoods['id2']][$keyGoods] = $valueGoods;
              }              
            }

          } 

    }

    $return = '<thead>
        <tr>
            <th>id</th>
            <th>id Заказа</th>
            <th>Дата</th>          
            <th>Заказ</th>
            <th>Рекламная площадка</th>
            <th>Тип рекламы</th>
            <th>Название рекламной кампании</th>
            <th>Ключевая фраза</th>
            <th>Доп. информации</th>
            <th>Тип</th>
        </tr>
    </thed>
      
    <tbody>';

    foreach ($order as $id => $value){
        
        $return .= '<tr>
          <td>'.$value['id'].'</td>
          <td>'.$value['order_id'].'</td>
          <td><span class="posix-time" style="display: none;">'.$value['date'].'</span>'. date('d.m.y', $value['date']) .'</td>
          <td>
            <img src="http://www.iconsearch.ru/uploads/icons/bnw/48x48/add.png" width="24" class="open-order">
            <span class="general-summ">Общая сумма: <b>'.$value['price'].'</b></span>
            <div class="order-composition">
              <ul>
                <li>
                  <b>Состав</b>: <br/>';

                  foreach ($value['goods'] as $key => $good){
                    if ($good == reset($value['goods']))
                      $return .= '<div class="product-item product-item-first">';
                    elseif ($good == end($value['goods']))
                      $return .= '<div class="product-item product-item-last">';                   
                    else
                      $return .= '<div class="product-item">';
                    
                      $return .= '<p>id: '.$good['product_id'].'</p>
                      <p>Название: '.$good['product_name'].'</p>
                      <p>Цена: '.$good['product_price'].'</p>
                      <p>Количество: '.$good['product_quantity'].'</p>
                    </div>';
                  }
                $return .= '</li>
                <li><b>Налог</b>: '.$row['tax'].'</li>
                <li><b>Доставка</b>: '.$row['delivery'].'</li>
                <li><b>Валюта</b>: '.$row['currency'].'</li>
              </ul>
            </div>
          </td>';
          
          $return .= '<td>'; if($value['utm_source'] != 'NULL') $return .= $value['utm_source']; $return .= '</td>';
          $return .= '<td>'; if($value['utm_medium'] != 'NULL') $return .= $value['utm_medium']; $return .= '</td>';
          $return .= '<td>'; if($value['utm_campaign'] != 'NULL') $return .= $value['utm_campaign']; $return .= '</td>';
          $return .= '<td>'; if($value['utm_term'] != 'NULL') $return .= $value['utm_term']; $return .= '</td>';
          $return .= '<td>'; if($value['utm_content'] != 'NULL') $return .= $value['utm_content']; $return .= '</td>';
          $return .= '<td>'; $return .= $value['type']; $return .= '</td>
        </tr>';

    }
      
      
    $return .= '</tbody>';

    echo json_encode($return);

    break;

/* Изменение значений полей в других целях */
case 'change-other-goals-fields':

    $db = new DB();
    $db->defaultConnect();

    $check_active_new = "true";
    $check_no_active_new = "true";
    $custom_string = "";

    // TODO: Убрать костыль с undefined
    if($_POST['active_new']){

        foreach ($_POST['active_new'] as $key => $value) {      
            
            $queryAdd = array();
            $value = str_replace('undefined', '', $value);
            $value = explode(',', $value);

            unset( $value[count($value)-1] );

            foreach ($value as $value2) {
                array_push($queryAdd, "`id` = ".$value2);
            }
            
            $queryAdd = implode(' OR ', $queryAdd);
            $queryString = "UPDATE `moiai_otherGoalsDescr` SET `".$key."` = 1 WHERE ".$queryAdd;
            $result = mysql_query($queryString);
            if(!$result) $check_active_new = "false";
            
        }

    }

    if($_POST['no_active_new']){

        foreach ($_POST['no_active_new'] as $key => $value) {      
            
            $queryAdd = array();
            $value = str_replace('undefined', '', $value);
            $value = explode(',', $value);

            unset( $value[count($value)-1] );

            foreach ($value as $value2) {
                array_push($queryAdd, "`id` = ".$value2);
            }
            
            $queryAdd = implode(' OR ', $queryAdd);
            $queryString = "UPDATE `moiai_otherGoalsDescr` SET `".$key."` = 0 WHERE ".$queryAdd;
            $result = mysql_query($queryString);
            if(!$result) $check_no_active_new = "false";
            
        }

    }

    if($check_active_new == "true" && $check_no_active_new == "true")
        echo json_encode("true");
    else
        echo json_encode("false");
   

    break;

/* Удаление других целей */
case 'delete-other-goals':
    
    $db = new DB();
    $db->defaultConnect();

    $deleteArr = array();

    foreach ($_POST['deleteFields'] as $value) {
        array_push($deleteArr, $value);
    }
    $queryAdd = implode(' OR `id`= ', $deleteArr);

    $query = mysql_query("DELETE FROM `moiai_otherGoalsDescr` WHERE `id`=".$queryAdd);

    if($query)
        echo json_encode(true);
    else
        echo json_encode(false);
    
    break;

/* Изменение заголовков в других целях */
case 'th-changed-other-goals':
    
    $db = new DB();
    $db->defaultConnect();

    $newVal = trim($_POST['newVal']);
    $oldVal = trim($_POST['oldVal']);

    if($newVal)
        $query = mysql_query("ALTER TABLE `moiai_otherGoalsDescr` CHANGE `".$oldVal."` `".$newVal."` BOOLEAN");
    else
        $query = mysql_query("ALTER TABLE `moiai_otherGoalsDescr` DROP `".$oldVal."`");
    
    if($query)
        echo json_encode(true);
    else
        echo json_encode(false);

    break;

/* Добавление нового поля в других целях */
case 'new-field-other-goals':
    
    $db = new DB();
    $db->defaultConnect();

    $query = mysql_query("ALTER TABLE `moiai_otherGoalsDescr` ADD `".$_POST['newField']."` BOOLEAN");
    
    if($query)
        echo json_encode(true);
    else
        echo json_encode(false);

    break;

default:
    break;
}

?>