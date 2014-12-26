<? 
  $activeModelsArray = array('db','htmlElements');
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
  	<head>
	    <meta charset="utf-8">
	    <title>Визуализация</title>
	    <?= $htmlElements->bootstrapFiles() ?>
    	<?= $htmlElements->mainCssJsFiles() ?>
    	<?= $htmlElements->flotFiles() ?>
    	<?= $htmlElements->buildStartGraphics($_GET['from'], $_GET['to']) ?>

    	<script language="javascript" type="text/javascript" src="../flot/jquery.flot.time.js"></script>
		<script language="javascript" type="text/javascript" src="../files/js/date.js"></script>   

		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

		<script type="text/javascript">graphics_constructror();</script>

	</head>
	
	<body>

		<div class="container graphic-container">

			<?= $htmlElements->main_navbar('graphics'); ?> 

			<h1 class="graphics-h1">Визуализация данных</h1>

			<div class="graphics-date">
          		<div>
          			<label for="from">C</label>
          			<input type="text" id="from" name="from">
          		</div>
          		<div>
          			<label for="to">По</label>
          			<input type="text" id="to" name="to">
          		</div>
          		<img src="/moiai_module/files/images/browser-go.png">
        	</div>

        	<div class="clear"></div>

			<?
				$query = mysql_query("SELECT * FROM `moiai_utm` WHERE `section` = 'main_section'");

				while ($row = mysql_fetch_array($query, MYSQL_ASSOC)): ?>

					<div class="graphic-block">

						<div class="graphic-block-header">
							<p class="graphic-block-head"><?=$row['rus_name']?></p>
							<i class="icon-chevron icon-chevron-down"></i>
						</div>

						<div class="graphic-block-body">

							<div class="graphicLeft">

								<div class="graphicPlaceholder placeholder_<?=$row['name']?>"></div>
								<button type="button" class="btn graphic-type">
									<i class="icon-align-justify"></i>
								</button>
								<select class="select-type">
									<option value="pie">Круговая</option>
									<option value="column">Столбики</option>
								</select>
							</div>

							<div class="graphicRight">
								<div class="graphicPlaceholderRight placeholder_right_<?=$row['name']?>"></div>
								<p class="choices choices_<?=$row['name']?>"></p>
							</div>

						</div>

					</div>

					<div class="clear"></div>

				<? endwhile; ?>

		</div>
	</body>
</html>