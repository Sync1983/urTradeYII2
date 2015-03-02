var windows_quene = new Object();

var windows = function (id,name,init){
  this.id = id;
  this.item = null;
  this.name = name;  
  this.dwnX = 0;
  this.dwnY = 0;
  this.itemLeft = 0;
  this.itemTop = 0;
  this.drag = false;
  
  if(windows_quene.hasOwnProperty(id))
      return windows_quene[id];    
  
  if(init)
    this.initWindow();
};

windows.prototype.mouseDown = function(event){
  item = event.data;  
  item.dwnX = event.clientX;
  item.dwnY = event.clientY;    
  item.itemLeft = item.item.position().left;
  item.itemTop  = item.item.position().top;        
  item.drag = true;
};

windows.prototype.mouseUp = function(event){
  item = event.data;  
  item.drag = false;
};

windows.prototype.mouseMove = function(event){
  item = event.data;
  if(item.drag!==true)
        return;
  event.stopPropagation();
  event.stopImmediatePropagation();
  if(event.buttons!==1)
    return;

  var posX = event.clientX;
  var posY = event.clientY;

  posX = posX - item.dwnX;
  posY = posY - item.dwnY;    

  item.item.css({
    left: (item.itemLeft + posX)+"px",
    top: (item.itemTop + posY)+"px"
  });
};

windows.prototype.initWindow = function(){
    
    if(windows_quene.hasOwnProperty(this.id))
      return windows_quene[this.id];    
    windows_quene[this.id] = this;
    
    text = $("#"+this.id).html();    
    $("#"+this.id).remove();    
    text = '<div id="'+this.id+'" class="panel panel-primary">'+
              '  <div class="panel-heading window-heading">'+this.name+'<a href="#" class="pull-right close" onclick="windowsCloseById(\''+this.id+'\');"><span aria-hidden="true">&times;</span></a></div>'+
              '   <div class="panel-body">'+
              '     <div class="window">'+
              text +
              '     </div>'+
              '   </div>'+
              '  </div>'+
              '</div>';      
    $( "body" ).append(text);
    this.item = $("#"+this.id);
    
    this.item.children("div.panel-heading").mousedown(this,this.mouseDown);    
    this.item.children("div.panel-heading").mouseup(this,this.mouseUp);    
    $("body").mousemove(this,this.mouseMove);
    
    this.item.css({
      'z-index': 1001,
      'position':'fixed'});
    this.hideWindow();
    this.visibly = false;
  };

windows.prototype.setPosititon = function(posX,posY){
    if(this.item===null)
      return false;
    this.item.css({
      'left': posX+"px",
      'top':posY+"px"});
    return true;
  };
  
windows.prototype.showWindow = function(){
    if((this.visibly)||(this.item===null))
      return false;
    this.item.removeClass("hidden");    
    this.visibly = true;
    return true;
  };
  
windows.prototype.hideWindow = function(){
    if((!this.visibly)||(this.item===null))
      return false;
    this.item.addClass("hidden");
    this.visibly = false;
    return true;
  };
  
windows.prototype.setWindowToCenter = function(){
    if(this.item===null)
      return false;
    var width = this.item.width()/2;
    var height= this.item.height()/2;
    this.item.css({
      'left':'50%',
      'top':'50%',
      'margin-left':'-'+width.toFixed(0)+'px',
      'margin-top':'-'+height.toFixed(0)+'px'
    });    
  };
  
function windowsCloseById(id){  
  windows_quene[id+""].hideWindow();
}

//windows = new windows_fucnt();

