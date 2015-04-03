var admin = function(){
  this.data_flow = [];
  
  $('.menu-item').each(function(index, elem){    
    var loc = window.location.pathname + window.location.search;
    var href = $(elem).attr('href');
    if( href === loc ){
      $(elem).parent().addClass('active');
    }
  });
  
  return this;
}

$.admin = admin(window);

