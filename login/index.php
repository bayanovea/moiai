<? include '../classes/module.php'; ?>

<?
  $htmlElements = new HtmlElements();
?>

<!DOCTYPE html>
<html lang="ru">
  
  <head>
    <meta charset="utf-8">
    <title>Логин</title>
    <?= $htmlElements->bootstrapFiles() ?>
    <?= $htmlElements->mainCssJsFiles() ?>   
    <script src="http://code.jquery.com/ui/1.9.1/jquery-ui.js"></script>
    <script>login_constructor();</script>
  </head>

  <body class="sign-in-body">

    <div class="container">

      <form class="form-signin" action="../main/" method="post">
        <h4 class="form-signin-heading">Введите Логин и Пароль:</h4>
        <input name="login" type="text" class="input-block-level" placeholder="Логин">
        <input name="password" type="password" class="input-block-level" placeholder="Пароль">
        <p class="alert alert-error">Неправильно введён Логин или Пароль</p>
        <label class="checkbox">
          <input type="checkbox" value="remember-me"> Запомнить меня
        </label>
        <input id="sign-in-button" class="btn btn-large btn-primary" type="button" value="Вход">
      </form>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!--<script src="../bootstrap/js/bootstrap-transition.js"></script>
    <script src="../bootstrap/js/bootstrap-alert.js"></script>
    <script src="../bootstrap/js/bootstrap-modal.js"></script>
    <script src="../bootstrap/js/bootstrap-dropdown.js"></script>
    <script src="../bootstrap/js/bootstrap-scrollspy.js"></script>
    <script src="../bootstrap/js/bootstrap-tab.js"></script>
    <script src="../bootstrap/js/bootstrap-tooltip.js"></script>
    <script src="../bootstrap/js/bootstrap-popover.js"></script>
    <script src="../bootstrap/js/bootstrap-button.js"></script>
    <script src="../bootstrap/js/bootstrap-collapse.js"></script>
    <script src="../bootstrap/js/bootstrap-carousel.js"></script>
    <script src="../bootstrap/js/bootstrap-typeahead.js"></script>-->

  </body>
</html>
