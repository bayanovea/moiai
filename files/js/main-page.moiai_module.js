$(document).ready(function(){

        // Устанавливаем id корзины
        setBasketId();

        // Отправка формы обратного звонка
        jQuery('.box-conversion-send').click(function(){

          var getParams = parseGetParams();

          $.ajax({
            type: 'POST',
            url: './ajax/index.php',
            data: {
              form: jQuery('.box-conversion form').serialize(),
              type: 'box-conversion-send'
            },
            dataType: 'json',
            cache: false,
            success: function(data) {
              console.log(data);
            },
          });

        });

      })

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
            url: './ajax/index.php',
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