var main_fucnt = function(){
  this.data_flow = [];  
};

main = new main_fucnt();

main_fucnt.prototype.searchKeyPress = function(e){
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
  
main_fucnt.prototype.init = function(guest){
  var search_bar = $("#search-string");
  var search_helper = $("#search-helper");
  
  search_helper.css({
    left: search_bar.offset().left,      
    width:search_bar.width() + 24
  });
  
  $( window ).resize(function(){
    search_helper.css({
      left: search_bar.offset().left,        
      width:search_bar.width() + 24
    });
  });
  
  search_bar.keyup(this.searchKeyPress);
  
  search_bar.keydown(function (e){
    var keyCode = (e.keyCode ? e.keyCode : e.which);   
    e.stopPropagation();
    if (keyCode === 13) {
      var form = $("#search-form");
      document.location.href = "index.php?" + form.serialize();
    }
  });
  search_bar.click(function(){search_helper.removeClass("show");});  
  if(guest===0){
    setInterval(function() {
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
    },10000);
  }
};

main_fucnt.prototype.ajax = function(url,params,success,error){    
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

main_fucnt.prototype.loadPartList = function(item,params,table){
  var collapse = $(item).children(".panel-collapse").attr("aria-expanded");
  if(collapse==="true"){
    return;
  }    
  
  var data_flow = [];
  var form_query = $("#search-form").serializeArray();    
  var query = {};
  
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
    data_flow = Array.concat(data_flow,parts);
    
    var table_class = $("#"+table).DataTable();
    table_class.clear();
    table_class.rows.add(data_flow).draw();      
  }
  for (id in form_query){
    var name = form_query[id].name;
    var value = form_query[id].value;      
    query[name] = value;
  }    
  
  for (id in params){      
    query['maker_id'] = params[id];
    query['provider'] = id;
    this.ajax("/index.php?r=site/ajax-load-parts",query,onSuccess);      
  }
};

main_fucnt.prototype.getActiveOverPrice = function(){
  return $('#over-price').val();
};
  
main_fucnt.prototype.changeOverPrice  = function(){
  var parent = $("div.panel-collapse.collapse.in");    
  var table = parent.find("table");    
  var table_class = $(table).dataTable();
  table_class.api().draw(false);
};  
  
main_fucnt.prototype.searchHelperHide = function(item){    
  $("#search-helper").removeClass("show");
};
  
main_fucnt.prototype.insertSearch = function(item){
  var val = $(item).val();
  $('#search-string').val(val);
  $("#search-helper").removeClass("show");
};





