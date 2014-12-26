<? 
  $activeModelsArray = array('settings','db','config','htmlElements');
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <title>Настройки</title>
    <?= $htmlElements->bootstrapFiles() ?>
    <?= $htmlElements->mainCssJsFiles() ?>   
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script src="../files/js/jquery.tablesorter.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">

    <script type="text/javascript">settings_contructor();</script>

  </head>
  <body>

     <div class="container settings-container">

        <?= $htmlElements->main_navbar('settings'); ?>
        <?= $htmlElements->settings_navbar(); ?>

        <? $utm_section = $settings->getUtmSection(); ?>
        
        <div class="tab-content">

        <div id="utm-sections" class="tab-pane active">  

          <h2>Настройка меток</h2>
  
          <table class="table utm-tag-setting-table <? if($_GET['edit_mode'] == 'true') echo 'edit-mode'; ?>">
            
             <tr class="text-info">
                <th>id</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Отображать</th>
                <th>Приоритет</th>
                <th>Отметить</th>
              </tr>
              <tr><td colspan="6"></td></tr>
  
            <? foreach ($utm_section as $name => $section): ?>
              
              <? if($_GET['edit_mode'] == 'true'): ?>
  
              <? if($section['id']): ?>
                <tr class="utm-section">
                  <td class="id"><?=$section['id']?></td>
                  <td class="name"><?=$name?></td>
                  <td><?=$section['rus_name']?></td>
                  <td><?=$section['show']?></td>
                  <td><?=$section['priority']?></td>
                  <td></td>
                </tr>
              <? endif; ?>
  
              <? if($section['SUBSECTION']): ?>
                <? foreach ($section['SUBSECTION'] as $subsection): ?>
                  <tr class="utm-subsection subsection-for-<?=$subsection['section']?>">
                    <td class="id"><?=$subsection['id']?></td>
                    <td class="name"><input type="text" class="input-xlarge" value="<?=$subsection['name']?>"/></td>
                    <td><input type="text" class="input-xlarge" value="<?=$subsection['rus_name']?>"/></td>
                    <td><input type="text" class="input-xlarge" value="<?=$subsection['show']?>"/></td>
                    <td><input type="text" class="input-xlarge" value="<?=$subsection['priority']?>"/></td>
                    <td><input type="checkbox"/></td>
                  </tr>
                <? endforeach; ?>
              <? endif; ?>
            
            <? else: ?>
              
              <? if($section['id']): ?>
                <tr class="utm-section">
                  <td class="id"><?=$section['id']?></td>
                  <td class="name"><?=$name?></td>
                  <td><?=$section['rus_name']?></td>
                  <td><?=$section['show']?></td>
                  <td><?=$section['priority']?></td>
                  <td></td>
                </tr>
              <? endif; ?>
  
              <? if($section['SUBSECTION']): ?>
                <? foreach ($section['SUBSECTION'] as $subsection): ?>
                  <tr class="utm-subsection subsection-for-<?=$subsection['section']?>">
                    <td class="id"><?=$subsection['id']?></td>
                    <td class="name"><?=$subsection['name']?></td>
                    <td><?=$subsection['rus_name']?></td>
                    <td><?=$subsection['show']?></td>
                    <td><?=$subsection['priority']?></td>
                    <td><input type="checkbox"/></td>
                  </tr>
                <? endforeach; ?>
              <? endif; ?>
  
              <? endif; ?>
  
            <? endforeach; ?>
  
            <tr class="add-utm-section">
              <td></td>
              <td><input type="text" name="name"></td>
              <td><input type="text" name="rus_name"></td>
              <td class="show">Отображать <input type="checkbox" name="show"></td>
              <td><input type="text" name="priority"></td>
              <td><select class="utm-section-select"></select></td>
            </tr>  
  
            <tr class="add-utm-section-buttons">
              <td colspan="6"><input class="btn btn-success" id="utm-tag-dynamic-add" value="Добавить"/></td>
            </tr>
  
          </table>
  
          <div class="utm-tag-setting-buttons">
            <? if($_GET['test_mode'] == 'true'): ?>
              <a href="?edit_mode=none"><div class="btn btn-warning" id="utm-tag-edit">Редактировать</div></a>
              <div class="btn btn-success" id="utm-tag-edit-save">Сохранить</div>
            <? else: ?>
              <a href="?edit_mode=true"><div class="btn btn-warning" id="utm-tag-edit">Редактировать</div></a>
              <div class="btn btn-success" id="utm-tag-add">Добавить</div>
              <div class="btn btn-danger" id="utm-tag-delete">Удалить отмеченные</div>
            <? endif; ?>
          </div>

        </div>

        <div id="otherGoals" class="tab-pane"> 

          <h2>Цели</h2>

          <? $queryHeads = $settings->getOtherGoalsQueryHeads(); ?>

          <table class="table otherGoalsSettings">
            <tr>
              <? foreach ($queryHeads as $row): ?>
                <th> <?=$row['Field']?> </th>
              <? endforeach; ?>
              <th class="mark-th">Отметить</th>
            </tr>

            <? foreach ($queryHeads as $row): ?>
              <tr>
               <? foreach ($row as $key => $value): ?>
                  <? if($key=='id' || $key=='type' || $key=='description'): ?>
                    <td class="<?=$key?>"><?=$value?></td>
                  <? else: ?>
                    <? if($value == 1): ?>
                      <td class="<?=$key?> active"></td>
                    <? else: ?>
                      <td class="<?=$key?> no-active"></td>
                    <? endif; ?>
                  <? endif; ?>
                <? endforeach; ?>
                <td class="mark-td"><input type="checkbox" class="mark"></td>
              </tr>
            <? endforeach; ?>
            
          </table>
          
          <div class="otherGoalsSettingsButtons">
            <div class="btn btn-success otherGoalsSettingsSave">Сохранить</div>
            <div class="btn btn-danger otherGoalsSettingsDelete">Удалить</div>
          </div>

          <div class="otherGoalsNewFields">
            <h2>Добавить новые поля</h2>
            <label>Название (на английском)</label>
            <input type="text" name="name"><br/>
            <div class="btn btn-success">Добавить</div>
          </div>

        </div>

        <div id="connection" class="tab-pane">
          
          <h2>Настройки подключения</h2>

          <? $ini_array = $config->getIniConfig('../config.ini'); ?>
          
          <? foreach ($ini_array as $section_key => $section_value): ?>
            <hr/>
            <div>
              <? echo '<h4>'.$section_key.'</h4>'; ?>
              <form class="<?=$section_key?>">
              <? foreach ($section_value as $key => $value): ?>
                <span><?=$key?></span>
                <input type="text" name="<?=$key?>" value="<?=$value?>">
              <? endforeach; ?> 
              </form> 
            </div>
          <? endforeach; ?>
        
          <hr/>

          <div class="connection-buttons">
            <div class="btn btn-success">Сохранить</div>
          </div>

        </div>

      </div>

     </div>
  </body>
 </html> 