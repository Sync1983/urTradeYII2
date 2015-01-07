function main_fucnt(){
  
  function getDataStruct(){
    var search = $('#search-string').val();
    var cross  = $('#cross').prop('checked');
    var overPrice = $('#over-price').val();
    var line = ((search!=="")?("&search="+search):"")+"&cross="+cross+"&op="+overPrice;
    return encodeURI(line);    
  }
  
  this.initMainMenu = function(){
    $('.menu-item > a').each(function(index,item){
      $(item).attr('onClick',"menuClick(this);");      
    });
    $('#search-button').attr('onClick',"searchClick(this)");
  };
  
  this.menuClick = function(item){
    var ref = $(item).attr('href');
    $(item).attr('href',ref+getDataStruct());
    return true;
  };
  
  this.searchClick = function(item){
    document.location.href = "index.php?r=site/search"+getDataStruct();
    return true;
  }
  
  return this;
}

main = main_fucnt();

