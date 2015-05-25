/* global main_funct */

var main_function = function(){
  this.data_flow = [];  
};

main = new main_function();

main_function.prototype.searchKeyPress = function(e){
  var search_helper = $("#search-helper");
  var search_text = $("#search-string");
  var select = search_helper.children("select");
  var search_string = search_text.val();
  
  if(search_string.length<3){
    search_helper.removeClass("show");
    return;
  }
  
  function onSuccess(answer){
    var data = answer;
    
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
  
main_function.prototype.init = function(guest){
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
    /*setInterval(function() {
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
    },10000);*/
  }
};

main_function.prototype.ajax = function(url,params,success,error) {    
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

main_function.prototype.getActiveOverPrice = function(){
  return $('#over-price').val();
};
  
main_function.prototype.changeOverPrice  = function(){
  var parent = $("div.panel-collapse.collapse.in");    
  var table = parent.find("table");    
  var table_class = $(table).dataTable();
  table_class.api().draw(false);
};  
  
main_function.prototype.searchHelperHide = function(item){    
  $("#search-helper").removeClass("show");
};
  
main_function.prototype.insertSearch = function(item){
  var val = $(item).val();
  $('#search-string').val(val);
  $("#search-helper").removeClass("show");
};

main_function.prototype.loadParts = function(source,head){
  var textLine = $(head).find("div.best-var");  
  var query = {};
  var form_query = $("#search-form").serializeArray(); 
  var table_class = $(head).find("table.out-data").DataTable();
  
  function onSuccess(data){    
    var id = data.id;
    var parts = data.parts;    
    
    $(textLine).find("div#part-loader"+id).remove();
    
    table_class.rows.add(parts).draw();
    table_class.rows().data().sort();
    
    $("body").find("a.ref-to-basket").unbind("click");    
    $(head).find("a.ref-to-basket").click(main.onAddToBasket);
  };
  
  function onError(data){
    console.log(data);
  };
  
  table_class.clear();  
  
  for(var key in source){
    var value = source[ key ];
    var newLoader = $("<div></div>")
          .addClass("part-loader")
          .attr("id","part-loader"+key)
          .text(" ");
    textLine.append(newLoader);
    query = form_query;
    query.push({
      name: "maker_id",
      value: value
    });
    query.push({
      name: "provider",
      value: key      
    });    
    main.ajax("/index.php?r=site/ajax-load-parts",query,onSuccess,onError);    
  }  
};

main_function.prototype.onAddToBasket = function (e){
  var target = e.currentTarget;
  var row = $(target).parent().parent();
  var table = $(row).parent().parent();
  var table_class = $(table).DataTable();
  var data = table_class.rows(row).data()[0];
  
  e.stopPropagation();  
  
  if( !data.lot_quantity ){
    data.lot_quantity = 1;
  }
  
  var wndCount = $('#count-request');
  wndCount.find("input#basketaddform-id").val(data.id);
  wndCount.find("#add-describe").html("<b>"+data.articul+"</b> "+data.name);
  wndCount.find("#add-step").html("<b>"+data.lot_quantity+"</b> шт.");  
  wndCount.find("input#basketaddform-count").attr('step',data.lot_quantity);
  wndCount.find("input#basketaddform-count").val(data.lot_quantity);
  if(data.info){
    wndCount.find("#add-info").text(data.info);  
    wndCount.find("div.basket-info").show();    
  } else {
    wndCount.find("div.basket-info").hide();
  };
  var form = $("#basket-add-form").each(function(){
    $(this).find('.has-error').removeClass('has-error');
    $(this).find('.has-success').removeClass('has-success');
    $(this).find('.help-block').text('');
  });
  wndCount.modal();
  
  return false;
};
