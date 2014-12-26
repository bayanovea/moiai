<?php

/**
 * Класс конструирования HTML-элементов
 */
class HtmlElements implements IHtmlElements
{

    /**
     * Подключение стандартных файлов библиотеки bootstrap
     * @return string
     */
    public function bootstrapFiles(){
        $return = '
        <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
        <link href="../bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
        <script src="../bootstrap/js/jquery.js"></script>';

        return $return;
    }

    /**
     * Подключение основных CSS и JS файлов модуля
     * @return string
     */
    public function mainCssJsFiles(){
        $return = '
        <link href="../files/css/main.css" rel="stylesheet">
        <script src="../files/js/main.moiai_module.js"></script>';

        return $return;
    }

    /**
     * Подключение стандартных файлов библиотеки flot (построение графиков) 
     * @return string
     */
    public function flotFiles(){
        $return = '
        <script language="javascript" type="text/javascript" src="../files/js/graphics.moiai_module.js"></script>      
        <script language="javascript" type="text/javascript" src="../flot/jquery.flot.js"></script>
        <script language="javascript" type="text/javascript" src="../flot/jquery.flot.pie.js"></script>
        <script language="javascript" type="text/javascript" src="../flot/jquery.flot.selection.js"></script>
        <script language="javascript" type="text/javascript" src="../flot/jquery.flot.time.js"></script>
        <script language="javascript" type="text/javascript" src="../flot/jquery.flot.categories.js"></script>';

        return $return;
    }

