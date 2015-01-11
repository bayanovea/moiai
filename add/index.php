<? 
  $activeModelsArray = array('add','db','htmlElements');
  include '../classes/module.php'; 
?>

<!DOCTYPE html>
<html lang="ru">
  	<head>
	    <meta charset="utf-8">
	    <title>Добавить</title>

	    <?= $htmlElements->bootstrapFiles() ?>
    	<?= $htmlElements->mainCssJsFiles() ?>

    	<script type="text/javascript">add_constructor();</script>  

  	</head>

 	<body>

     	<div class="container">

     		<?= $htmlElements->main_navbar('add'); ?>

     		<h1>Добавить новый заказ</h1>

            <div class="add-new-order">
            	<input name="tax" type="text" placeholder="Налог"><br/>
            	<input name="delivery" type="text" placeholder="Доставка"><br/>

            	<div class="add-new-order-item">
            		<p>Товар №<span class="order-number">1</span></p>
            		<input name="product_id" type="text" placeholder="id товара"><br/>
            		<input name="product_name" type="text" placeholder="Название товара"><br/>
            		<input name="product_price" type="text" placeholder="Цена"><br/>
            		<input name="product_quantity" type="text" placeholder="Количество"><br/>
            	</div>

            	<div class="btn btn-primary add-product-fields">Ещё товары</div><br/>

            	<input name="basket-id" type="text" placeholder="id корзины"><br/>
            	<span>Общая сумма: </span> <span class="main-price">0</span> Руб.<br/>

            	<div class="btn btn-success do-order">Оформить</div>

            </div>

     	</div>
    </body>
</html>