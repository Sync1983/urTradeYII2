function main_fucnt(){
  
  function getDataStruct(){
    var search = $('#search-string').val();
    var cross  = $('#cross').prop('checked');
    var overPrice = $('#over-price').val();
    var line = ((search!=="")?("&search="+search):"")+"&cross="+cross+"&op="+overPrice;
    return encodeURI(line);    
  }
  
  this.initMainMenu = function(){
    $('.nav-atc-list > li > a').each(function(index,item){      
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
  };
  
  this.searchKeyPress = function(item){
    function onSuccess(data){
      if(data!=="none"){
        $("#search-helper").addClass("show");
        $("#search-helper").html(data);
      }
      else
        $("#search-helper").removeClass("show");
    }
    
    var val = $(item).val();
    this.ajax("index.php?r=site/ajaxsearchdata",{text:val},onSuccess);    
  };
  
  this.insertSearch = function(item){
    var val = $(item).val();
    $('#search-string').val(val);
    $("#search-helper").removeClass("show");
  }
  
  this.ajax = function(url,params,success,error){    
    jQuery.ajax({                
                url: url,
                type: "POST",
                data: params,
                error: function(xhr,tStatus,e){
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
  
  return this;
}

main = main_fucnt();

