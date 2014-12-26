<? 
  $activeModelsArray = array('check','db','htmlElements');
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Расшифровка id корзины</title>
    <?= $htmlElements->bootstrapFiles() ?>
    <?= $htmlElements->mainCssJsFiles() ?>   
    <script src="../files/js/jquery.inputmask.js" type="text/javascript"></script>
    <script type="text/javascript">check_constructror();</script>
  </head>
  <body>

    <div class="container">
    
    <?= $htmlElements->main_navbar('check'); ?> 

    <?
      $query = mysql_query("SELECT COUNT(*) FROM `moiai_utm` WHERE `section` LIKE 'main_section'");
      
        $result = mysql_fetch_array($query);
        $utm_section_count = $result[0];
    ?>

    <div class="check_basktet_id">
      <h2>Расшифровка id корзины</h2>
      <h4>id корзины</h4>
      <input  type="text" name="basket_id" class="decryption-basket-id utm_section_count_<?=$utm_section_count?>"><br/>
      <div class="btn btn-success do-decryption">Проверить</div><br/>
      <h4>Расшифровка</h4>
      <div class="decryption"></div>
    </div>
    
    </div>

  </body>
 </html> 