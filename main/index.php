<? include '../classes/module.php'; ?>

<?
  $users = new Users();
  $config = new Config();
  $htmlElements = new HtmlElements();
?> 

<!DOCTYPE html>
<html lang="ru">
  
  <head>
    <meta charset="utf-8">
    <title>Главная страница модуля</title>
    <?= $htmlElements->bootstrapFiles() ?>
    <?= $htmlElements->mainCssJsFiles() ?>   
  </head>

  <body>

  	 <div class="container">
  	 	
  	 	<div class="variant-choice">

  	 		<h3 class="muted">Выберите действие:</h3>

        <div>
          <a href="../add/index.php"><img src="../files/images/add.png"></a>
          <a href="../add/index.php">Добавить</a>
        </div>

  	 		<div>
  	 			<a href="../report/"><img src="../files/images/bd.jpg"></a>
  	 			<a href="../report/">Отчёт</a>
  	 		</div>

        <div>
          <a href="../graphics/"><img src="../files/images/graphics.png"></a>
          <a href="../graphics/">Визуализация</a>
        </div>

        <div>
          <a href="../download/"><img src="../files/images/download.png"></a>
          <a href="../download/">Скачать</a>
        </div>
  	 		
  	 		<div>
  	 			<a href="../check/"><img src="../files/images/loop.png"></a>
  	 			<a href="../check/">Проверка</a>
  	 		</div>

        <div>
          <a href="../new-words/"><img src="../files/images/new-words.png"></a>
          <a href="../new-words/">Новые слова</a>
        </div>

        <div>
          <a href="../settings/"><img src="../files/images/setting.png"></a>
          <a href="../settings/">Настройка</a>
        </div>

  		 </div>

  		 <div class="clearfix"></div>
  	 	
  	 </div>
  </body>
 </html> 