    /**
     * Построение начальных графиков для страницы /graphics
     * @return string
     */
    public function buildStartGraphics($from = false, $to = false){
        $return = '<script type="text/javascript">
            
            jQuery(document).ready(function(){';
            
            // Подключение к БД
            $db = new DB();    
            $db->defaultConnect();

            $from = strtotime( str_replace('.', '/', $from) );
            $to = strtotime( str_replace('.', '/', $to) );

            // дата
            if($from && $to)
              $query = mysql_query("SELECT * FROM `moiai_orderGoods` JOIN `moiai_orderGoals` WHERE `moiai_orderGoals`.`order_id` = `moiai_orderGoods`.`order_id` AND `moiai_orderGoals`.`date` >= ".$from." AND `moiai_orderGoals`.`date` <= ".$to);
            elseif($from)
              $query = mysql_query("SELECT * FROM `moiai_orderGoods` JOIN `moiai_orderGoals` WHERE `moiai_orderGoals`.`order_id` = `moiai_orderGoods`.`order_id` AND `moiai_orderGoals`.`date` >= ".$from); 
            elseif($to)
              $query = mysql_query("SELECT * FROM `moiai_orderGoods` JOIN `moiai_orderGoals` WHERE `moiai_orderGoals`.`order_id` = `moiai_orderGoods`.`order_id` AND `moiai_orderGoals`.`date` <= ".$to); 
            else
              $query = mysql_query("SELECT * FROM `moiai_orderGoods` JOIN `moiai_orderGoals` WHERE `moiai_orderGoals`.`order_id` = `moiai_orderGoods`.`order_id`"); 

            // Формируем массив с заказами для левого графика
            while ($row = mysql_fetch_array($query, MYSQL_ASSOC)){
               
                foreach ($row as $key => $value) {
                  
                    if( $key=='product_id' || $key=='product_name' || $key=='product_price' || $key=='product_quantity')
                      $order[$row['id']]['goods'][$row['id2']][$key] = $value;
                    else
                      $order[$row['id']][$key] = $value;
                }

            }

            /* Диаграмма */

            // Задаём utm-секции, по которым будем строить графики
            $utm_sections = Array(
                'utm_source' => Array(),
                'utm_medium' => Array(),
                'utm_campaign' => Array(),
                'utm_term' => Array(),
                'utm_content' => Array(),
            );

            // Получаем массив со значениями этих секций
            foreach ($order as $key => $value) {
                        
                foreach ($utm_sections as $section_key => $section_value) {
                    if ( array_key_exists($value[$section_key], $utm_sections[$section_key] ) ){
                        $utm_sections[$section_key][$value[$section_key]]['main_count']++; 
                            if ( array_key_exists($value['date'], $utm_sections[$section_key][$value[$section_key]] ) )
                                $utm_sections[$section_key][$value[$section_key]][$value['date']]++;
                            else
                                $utm_sections[$section_key][$value[$section_key]][$value['date']] = 1;
                    }
                    else{
                        $utm_sections[$section_key][$value[$section_key]]['main_count'] = 1;
                        $utm_sections[$section_key][$value[$section_key]][$value['date']] = 1;
                    }
                }
                        
            }

            $return .= 'diagramData = []; ';
            $return .= 'diagramDataset = []; ';

                foreach ($utm_sections as $section_key => $section_value){

                    // diagramData
                    $return .= ' diagramData["'.$section_key.'"] = [';
                        foreach ($section_value as $key => $value){
                            if($key){
                                $return .= '["'.$key.'", "'.$value['main_count'].'" ],';
                            }
                        }
                    $return .= ']; ';

                    //print_r($section_value);

                    // diagramDataset
                    $return .= 'diagramDataset["'.$section_key.'"] = {';
                      foreach ($section_value as $label => $value_array){
                        $return .= '"'.$label.'": { label: "'.$label.'", data: [';
                          
                          $timeSegments = array();
                          foreach ($value_array as $time => $value){
                            if( $time != 'main_count' ){
                              $time = round($time, -6);
                              if ( !array_key_exists("$time", $timeSegments) )
                                $timeSegments["$time"] = 1;
                              else{
                                $timeSegments["$time"]++; 
                              }
                            }
                          }

                          ksort($timeSegments);

                          foreach ($timeSegments as $time => $value){
                            $time = $time*1000;
                            $return .= '['.$time.', '.$value.'],';
                          }

                        $return .= ']},'; 
                      }
                    $return .= '}; ';

                    $return .= 'showDiagramPie(diagramData["'.$section_key.'"], "placeholder_'.$section_key.'"); ';
                    $return .= 'showDiagramLinked(diagramDataset["'.$section_key.'"], "'.$section_key.'");';

               }

            $return .= '}); </script>';

        return $return;
    }

    /**
     * Построение главного навигационного меню 
     * @param string $active активнвый пункт меню
     * @return string
     */
    public function main_navbar($active){
        
        $navbar_item = Array(
            "main" => "Главная",
            "add" => "Добавить",
            "report" => "Отчёт",
            "graphics" => "Визуализация",
            "download" => "Скачать",
            "check" => "Проверка",
            "new-words" => "Новые слова",
            "settings" => "Настройка"
        );

        $navbar = '<ul class="nav nav-tabs main_navbar">';
        foreach ($navbar_item as $key => $value) {
            if($key == $active)
                $navbar .= '<li class="active"><a href="../'.$key.'/">'.$value.'</a></li>';
            else
                $navbar .= '<li><a href="../'.$key.'/">'.$value.'</a></li>';
        }
        $navbar .= '</ul>';

        return $navbar;

    }

    /**
     * Построение навигацинного меню настроек 
     * @return string
     */
    public function settings_navbar(){
        $return = '
        <div class="navbar">
         <div class="navbar-inner">
            <a class="brand" href="#">Настройки</a>
            <ul class="nav">
                <li class="active"><a data-toggle="tab" href="#utm-sections">Настройки меток</a></li>
                <li><a data-toggle="tab" href="#otherGoals">Дополнительные цели</a></li>
                <li><a data-toggle="tab" href="#connection">Подключение</a></li>
            </ul>
        </div>
        </div>
        ';

        return $return;
    }

   /**
     * Построение навигацинного меню настроек 
     * @return string
     */
    public function report_navbar(){
        $return = '
        <div class="navbar">
         <div class="navbar-inner">
            <a class="brand" href="#"></a>
            <ul class="nav">
                <li class="active"><a data-toggle="tab" href="#orders">Заказы</a></li>
                <li><a data-toggle="tab" href="#otherGoals">Другие цели</a></li>
            </ul>
        </div>
        </div>
        ';

        return $return;
    } 

  /**
   * Паджинация в отчётах
   * @param int $num количество записей на странице
   * @return string
   */
  public function buildPagination($num, $addQuery = false){
    $db = new DB();    
    $db->defaultConnect();

    // Общее число записей
    if($addQuery)
      $result = mysql_query("SELECT COUNT(*) FROM `moiai_orderGoals` WHERE ".$addQuery);
    else
      $result = mysql_query("SELECT COUNT(*) FROM `moiai_orderGoals`");

    $posts = mysql_result($result, 0);

    // Находим общее число страниц  
    $total = intval(($posts - 1) / $num) + 1;  

    $return = "<p>Страница:</p>";

    $return .= '<div class="pagination-page pagination-page-active">1</div>';

    if($total > 10){
      
      for ($i=2; $i < 5; $i++) { 
        $return .= '<div class="pagination-page">'.$i.'</div>';
      }
      $return .= '<span class="pagination-dots">...</span>';
      for ($i=$total-3; $i < $total+1; $i++) { 
        $return .= '<div class="pagination-page">'.$i.'</div>';
      }

    }
    else{
      for ($i=2; $i < $total+1; $i++) { 
        $return .= '<div class="pagination-page">'.$i.'</div>';
      }
    }

    $return .= '<div class="clear"></div>';

    return $return;
  }

}

?>