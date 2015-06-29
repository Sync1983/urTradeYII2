(function( $ ) {
  "use strict";
  var settings = {
      attributes: {},
      line_len: 3,
      width:null
    };
  
  var methods = {    
  init: function (options){
      var $this = $(this),
          data = $this.data('tile'),
          head = null;
      
      settings = $.extend(settings,options);
      $this.hide();
      
      head = $("<div></div>").addClass("tiles-head");
      $this.parent().append(head);
      
      initHead(head);
      initBody($this,head);
      $this.remove();
      
      $(head).on("click","div.tile-item",onClick);
      
      function initHead(parent){
        var attribute,
            attr_name,
            name,
            button_text,
            buttons,
            button,
            row;
        row = $("<div></div>").addClass("row order-type-filter");
        buttons = $("<div></div>").addClass("btn-group").attr("role","group").css("text-allign","center");
        row.append(buttons);
        $(parent).append(row);

        for(var i in settings.attributes){
          attr_name = i;
          attribute = settings.attributes[i];
          name = attribute.name || attr_name;
          button_text = attribute.button_text || null;
          button = $("<button></button>").addClass("btn").addClass("btn-default").attr("type","button");
          if( button_text){
            name += " [" + button_text + "] ";
          };
          button.text(name);
          buttons.append(button);
        }
      }
      
      function initBody(root,parent){
        var body,
            head,            
            tile,            
            tile_head,
            tile_body,
            width;
    
        width = settings.width || (($this.parent().width() / (settings.line_len + 0.5)).toFixed(0) * 1);
        
        body = $("<div></div>").addClass("tiles-body");
        
        
        $(parent).append(body);
        $(root).children("li").each(function(index,item){
          tile = $("<div></div>").addClass('tile-item');
          tile_head = $("<div></div>").addClass('tile-head').addClass("clearfix");
          tile_body = $("<div></div>").addClass('tile-body');
          tile.append(tile_head).append(tile_body);
          body.append(tile);
          for(var i = 0; i < item.attributes.length; i++) {
            if( item.attributes[i].value ){
              tile.attr(item.attributes[i].name,item.attributes[i].value);
            }
          }
          head = $(item).children("div.tile-head");          
          tile_head.html(head.html());
          head.remove();
          tile_body.html($(item).html());
          $(item).remove;
          tile.width(width);
        });
      }
      
      function onClick(event){
        var action = $(event.currentTarget).attr("action");
        if( action ){
          window.location.href = action;
        }
      }
    }
    
  };
  
$.fn.tiles = function(method) {  
 
  if ( methods[method] ) {
    return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
  } else if ( typeof method === 'object' || ! method ) {
    return methods.init.apply( this, arguments );
  } else {
    $.error( 'Метод с именем ' +  method + ' не существует для jQuery.tiles' );
  }  

};
})(jQuery);