function parseGetParams() { 
  var $_GET = {}; 
  var __GET = window.location.search.substring(1).split("&"); 
  for(var i=0; i<__GET.length; i++) { 
     var getVar = __GET[i].split("="); 
     $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
  } 
  return $_GET; 
}

function setBasketId() {        
  var basket_id;
  var getParams = parseGetParams();
          
  $.ajax({
    type: 'POST',
    url: '/ajax/index.php',
    data: {
      type: 'set-basket-id',
      getParams: getParams
    },
    dataType: 'json',
    cache: false,
    success: function(data) {
      console.log(data);
      $('#basket-id').text(data);
    },
  });
}

jQuery(document).ready(function(){

  //var __utmz = '126394024.1260524913.5.5.utmcsr=yandex|utmccn=(cpc)|utmcmd=organic|utmctr=best';
  //if()
  //setcookie('__utmz', __utmz);

  $_GET = parseGetParams();
  console.log($_GET['utm_source']);

  // Устанавливаем id корзины
  setBasketId();

  // Совершение заказа
  jQuery('.make-order').click(function(){

    var orderParams = {
      order_id: "125",
      order_price: 2400, 
      currency: "RUR",
      goods: 
        [
          {
            id: "1", 
            name: "наименование товара", 
            price: 300,
            quantity: 1
          },               
          {
            id: "2", 
            name: "наименование товара 2", 
            price: 200,
            quantity: 3
          },
          {
            id: "45", 
            name: "наименование товара 45", 
            price: 500,
            quantity: 3
          }  
        ],
    };

    jQuery.ajax({
      type: 'POST',
      url: '/ajax/index.php',
      data: {
        orderParams: orderParams,
        type: 'make-order'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        if(data == 'true'){
          alert('Заказ успешно совершён');
          window.location = '/';
        }          
      },
    });

  }); 

})      