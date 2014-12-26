<? 
  $activeModelsArray = array('new-words','db','config','htmlElements');
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
	
	<head>
	    <meta charset="utf-8">
	    <title>Добавить новые ключевые слова</title>
	    <?= $htmlElements->bootstrapFiles() ?>
      <?= $htmlElements->mainCssJsFiles() ?>   
	
	    <script type="text/javascript">new_words_constructor();</script>
	</head>

	<body>
		<div class="container">

			<?= $htmlElements->main_navbar('new-words'); ?>

			<? $allNewWords = $newWords->getAllNewWords(); ?>

      		<table class="new-words-table table">
      			<tr>
      				<th>Название</th>
      				<th>Альтер-ое название</th>
      				<th>Описание</th>
      				<th>Раздел</th>
      				<th>Приоритет</th>
      				<th>Действие</th>
      			</tr>
      			<? foreach ($allNewWords as $row): ?>
      				<tr>
      					<td><input type="text" name="name" value="<?=$row['name']?>" disabled="disabled"> </td>
      					<td><input type="text" name="alt_name"></td>
      					<td><input type="text" name="rus_name"></td>
      					<td><input type="text" name="section" value="<?=$row['section']?>" disabled="disabled"></td>
      					<td><input type="text" name="priority" class="priority" value="0"></td>
      					<td>
      						<input type="hidden" name="id" value="<?=$row['id']?>">
      						<div class="btn btn-success confirm-new-word">Подтвердить</div>
      						<div class="btn btn-danger reject-new-word">Отклонить</div>
      					</td>
      				</tr>
      			<? endforeach; ?>
      		</table>

		</div>
	</body>
</html>

