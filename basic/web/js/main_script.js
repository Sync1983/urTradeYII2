function main_fucnt(){
  var data_flow = [];
  
  function searchKeyPress(e){
    var search_helper = $("#search-helper");
    var search_text = $("#search-string");
    var select = search_helper.children("select");
    var search_string = search_text.val();
    
    if(search_string.length<3){
      search_helper.removeClass("show");
      return;
    }
    
    function onSuccess(answer){
      var data = [];    
      try{
        data = JSON.parse(answer);
      } catch (e){
        data = [];
      }      
      
      $(select).children().remove();
      for(var key in data)
        $(select).append("<option value=\"" + key + "\">" + data[key] + "</option>");
      
      $(select).children().dblclick(function(event){
        var value = event.currentTarget.value;
        search_text.val(value);
      });
      search_helper.addClass("show");      
    }   
    
    main.ajax("index.php?r=site/ajax-search-data",{text:search_string},onSuccess);    
  };
  
  function initSearch(){
    var search_bar = $("#search-string");
    var search_helper = $("#search-helper");
    
    search_helper.css({
      left: search_bar.offset().left,
      top:  search_bar.offset().top + 14 + search_bar.height(),
      width:search_bar.width() + 12
    });
    
    $( window ).resize(function(){
      search_helper.css({
        left: search_bar.offset().left,
        top:  search_bar.offset().top + 14 + search_bar.height(),
        width:search_bar.width() + 12
      });
    });
    
    search_bar.keyup(searchKeyPress);
    search_bar.keydown(function (e){
      var keyCode = (e.keyCode ? e.keyCode : e.which);   
      e.stopPropagation();
      if (keyCode === 13) {
        var form = $("#search-form");
        document.location.href = "index.php?" + form.serialize();
      }
    });
    search_bar.click(function(){
      search_helper.removeClass("show");
    });
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
  
  this.init = function(){
    initSearch();    
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
  
  this.showLoginWindow = function(id,parent){
    var wnd = new windows(id,"Вход пользователя",true);
    wnd.setWindowToCenter();
    wnd.showWindow();
  };
  
  return this;
}

main = main_fucnt();

