<?
  $activeModelsArray = array('add','db','htmlElements','config','report'); 
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Отчёт</title>
    <?= $htmlElements->bootstrapFiles() ?>
    <?= $htmlElements->mainCssJsFiles() ?>   
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script src="../files/js/jquery.tablesorter.min.js" type="text/javascript"></script>
    <script type="text/javascript">report_constructor();</script>
  </head>
  <body>

    <div class="container report-container">
    
    <?= $htmlElements->main_navbar('report'); ?>

      <? 
        $query = mysql_query("SELECT * FROM `moiai_orderGoals` ORDER BY `date` DESC LIMIT 1,40");
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
        //$order = getAllOrderGoals(40);    
      ?>

      <? 
        $utmSourceArray = array();
        $utmMediumArray = array();
        $utmCampaignArray = array();
        $utmTermArray = array();
        $utmContentArray = array();
        $utmTypeArray = array();

        if($order){
          foreach ($order as $id => $value){
            array_push($utmSourceArray, $value['utm_source']);
            array_push($utmMediumArray, $value['utm_medium']);
            array_push($utmCampaignArray, $value['utm_campaign']);
            array_push($utmTermArray, $value['utm_term']);
            array_push($utmContentArray, $value['utm_content']);
            array_push($utmTypeArray, $value['type']);
          }
        }

        $utmSourceArray = array_unique($utmSourceArray);
        $utmMediumArray = array_unique($utmMediumArray);
        $utmCampaignArray = array_unique($utmCampaignArray);
        $utmTermArray = array_unique($utmTermArray);
        $utmContentArray = array_unique($utmContentArray);
        $utmTypeArray = array_unique($utmTypeArray);

      ?>

      <div class="tab-content">

      <div id="utm-sections">

      <div class="filters">
  
        <h2>Фильтры</h2>

        <div class="filter-block filter-block-first">
          <p class="filter-block-head text-warning">Рекламная площадка</p>
          <select multiple="multiple" name="utm_source">
            <? foreach ($utmSourceArray as $key => $value): ?>
              <? if($value): ?>  
                <option><?=$value?></option>
              <? endif; ?>
            <? endforeach; ?>
          </select>
        </div>
        
        <div class="filter-block">
          <p class="filter-block-head text-warning">Тип рекламы</p>
          <select multiple="multiple" name="utm_medium">
            <? foreach ($utmMediumArray as $key => $value): ?>
              <? if($value): ?>
                <option><?=$value?></option>
              <? endif; ?>
            <? endforeach; ?>
          </select>
        </div>

        <div class="filter-block">
          <p class="filter-block-head text-warning">Название рекламной кампании</p>
          <select multiple="multiple" name="utm_campaign">
            <? foreach ($utmCampaignArray as $key => $value): ?>
              <? if($value): ?>
                <option><?=$value?></option>
              <? endif; ?>
            <? endforeach; ?>
          </select>
        </div>

        <div class="filter-block filter-block-date">
          <p class="filter-block-head text-warning">Дата</p>
          <label for="from">C</label>
          <input type="text" id="from" name="from">
          <label for="to">По</label>
          <input type="text" id="to" name="to">
        </div>

        <div class="clear"></div>

        <div class="filter-block filter-block-first">
          <p class="filter-block-head text-warning">Ключевая фраза</p>
          <select multiple="multiple" name="utm_term">
            <? foreach ($utmTermArray as $key => $value): ?>
              <? if($value): ?>
                <option><?=$value?></option>
              <? endif; ?>
            <? endforeach; ?>
          </select>
        </div>

        <div class="filter-block">
          <p class="filter-block-head text-warning">Контейнер для доп. информации</p>
          <select multiple="multiple" name="utm_content">
            <? foreach ($utmContentArray as $key => $value): ?>
              <? if($value): ?>
                <option><?=$value?></option>
              <? endif; ?>  
            <? endforeach; ?>
          </select>
        </div>

        <div class="filter-block">
          <p class="filter-block-head text-warning">Тип</p>
          <select multiple="multiple" name="type">
            <? foreach ($utmTypeArray as $key => $value): ?>
              <? if($value): ?>
                <option><?=$value?></option>
              <? endif; ?>
            <? endforeach; ?>
          </select>
        </div>

        <div class="filter-block filter-block-price">
          <p class="filter-block-head text-warning">Цена</p>
            <label for="price_from">От</label>
            <input type="text" id="price_from" name="price_from">
            <label for="price_to">До</label>
            <input type="text" id="price_to" name="price_to">
        </div>
        
        <div class="clear"></div>

        <div class="btn btn-success filter-apply">Применить</button>

      </div>

      <h2>Отчёт</h2>

      <div class="report-pagination-top"> 
        <?= $htmlElements->buildPagination(40); ?>
      </div>

      <table class="table orderGoals">
        
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
            <th>Доп. информации</th>
            <th>Тип</th>
          </tr>
        </thed>
      
      <tbody>
      <? foreach ($order as $id => $value): ?>
        
        <tr>
          <td><?=$value['id']?></td>
          <td><?=$value['order_id']?></td>
          <td><span class="posix-time" style="display: none;"><?=$value['date']?></span><?= date('d.m.y', $value['date']) ?></td>
          <td>
            <img src="http://www.iconsearch.ru/uploads/icons/bnw/48x48/add.png" width="24" class="open-order">
            <span class="general-summ">Общая сумма: <b><?=$value['price']?></b></span>
            <div class="order-composition">
              <ul>
                <li>
                  <b>Состав</b>: <br/>
                  <? foreach ($value['goods'] as $key => $good): ?>
                    <? if ($good == reset($value['goods'])): ?>
                      <div class="product-item product-item-first">
                    <? elseif ($good == end($value['goods'])): ?>
                      <div class="product-item product-item-last">                     
                    <? else: ?>
                      <div class="product-item">
                    <? endif; ?>
                      <p>id: <?=$good['product_id']?></p>
                      <p>Название: <?=$good['product_name']?></p>
                      <p>Цена: <?=$good['product_price']?></p>
                      <p>Количество: <?=$good['product_quantity']?></p>
                    </div>
                  <? endforeach; ?>
                </li>
                <li><b>Налог</b>: <?=$row['tax']?></li>
                <li><b>Доставка</b>: <?=$row['delivery']?></li>
                <li><b>Валюта</b>: <?=$row['currency']?></li>
              </ul>
            </div>
          </td>
          <td><? if($value['utm_source'] != 'NULL') echo $value['utm_source']; ?></td>
          <td><? if($value['utm_medium'] != 'NULL') echo $value['utm_medium']; ?></td>
          <td><? if($value['utm_campaign'] != 'NULL') echo $value['utm_campaign']; ?></td>
          <td><? if($value['utm_term'] != 'NULL') echo $value['utm_term']; ?></td>
          <td><? if($value['utm_content'] != 'NULL') echo $value['utm_content']; ?></td>
          <td><? echo $value['type']; ?></td>
        </tr>
      
      <? endforeach; ?>
      </tbody>
          
      <? mysql_free_result($query); ?>
        
      </table>

      <div class="report-pagination-bottom"> 
        <?= $htmlElements->buildPagination(40); ?>
      </div>

    </div>

  </div>

    <div id="otherGoals"> 
        <h2>Другие цели</h2>

        <?
          $queryFieldsOtherGoals = mysql_query("SHOW COLUMNS FROM `moiai_otherGoals`");
          $queryOtherGoals = mysql_query("SELECT * FROM `moiai_otherGoals`"); 
        ?>
        
        <table class="table otherGoalsTable">
          <thead>
            <tr>
              <? while ($row = mysql_fetch_array($queryFieldsOtherGoals, MYSQL_ASSOC)): ?>
                <th><?=$row['Field'];?></th>
              <? endwhile; ?>
            </tr>
          </thead>
          <tbody>
          <? while ($row = mysql_fetch_array($queryOtherGoals, MYSQL_ASSOC)): ?>
            <tr>
              <? foreach ($row as $key => $value): ?>
                <td><?=$value?></td>
              <? endforeach; ?>
            </tr>
          <? endwhile; ?>
          </tbody>  
        </table>
  
        

    </div>

      </div>

    </div>
  </body>
 </html> 