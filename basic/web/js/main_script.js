function main_fucnt(){
  var data_flow = [];
  
  function getDataStruct(){
    var search = $('#search-string').val();
    var cross  = $('#cross').prop('checked');
    var overPrice = $('#over-price').val();
    var line = ((search!=="")?("&search="+search):"")+"&cross="+cross+"&op="+overPrice;
    return encodeURI(line);    
  }
  
  function getDataStructArray(){
    var search = $('#search-string').val();
    var cross  = $('#cross').prop('checked');
    var overPrice = $('#over-price').val();
    var line = {
      search:search,
      cross:cross,
      op:overPrice
    };
    return line;
  }
  
  this.getDataFlow  = function(){
    return data_flow;
  };
  
  this.getActiveOverPrice = function(){
    return $('#over-price').val();
  };
  
  this.changeOverPrice  = function(){
    var parent = $("div.panel-collapse.collapse.in");    
    var table = parent.find("table");    
    var table_class = $(table).dataTable();
    table_class.api().draw(false);
  };
  
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
  
  this.searchClick = function(){
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
  
  this.searchHelperHide = function(item){    
    $("#search-helper").removeClass("show");
  };
  
  this.insertSearch = function(item){
    var val = $(item).val();
    $('#search-string').val(val);
    $("#search-helper").removeClass("show");
  };
  
  this.loadPartList = function(item,params,table){
    var collapse = $(item).children(".panel-collapse").attr("aria-expanded");
    if(collapse==="true"){
      return;
    }    
    var maker_id;
    var query = getDataStructArray();
    var table_name = table;
    
    function onSuccess(data){      
      var answer = false;
      try{
        answer = JSON.parse(data);
      }catch(err){
        console.log(err);
      }
      if((!answer)||(!answer.id))
        return;      
      
      var parts = answer.parts;
      data_flow = data_flow.concat(parts);
      
      var table_class = $("#"+table).DataTable();
      table_class.clear();
      table_class.rows.add(data_flow).draw();      
    }
    
    data_flow = [];
    for (id in params){
      maker_id = params[id];      
      query['maker_id'] = maker_id;
      query['provider'] = id;
      this.ajax("/index.php?r=site/ajax-load-parts",query,onSuccess);      
    }
  };
  
  this.historyItemClick = function(text){
    $('#search-string').val(text);
    this.searchClick();
  };
  
  this.ajax = function(url,params,success,error){    
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
  
  return this;
}

main = main_fucnt();

