/* LOGIN */

function login_constructor(){
  jQuery(document).ready(function(){

    // Отправка формы логина
    $('.form-signin #sign-in-button').click(function() {

      $.ajax({
        type: 'POST',
        url: '../ajax/index.php',
        data: {
          module_login: $('.form-signin input[name="login"]').val(), 
          module_password: $('.form-signin input[name="password"]').val(), 
          type:'sign-in-check'
        },
        dataType: 'json',
        cache: false,
        success: function(data) {
          console.log(data);
          if(data != "false"){
            setCookie('admin_check', data, { expires: 7200 });
            window.location = "../main/";
          }
          if(data == "false"){
            if($('.form-signin .alert-error').css('display') == 'none')
              $('.form-signin .alert-error').show('600');
            else
              $('.form-signin .alert-error').effect( "shake" );
          }
        },
      });

      return false;
      
      });

  })
}

/* REPORT */

function report_constructor(){

  jQuery(document).ready(function(){
    
    // Подклюаем сортировку таблицы
    jQuery(".orderGoals").tablesorter(); 
    jQuery(".otherGoalsTable").tablesorter();

    // Плавное открытие состава заказа
    $('.open-order').click(function(){ console.log('click');

    var order_composition = $(this).siblings('.order-composition');

    if( order_composition.css('display') == 'none' )
      order_composition.show('400');
    else
      order_composition.hide('400');
    });

    // Применение фильтров
    jQuery('.filter-apply').click(function(){

      var filtersArr = new Array('utm_source','utm_medium','utm_campaign','utm_term','utm_content','goal_type');
     
      jQuery('.filter-block select option').removeClass('active');
      
      // Проставляем активный класс
      for (var i in filtersArr) {
        jQuery('.filter-block select[name="'+filtersArr[i]+'"] option:selected').addClass('active');
      }

      jQuery.ajax({
        type: 'POST',
        url: '../ajax/index.php',
        data: {
          utm_source: jQuery('.filter-block select[name="utm_source"]').val(),
          utm_medium: jQuery('.filter-block select[name="utm_medium"]').val(),
          utm_campaign: jQuery('.filter-block select[name="utm_campaign"]').val(),
          utm_term: jQuery('.filter-block select[name="utm_term"]').val(),
          utm_content: jQuery('.filter-block select[name="utm_content"]').val(),
          goal_type: jQuery('.filter-block select[name="goal_type"]').val(),
          from: jQuery('.filter-block input#from').val(),
          to: jQuery('.filter-block input#to').val(),
          price_from: jQuery('.filter-block input#price_from').val(),
          price_to: jQuery('.filter-block input#price_to').val(),
          type:'filter-apply'
        },
        dataType: 'json',
        cache: false,
        success: function(data) {
          // Работа с таблицей
          jQuery('table.orderGoals').animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "slow");
          jQuery('table.orderGoals').html(data[0]);
          jQuery('table.orderGoals').animate({ backgroundColor: "rgba(21, 139, 21, 0.35);" }, "fast").animate({ opacity: "show" }, "slow");
          jQuery('table.orderGoals').animate({ backgroundColor: "white" }, "normal");
          jQuery(".orderGoals").tablesorter();
          // Работа с паджинацией
          jQuery('.report-pagination-top').html(data[1]);
          jQuery('.report-pagination-bottom').html(data[1]);
        },
      });

    });

  // Календарь
  jQuery( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    jQuery( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 3,
      onClose: function( selectedDate ) {
        jQuery( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });

  })

  // Паджинация
  jQuery('.pagination-page').live("click", function(){

    if( jQuery(this).hasClass('pagination-page-active') )
        return false;

    var _this = jQuery(this);

    var filtersArr = new Array('utm_source','utm_medium','utm_campaign','utm_term','utm_content','goal_type');
    var filtersSelect = new Array();

    // Передаём выбранные option
    for (var i in filtersArr) {
      filtersSelect[filtersArr[i]] = new Array();
      jQuery('.filter-block select[name="'+filtersArr[i]+'"] option.active').each(function(){
        filtersSelect[filtersArr[i]].push(jQuery(this).val());
      });
    }

    jQuery.ajax({
        type: 'POST',
        url: '../ajax/index.php',
        data: {
          page: _this.text(),
          utm_source: filtersSelect['utm_source'],
          utm_medium: filtersSelect['utm_medium'],
          utm_campaign: filtersSelect['utm_campaign'],
          utm_term: filtersSelect['utm_term'],
          utm_content: filtersSelect['utm_content'],
          goal_type: filtersSelect['goal_type'],
          from: jQuery('.filter-block input#from').val(),
          to: jQuery('.filter-block input#to').val(),
          price_from: jQuery('.filter-block input#price_from').val(),
          price_to: jQuery('.filter-block input#price_to').val(),
          type: 'pagination-page-click'
        },
        dataType: 'json',
        cache: false,
        success: function(data) {

          // Обновление пагинации
          var last = parseInt(jQuery('.pagination-page').last().text());
          var curPos = parseInt(_this.text());
          
          if(last > 10){
            
            var html = '<p>Страница:</p>'; 

            if ( curPos < 9 ){
                           
              for (var i = 1; i <= curPos + 2; i++) {
                if (i == curPos)
                  html = html + '<div class="pagination-page pagination-page-active">'+i+'</div>';
                else
                  html = html + '<div class="pagination-page">'+i+'</div>';
              };

              html = html + '<span class="pagination-dots">...</span>';
              
              for (var i = last-2; i <= last+1; i++) {
                html = html + '<div class="pagination-page">'+i+'</div>';
              };

            }

            if( curPos > last - 7 ){
              
              for (var i = 1; i <= 5; i++) {
                html = html + '<div class="pagination-page">'+i+'</div>';
              };

              html = html + '<span class="pagination-dots">...</span>';

                for (var i = curPos-2; i <= last; i++) {
                  if (i == curPos)
                    html = html + '<div class="pagination-page pagination-page-active">'+i+'</div>';
                  else
                    html = html + '<div class="pagination-page">'+i+'</div>';
                };

            }

            if( curPos >= 9 && curPos <= last - 7 ){

              for (var i = 1; i <= 5; i++) {
                html = html + '<div class="pagination-page">'+i+'</div>';
              };

              html = html + '<span class="pagination-dots">...</span>';

              for (var i = curPos-2; i <= curPos+3; i++) {
                if (i == curPos)
                  html = html + '<div class="pagination-page pagination-page-active">'+i+'</div>';
                else
                  html = html + '<div class="pagination-page">'+i+'</div>'
              };

              html = html + '<span class="pagination-dots">...</span>';

              for (var i = last-2; i <= last; i++) {
                html = html + '<div class="pagination-page">'+i+'</div>';
              };

            }

            html = html + '<div class="clear"></div>';

            jQuery('.report-pagination-top').html(html);
            jQuery('.report-pagination-bottom').html(html);

          }
          else{
            jQuery('.pagination-page').removeClass('pagination-page-active');
            _this.addClass('pagination-page-active');
          }

          // Обновление страницы
          jQuery('table.orderGoals').hide('600');
          jQuery('table.orderGoals').html(data);
          jQuery('table.orderGoals').show('600');
        },
      });

  });

}

/* GRAPHICS */

function graphics_constructror(){

  jQuery(document).ready(function(){
    // Скрытие/Открытие блоков
      jQuery('.icon-chevron').click(function(){
        if( jQuery(this).hasClass('icon-chevron-down')){
          jQuery(this).parents('.graphic-block-header').siblings('.graphic-block-body').hide('600');
          jQuery(this).addClass('icon-chevron-up');
          jQuery(this).removeClass('icon-chevron-down');
        }
        else{
          jQuery(this).parents('.graphic-block-header').siblings('.graphic-block-body').show('600');
          jQuery(this).addClass('icon-chevron-down');
          jQuery(this).removeClass('icon-chevron-up');
        }           
      });

      // Работы с селектом
      jQuery('.graphic-type').click(function(){
        var select = jQuery(this).siblings('select');
        
        if(select.css('display') == 'none')
          select.show('400');
        else
          select.hide('400');
      });

      jQuery('.select-type').change(function(){
        
        placeholderClass = jQuery(this).siblings('.graphicPlaceholder').attr('class').split(' ')[1];
        placeholderName = placeholderClass.split('placeholder_')[1];
        
        switch ( jQuery(this).val() ){
          case 'pie':
            showDiagramPie(diagramData[placeholderName], placeholderClass);
            break;
          case 'column':
            showDiagramColumn(diagramData[placeholderName], placeholderClass);
            break;
          default:
            break;
        }

          jQuery(this).hide('400');
        });

      // Календарь
      jQuery( "#from" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
          jQuery( "#to" ).datepicker( "option", "minDate", selectedDate );
        }
      });
      jQuery( "#to" ).datepicker({
        defaultDate: "+1w",
        changeMonth: true,
        numberOfMonths: 3,
        onClose: function( selectedDate ) {
          jQuery( "#from" ).datepicker( "option", "maxDate", selectedDate );
        }
      });

      // Фильтр по дате
      jQuery('.graphics-date img').live("click", function(){

        var from = jQuery('#from').val();
        var to = jQuery('#to').val();
        document.location.href = "/moiai_module/graphics/?from=" + from + "&to=" + to;

      });

  })

}

