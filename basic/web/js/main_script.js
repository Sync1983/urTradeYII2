/* global main_funct */
(function( $ ) {
  $.fn.main = function(item){
    var self = this;
    
    self.ajax = function(url,params,success,error) {    
      jQuery.ajax({                
              url: url,
              type: "POST",
              data: params,
              error: function(xhr,tStatus,e){
                $(".preloader").removeClass("show");
                console.log("Ajax error: ",xhr,tStatus,e);
                if(error)
                  error(xhr,tStatus,e);
              },
              success: function(data){
                $(".preloader").removeClass("show");
                if(success)
                  success(data);
              },
              beforeSend:	function(){ 
                $(".preloader").addClass("show"); 
              }
      });	
    };
    
    self.getActiveOverPrice = function(){
      var value = $('input[name="op"]').val();      
      if( !value ){
        return 0;
      }
      return value*1;
    };
    
    self.onAddToBasket = function (e){
      var target = e.currentTarget;
      var row = $(target).parent().parent();
      var table = $(row).parent().parent();
      var table_class = $(table).DataTable();
      var data = table_class.rows(row).data()[0];

      e.stopPropagation();  

      if( !data.lot_quantity ){
        data.lot_quantity = 1;
      }

      var wndCount = $('#count-request');
      wndCount.find("input#basketaddform-id").val(data.id);
      wndCount.find("#add-describe").html("<b>"+data.articul+"</b> "+data.name);
      wndCount.find("#add-step").html("<b>"+data.lot_quantity+"</b> шт.");  
      wndCount.find("input#basketaddform-count").attr('step',data.lot_quantity);
      wndCount.find("input#basketaddform-count").val(data.lot_quantity);
      if(data.info){
        wndCount.find("#add-info").text(data.info);  
        wndCount.find("div.basket-info").show();    
      } else {
        wndCount.find("div.basket-info").hide();
      };
      var form = $("#basket-add-form").each(function(){
        $(this).find('.has-error').removeClass('has-error');
        $(this).find('.has-success').removeClass('has-success');
        $(this).find('.help-block').text('');
      });
      wndCount.modal();

      return false;
    };
    
    self.loadParts = function(source){      
      var head = this;
      var textLine = $(head).find("div.best-var");  
      var query = {};
      var form_query = $("#search-form").serializeArray(); 
      var table_class = $(head).find("table.out-data").DataTable();
      
      function onSuccess(data){    
        var id = data.id;
        var parts = data.parts;    
        console.log(parts);        
        $(textLine).find("div#part-loader"+id).remove();
        var rows = table_class.rows;
        var rows_part = rows.add(parts);
        rows_part.draw();
        table_class.rows().data().sort();

        $("body").find("a.ref-to-basket").unbind("click");    
        $(head).find("a.ref-to-basket").click(self.onAddToBasket);
        
      };

      function onError(data){
        console.log(data);
      };

      table_class.clear();  

      for(var key in source){
        var value = source[ key ];
        var newLoader = $("<div></div>")
              .addClass("part-loader")
              .attr("id","part-loader"+key)
              .text(" ");
        textLine.append(newLoader);
        query = form_query.slice();        
        query.push({
          name: "maker_id",
          value: value
        });
        query.push({
          name: "provider",
          value: key      
        });    
        self.ajax("/index.php?r=site/ajax-load-parts",query,onSuccess,onError);    
      }  
    };
    
    self.changeOverPrice  = function(){
      var value = $(".over-price").val()+"";
      var num_value = value.substring(0,value.length - 2) * 1;
      $('input[name="op"]').val(num_value);
      $(this).click(onChangeOverPrice);      
    };
    
    self.searchHelperButton = function(){
      $(this).click(onSearchHelperButton);      
    };
    
    self.infoBtnHover = function(text){
      $(this).tooltip({
        html:true,        
        title: "<p class=\"tooltip-text\">" + text + "</p>"
      });
    };
    
    function onChangeOverPrice (e){
      var parent = $("div.panel-collapse.collapse.in");    
      var table = parent.find("table");    
      var table_class = $(table).dataTable();
      var value = $(".over-price").val()+"";
      var num_value = value.substring(0,value.length - 2) * 1;
      $('input[name="op"]').val(num_value);
      table_class.api().draw(false);      
    }
    
    function onSearchHelperButton( e ){
      var item = e.currentTarget;
      var parent = $(item).parent();
      var articul = parent.text();
      $("#search-string").val(articul);
      $("#search-btn").click();
    }
    
    function _init_helper_position(search_bar, search_helper){
      search_helper.css({left: search_bar.offset().left});
      $( window ).resize(function(){ search_helper.css({left: search_bar.offset().left});});
    }

    function _init_helper_events(search_bar, search_helper){
        var search_text = $("#search-string");
        var select = search_helper.children("select");

        function onKeyDown(e){
          var keyCode = (e.keyCode ? e.keyCode : e.which);   
          e.stopPropagation();
          if (keyCode === 13) {
            var form = $("#search-form");
            document.location.href = "index.php?" + form.serialize();
          }      
        }

        function onKeyUp(e){      
          var search_string = search_text.val();

          if(search_string.length<3){
            search_helper.removeClass("show");
            return;
          }

          function jsonToSelector(data){
            $(select).children().remove();
            for(var key in data)
              $(select).append("<option value=\"" + key + "\">" + data[key] + "</option>");        
          }

          function onSuccess(answer){        
            jsonToSelector(answer);

            $(select).children().dblclick(function(event){
              var value = event.currentTarget.value;
              search_text.val(value);
            });
            search_helper.addClass("show");      
          }   

          self.ajax("index.php?r=site/ajax-search-data",{text:search_string},onSuccess);      
        };


        search_bar.keyup( onKeyUp );
        search_bar.keydown( onKeyDown );  
        search_bar.click( function(){
          search_helper.removeClass("show");
        });  
      }
    
    function _init(){      
      var search_bar    = $("#search-string");
      var search_helper = $("#search-helper");

      _init_helper_position(search_bar, search_helper);
      _init_helper_events(search_bar, search_helper);
      
      if( window.isGuest === 0 ){
        /*setInterval(function() {
        function success(answer){
           var data = JSON.parse(answer);
          if(!data['messages']){
             return;
          }
          for(var id in data['messages']){
             $('.bottom-right').notify({
                type: 'bangTidy',
                message: {text: data['messages'][id]}
               }).show();
           }
         };
         main.ajax("index.php?r=site/notify",{},success);
        },10000);*/
      }
    }  
    
    if( item === "init" ){      
      _init();  
    }
    return self;
  };  
}(jQuery));
