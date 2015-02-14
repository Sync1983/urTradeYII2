var windows_quene = new Object();

function windows(id,name,init){
  if(windows_quene.hasOwnProperty(id))
      return windows_quene[id];
    
  var id = id;
  var item = null
  var name = name;
  var text = "text";
  var dwnX = 0;
  var dwnY = 0;
  var itemLeft = 0;
  var itemTop = 0;
  var drag = false;
  
  function onMouseDown(event){
    dwnX = event.clientX;
    dwnY = event.clientY;    
    itemLeft = item.position().left;
    itemTop  = item.position().top;        
    drag = true;
  };
  
  function onMouseUp(event){
    drag = false;
  };
  
  function onMouseMove(event){
    if(!drag)
      return;
    event.stopPropagation();
    event.stopImmediatePropagation();
    if(event.buttons!==1)
      return;
    
    var posX = event.clientX;
    var posY = event.clientY;
    
    posX = posX - dwnX;
    posY = posY - dwnY;    
    
    item.css({
      left: (itemLeft + posX)+"px",
      top: (itemTop + posY)+"px"
    });
  }
  
  this.initWindow = function(){    
    if(windows_quene.hasOwnProperty(id))
      return windows_quene[id];    
    windows_quene[id] = this;
    
    text = $("#"+id).html();
    console.log(text);
    $("#"+id).remove();    
    text = '<div id="'+id+'" class="panel panel-primary">'+
              '  <div class="panel-heading window-heading">'+name+'<a href="#" class="pull-right close" onclick="windowsCloseById(\''+id+'\');"><span aria-hidden="true">&times;</span></a></div>'+
              '   <div class="panel-body">'+
              '     <div class="window">'+
              text +
              '     </div>'+
              '   </div>'+
              '  </div>'+
              '</div>';      
    $( "body" ).append(text);
    item = $("#"+id);
    item.children("div.panel-heading").mousedown(onMouseDown);    
    item.children("div.panel-heading").mouseup(onMouseUp);    
    $("body").mousemove(onMouseMove);    
    item.css({
      'z-index': 1001,
      'position':'fixed'});
    this.hideWindow();
    this.visibly = false;
  };
  
  this.setPosititon = function(posX,posY){
    if(item===null)
      return false;
    item.css({
      'left': posX+"px",
      'top':posY+"px"});
    return true;
  };
  
  this.showWindow = function(){
    if((this.visibly)||(item===null))
      return false;
    item.removeClass("hidden");    
    this.visibly = true;
    return true;
  };
  
  this.hideWindow = function(){
    if((!this.visibly)||(item===null))
      return false;
    item.addClass("hidden");
    this.visibly = false;
    return true;
  };
  
  this.setWindowToCenter = function(){
    if(item===null)
      return false;
    var width = item.width()/2;
    var height= item.height()/2;
    item.css({
      'left':'50%',
      'top':'50%',
      'margin-left':'-'+width.toFixed(0)+'px',
      'margin-top':'-'+height.toFixed(0)+'px'
    });    
  };
  
  if(init)
    this.initWindow();
}

function windowsCloseById(id){  
  windows_quene[id+""].hideWindow();
}

//windows = new windows_fucnt();