/* CHECK */

function check_constructror(){

  jQuery('document').ready(function(){
          
    // Клик на "Проверить"
    jQuery('.do-decryption').click(function(){ 

      jQuery.ajax({
        type: 'POST',
        url: '../ajax/index.php',
        data: {
          basket_id: jQuery('input[name="basket_id"]').val(),
          type:'do-decryption'
          },
          dataType: 'json',
          cache: false,
          success: function(data) {
            jQuery('.decryption').html(data);
          },
        });
  
    });
  
  })

}

/* NEW_WORDS */

function new_words_constructor(){
  jQuery(document).ready(function(){
    
    //Добавление новых ключевых слов    
    jQuery('.new-words-table .confirm-new-word').click(function(){
      var confirm_new_word = jQuery(this);
      
      jQuery.ajax({
            type: 'POST',
            url: '../ajax/index.php',
            data: {
              id:       confirm_new_word.parents('tr').find('input[name="id"]').val(),
              alt_name: confirm_new_word.parents('tr').find('input[name="alt_name"]').val(),
              rus_name: confirm_new_word.parents('tr').find('input[name="rus_name"]').val(),
              priority: confirm_new_word.parents('tr').find('input[name="priority"]').val(),
              type:'confirm-new-word'
            },
            dataType: 'json',
            cache: false,
            success: function(data) {
              if(data == 'true')
                confirm_new_word.parents('tr').animate({ backgroundColor: "rgba(21, 139, 21, 0.35);" }, "fast").animate({ opacity: "hide" }, "slow");
              else
                alert('Ошибка добавления');
            },
          });
    });

    // Отклонение новых ключевых слов
    jQuery('.new-words-table .reject-new-word').click(function(){
      var reject_new_word = jQuery(this);
      jQuery.ajax({
              type: 'POST',
              url: '../ajax/index.php',
              data: {
                id: reject_new_word.parents('tr').find('input[name="id"]').val(),
                type:'reject-new-word'
              },
              dataType: 'json',
              cache: false,
              success: function(data) {
                if(data == 'true')
                  reject_new_word.parents('tr').animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "slow");
                else
                  alert('Ошибка отклонения');
              },
            });
      });

  })
}

