function admin_funct(){
  
  function menu_click(event){
    var item = event.target;
    var item_name = $(item).text();
    var content = $("div.admin-content");
    
    function onSuccess(answer){
      try{
        answer = JSON.parse(answer);
      }catch(e){
        console.log(e);
      };
      if(answer.error){
        onError({responseText:answer.error});
        return;
      }
      if(answer.html){
        content.html(answer.html);
      }
    }
    
    function onError(answer){
      content.html(
        '<div class="alert alert-danger" role="alert">'+
        ' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+
        ' <span class="sr-only">Ошибка:</span>' + answer.responseText +
        '</div>'              
      );
    }
    
    main.ajax("index.php?r=admin/menu-call",{item:item_name},onSuccess,onError);
  };
  
  this.init = function(wnd){
    var row = $(".a-row");
    
    if(row.height()<800){
      row.css("height","800px");
    };
    var height = row.height()+"px";
    $(".a-row").children().each(function(i,e){      
      $(e).css("height",height);
    });
    
    $("ul.a-menu").click(menu_click);
  };
  
  this.news_init = function(){
    function headerChange(e){
      var item = $(e.currentTarget);
      var head = item.parent().parent();
      var header = $(head).children("div.news-view").children("label.news-header");
      header.text(item.val());
    }
    
    function textChange(e){
      var item = $(e.currentTarget);
      var head = item.parent().parent();
      var text = $(head).children("div.news-view").children("label.news-text");
      text.html(item.val());
    }
    
    function iconChange(e){
      var item = $(e.currentTarget);
      var head = item.parent().parent();
      var icon = $(head).children("div.news-view").children("img");
      icon.attr("src",item.val());
    }
    
    function onSave(e){
      var item = $(e.currentTarget);
      var head = item.parent().parent();
      var icon = $(head).children("div.news-view").children("img");
      var text = $(head).children("div.news-view").children("label.news-text");
      var header = $(head).children("div.news-view").children("label.news-header");
      var show = $(head).children("div.row").children("input[name='post-show']");
      
      function onSuccess(answer){
        try{
          answer = JSON.parse(answer);
        }catch(e){
          console.log(e);
        };
        if(answer.error){
          onError({responseText:answer.error});
          return;
        }
        if(answer.ok){
          $(head).children('div#uid').append('<div class="alert alert-info" role="alert">'+
            ' <span class="glyphicon glyphicon glyphicon-ok-sign" aria-hidden="true"></span>'+
            ' <span class="sr-only">Всё хорошо!</span> Запись сохранена.'+
            '</div>');
        }
      }
      
      function onError(answer){        
        $(head).children('div#uid').append('<div class="alert alert-danger" role="alert">'+
        ' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+
        ' <span class="sr-only">Ошибка:</span>' + answer.responseText +
        '</div>');
      }
      
      main.ajax(
              "index.php?r=admin/news-save",
              { uid: item.attr('uid'),
                head: header.text(),
                text: text.text(),
                icon: icon.attr('src'),
                show: show.prop('checked')},
              onSuccess,
              onError);      
    }
    
    $("input[name='post-header']").each(function(i,e){      
      $(e).change(headerChange);
      $(e).keypress(headerChange);
    });
    
    $("input[name='post-icon']").each(function(i,e){      
      $(e).change(iconChange);
      $(e).keypress(iconChange);
    });
    
    $("input[type='button'].btn").each(function(i,e){      
      $(e).click(onSave);
    });
    
    $("textarea[name='post-text']").each(function(i,e){      
      $(e).change(textChange);
      $(e).keypress(textChange);
    });
  };
  
  return this;
}

admin = admin_funct(window);

