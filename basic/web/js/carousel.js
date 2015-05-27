(function( $ ) {
  $.fn.carousel_atc = function( options ) {
    var self = this;
    
    self.order = [];
    self.activeArea = null;
    
    function getNeighbors(area){
      var index = self.order.indexOf(area);
      var len   = self.order.length;
      
      var prev = index - 1;
      var next = index + 1;
      
      if( prev < 0){
        prev = len - 1;
      }
      if( next >= len ) {
        next = 0;
      }
      return {prev:prev, next:next};
    }
    
    function onPrev(event){
      var neighbors = getNeighbors(self.activeArea);
      setActive(self.order[neighbors.prev]);
    }
    
    function onNext(event){
      var neighbors = getNeighbors(self.activeArea);
      setActive(self.order[neighbors.next]);
    }
    
    function onIndicator(event){
      var area = $(this).attr("data-area");
      setActive(area);
    }
    
    function setActive(area, fast){      
      var imgPart = $(self).children("ul.img-part");
      var textPart = $(self).children("div.text-part");
      var indicators  = $(self).children(".indicators");
      
      self.activeArea = area;
      
      indicators.children().removeClass("active");
      indicators.find('[data-area='+self.activeArea+']').addClass("active");      
      
      function showImg(){
        imgPart.children().removeClass("pre-show");
        imgPart.children().removeClass("active");
        imgPart.find('[data-area='+self.activeArea+']').addClass("active");
      }
      
      function showText(){
        textPart.children().removeClass("pre-show");
        textPart.children().removeClass("active");
        textPart.find('[data-area='+self.activeArea+']').addClass("active");        
      }
      
      if ( fast === true) {
        showImg();
        showText();
        return;
      }        
      
      var oldImg = imgPart.find('.active');
      var newImg = imgPart.find('[data-area='+self.activeArea+']');
      var oldText= textPart.find('.active');
      var newText= textPart.find('[data-area='+self.activeArea+']');
      
      newImg.addClass("pre-show");      
      oldImg.animate({opacity:0},1500);
      
      newImg.animate({opacity:1},1500,function(){        
        showImg();
      });
      
      newText.addClass("pre-show");      
      oldText.animate({opacity:0},1500);
      
      newText.animate({opacity:1},1500,function(){        
        showText();
      });
      
    }
    
    function _init(){
      var imgPart     = $(self).children(".img-part");
      var indicators  = $(self).children(".indicators");
      
      //Заменяем img на div с фоном, добавляем индикатор, вешаем не индикатор слушателя
      imgPart.find(".item").each(function (index, item){        
        var area    = $(item).attr("data-area");
        var imgSrc  = $(item).attr("img-src");         
        var wrapper = $("<div class=\"img-wrapper\"></div>");
        var indicator = $('<li data-area="' + area + '"></li>');
        
        wrapper.css("background-image","url("+imgSrc+")");        
        indicator.click(onIndicator);
        
        $(item).append(wrapper);
        $(indicators).append(indicator);
        
        
        self.order.push(area);
        if( $(item).hasClass("active") ){
          self.activeArea = area;
        }
      });      
      
      if( self.activeArea === null ){        
        setActive(self.order[0],true);
      }
      
      self.find("a.arrow").each(function (index, item){
        var side = $(item).attr("data-arrow");
        if( side === "prev" ){
          $(item).click(onPrev);
        }else if ( side === "next" ){
          $(item).click(onNext);          
        }
      });
      
    }
    
    _init();
    setInterval(onNext,10000,null);
    return this; 
  }; 
}( jQuery ));