/* SETTINGS */

function settings_contructor(){
jQuery(document).ready(function(){

  // Появление строки добавления метки

  jQuery('#utm-tag-add').click(function(){

    // Заполяем select в строке добавления новой метки секциями
    var sections = '';
    jQuery('tr.utm-section td.name').each(function(){
      sections = sections + '<option>'+$(this).text()+'</option>';
    });
  
    jQuery('.add-utm-section .utm-section-select').append(sections);
  
    // Появление строк
    jQuery('.utm-tag-setting-table .add-utm-section').show('400');
    jQuery('.utm-tag-setting-table .add-utm-section-buttons').show('400');

  });

  // Добавление элемента в таблицу меток

  $('#utm-tag-dynamic-add').click(function(){
        
    var add_utm_section = jQuery('tr.add-utm-section');
        
    var ajax = jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {
        name:     add_utm_section.find('input[name="name"]').val(), 
        rus_name: add_utm_section.find('input[name="rus_name"]').val(), 
        show:     add_utm_section.find('input[name="show"]').prop("checked"),
        priority: add_utm_section.find('input[name="priority"]').val(),
        section:  add_utm_section.find('select.utm-section-select').val(),
        type: 'utm-tag-add'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        if(data == "true"){
          location.reload();
        }else{
          alert("Ошибка добавления");
        }
      },
    });

  });


  // Удаление элемента из таблицы меток

  jQuery('#utm-tag-delete').click(function(){
        
    var delete_string = '';
  
    jQuery('.utm-tag-setting-table input:checkbox:checked').each(function(){
      delete_string = delete_string + jQuery(this).parent('td').siblings('td.id').text() + ',';
    });
  
    var ajax = jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {delete_string: delete_string, type:'utm-tag-delete'},
      dataType: 'json',
      cache: false
    });

    if(ajax)
      jQuery('.utm-tag-setting-table input:checkbox:checked').parents('tr').animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "slow");

  });

  // Настройки подключения

  jQuery('#connection .connection-buttons').click(function(){

    jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {
        sign_in: jQuery('#connection form.sign_in').serialize(),
        database: jQuery('#connection form.database').serialize(),
        type:'connection-settings-save'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        alert('Настройки удачно сохранены');
        location.reload();
      },
    });

  });

  // Дополнительные цели

    // Изменение цветов 
  jQuery('.otherGoalsSettings .active').live("click", function(){
    jQuery(this).removeClass('active').addClass('no-active-new');
  });
  jQuery('.otherGoalsSettings .no-active').live("click", function(){
    jQuery(this).removeClass('no-active').addClass('active-new');
  });
  jQuery('.otherGoalsSettings .active-new').live("click", function(){
    jQuery(this).removeClass('active-new').addClass('no-active');
  });
  jQuery('.otherGoalsSettings .no-active-new').live("click", function(){
    jQuery(this).removeClass('no-active-new').addClass('active');
  });

    // Сохранение изменений
  jQuery('.otherGoalsSettingsSave').live("click", function(){

    var active_new = {};
    var no_active_new = {};

    jQuery('.active-new').each(function(){
      var attrClass = jQuery(this).attr('class').split(' active-new');
      active_new[attrClass[0]] = active_new[attrClass[0]] + jQuery(this).siblings('.id').text() + ',';
    });
    jQuery('.no-active-new').each(function(){
      var attrClass = jQuery(this).attr('class').split(' no-active-new');
      no_active_new[attrClass[0]] = no_active_new[attrClass[0]] + jQuery(this).siblings('.id').text() + ',';
    });

    jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {
        active_new: active_new,
        no_active_new: no_active_new,
        type:'change-other-goals-fields'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        if(data == "true"){
          alert('Изменения успешно сохранены');
          jQuery('.active-new').removeClass('active-new').addClass('active');
          jQuery('.no-active-new').removeClass('no-active-new').addClass('no-active');
        }
      },
    });

  });

    // Удаление доп. целей.
  jQuery('.otherGoalsSettingsDelete').live("click", function(){

    var deleteFields = [];
    jQuery('.otherGoalsSettings .mark:checked').each(function(){
      deleteFields.push( jQuery(this).parent('.mark-td').siblings('.id').text() );
    });

    jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {   
        deleteFields: deleteFields,     
        type:'delete-other-goals'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        if(data == true){
          jQuery('.otherGoalsSettings .mark:checked').each(function(){
            jQuery(this).parents('tr').animate({ backgroundColor: "#fbc7c7" }, "fast").animate({ opacity: "hide" }, "slow");
          });
        }
      },
    });

  });

    // Изменение названия полей
  jQuery('.otherGoalsSettings th').live("dblclick", function(){
    var _this = jQuery(this);
    if( _this.text() == ' id ' || _this.text() == ' type ' )
      return false;
    jQuery(this).html( '<input rel="'+jQuery(this).text()+'" type="text" value="'+jQuery(this).text()+'">' );
  });

  jQuery('.otherGoalsSettings th input').live("focusout", function(){
    var _this = jQuery(this);

    if( _this.attr('rel') == _this.val() )
      _this.parent('th').text( _this.val() );
    else{
   
      jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {   
        newVal: _this.val(),     
        oldVal: _this.attr('rel'),
        type:'th-changed-other-goals'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {

        console.log(data);

        if(data == true){
          _this.parent('th').text( _this.val() ).addClass('changed');
          _this.parent('th').text( _this.val() );
        }
      },
      });

    }
    
  });

    // Добавление нового поля
  jQuery('.otherGoalsNewFields .btn-success').live("click", function(){
    
    if( !jQuery('.otherGoalsNewFields input[name="name"]').val() )
      return false;

    jQuery.ajax({
      type: 'POST',
      url: '../ajax/index.php',
      data: {   
        newField: jQuery('.otherGoalsNewFields input[name="name"]').val(),
        type:'new-field-other-goals'
      },
      dataType: 'json',
      cache: false,
      success: function(data) {
        if(data){
          alert("Успешно добавлено");
          location.reload();
        }
      }

    });

  });

})

}

