/* global Function */

(function( $ ) {
  $.fn.resizeble = function( options ) {
    var self = this;    
    var middle = 1280;
    var low = 740;
    
    self.events = {};
    
    self.onResize = function onResize(event){
      var state = "size-";
      $(".resizeble").removeClass("size-high");
      $(".resizeble").removeClass("size-middle");
      $(".resizeble").removeClass("size-low");
      var width = $(window).width();
      if( width < low ){
        state += "low";
      }else if (width < middle ){
        state += "middle";
      }else {
        state += "high";
      }
      $(".resizeble").addClass(state);
    };
    
    function _init() {
      var name = $(self).prop("tagName");
      if( name === "BODY"){
        $(window).resize(self.onResize);
        self.onResize(null);
        return;
      }
      if( options instanceof Function ){
        Object.defineProperty(self.events,this,{
          value: options
        });
      }      
    }
    
    _init();    
    return this; 
  }; 
}( jQuery ));