/* ADD */

function add_constructor(){

  jQuery(document).ready(function(){
          
    // Добавление новых товаров
    jQuery('.add-product-fields').click(function(){
      var new_order_item = jQuery('.add-new-order-item').first().html();
      jQuery( jQuery('.add-new-order-item').last() ).after('<div class="add-new-order-item">' + new_order_item + '</div>');
      jQuery('.add-new-order-item').last().find('.order-number').text( jQuery('.add-new-order-item').length );
    });

    // Изменение цены товара приводит к динамической измене общей цены
    var new_price = 0;

    jQuery('.add-new-order-item input[name="product_price"]').change(function(){
      console.log('change-1');
      var new_price = 0;

      jQuery('.add-new-order-item').each(function(){
        product_price = jQuery(this).find('input[name="product_price"]').val();
        product_quantity = jQuery(this).find('input[name="product_quantity"]').val();
              
          if (product_price && product_quantity)
            new_price = new_price + product_price*product_quantity;

        jQuery('.main-price').text(new_price);

       });
    });

    jQuery('.add-new-order-item input[name="product_quantity"]').change(function(){
      var new_price = 0;

      jQuery('.add-new-order-item').each(function(){
        product_price = jQuery(this).find('input[name="product_price"]').val();
        product_quantity = jQuery(this).find('input[name="product_quantity"]').val();
              
        if (product_price && product_quantity)
          new_price = new_price + product_price*product_quantity;

        jQuery('.main-price').text(new_price);

      });
    });

    // Добавление заказа
    jQuery('.do-order').click(function(){

      var goods = new Array();
      var i = 0;

      jQuery('.add-new-order-item').each(function(){
        goods[i] = jQuery(this).find('input[name="product_id"]').val() + '&&';
        goods[i] = goods[i] + jQuery(this).find('input[name="product_name"]').val() + '&&';
        goods[i] = goods[i] + jQuery(this).find('input[name="product_price"]').val() + '&&';
        goods[i] = goods[i] + jQuery(this).find('input[name="product_quantity"]').val();
              
        i++;
      });

      $.ajax({
        type: 'POST',
        url: '../ajax/index.php',
        data: {
        order_price: jQuery('.add-new-order').find('.main-price').text(),
        currency: 'RUR',
        tax: jQuery('.add-new-order').find('input[name="tax"]').val(),
        delivery: jQuery('.add-new-order').find('input[name="delivery"]').val(),
        basket_id: jQuery('.add-new-order').find('input[name="basket-id"]').val(),
        goods: goods,
          type: 'do-order-manually'
        },
        dataType: 'json',
        cache: false,
        success: function(data) {
          console.log(data);
          if(data == "true"){
            alert("Заказ удачно добавлен");
            location.reload();
          } 
          else
            alert("Ошибка добавления заказа");
        },
      });

    });

  })

}

/* Другие функции */

function parseGetParams() { 
   var $_GET = {}; 
   var __GET = window.location.search.substring(1).split("&"); 
   for(var i=0; i<__GET.length; i++) { 
      var getVar = __GET[i].split("="); 
      $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
   } 
   return $_GET; 
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
      "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
  options = options || {};

  var expires = options.expires;

  if (typeof expires == "number" && expires) {
    var d = new Date();
    d.setTime(d.getTime() + expires*1000);
    expires = options.expires = d;
  }
  if (expires && expires.toUTCString) { 
    options.expires = expires.toUTCString();
  }

  value = encodeURIComponent(value);

  var updatedCookie = name + "=" + value;

  for(var propName in options) {
    updatedCookie += "; " + propName;
    var propValue = options[propName];    
    if (propValue !== true) { 
      updatedCookie += "=" + propValue;
     }
  }

  document.cookie = updatedCookie;
}

// Отправка формы при клике на Enter
document.onkeyup = function (e) {
  e = e || window.event;
  if (e.keyCode === 13) {
    if( ($('.form-signin input[name="login"]').is(':focus')) || ($('.form-signin input[name="password"]').is(':focus')) )
      $('.form-signin #sign-in-button').click();
  }
  // Отменяем действие браузера
  return false;
}



/**********/

jQuery(document).ready(function(){

  var getParams = parseGetParams();

  // Устанавливаем utm-cookie, которую берём из GET-параметров

  if(getParams['utm_medium']){
    console.log('qwerty');
    setCookie(
      'utm_get', 
      'utm_source='+getParams['utm_source']+'&'+'utm_medium='+getParams['utm_medium']+'&'+'utm_campaign='+getParams['utm_campaign']+'&'+'utm_term='+getParams['utm_term']+'&'+'utm_content='+getParams['utm_content'],
      { expires: 0, path: '/' }
    );
  }

})
