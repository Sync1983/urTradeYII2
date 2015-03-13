var news_roller = function(){
  this.clip   = null;
  this.roller = null;
  this.left = null;
  this.right = null;
  this.head = null;
};

news_roller.prototype.init = function(){    
    this.head   = $("#news-roller").children("div.panel-body");    
    this.clip   = this.head.children("div.news-clip");
    this.roller = this.clip.children(".news-roller");
    var  childs = this.roller.children().length+0.8;
    this.left   = this.head.children("button.new-scroll-left");
    this.right  = this.head.children("button.new-scroll-right");
    this.left.attr('disabled',1);
    var width = this.clip.width()/3.4;
    
    this.roller.children().width(width);
    
    this.right.click(this,this.onRight);
    this.left.click(this,this.onLeft);
    
    this.roller.css({
      width:childs*width+"px",
      left: "0px"});    
    
    if(this.clip.width()>this.roller.width()){
      this.right.attr('disabled',1);
    }
};

news_roller.prototype.onRight = function(event){
  var item = event.data;
  item.roller.animate({left:"-=110"},function(){
    if(item.roller.position().left+item.roller.width()<item.clip.width()){
      item.roller.css("left",-item.roller.width()+item.clip.width()+"px");
    }
  });  
  item.left.removeAttr('disabled');  
  var pos_left = item.roller.position().left+item.roller.width();
  
  if(pos_left-110<item.clip.width()){    
    item.right.attr('disabled',1);
  }
};

news_roller.prototype.onLeft = function(event){
  var item = event.data;
  if(item.roller.position().left>-110){
    item.left.attr('disabled',1);
    return;    
  }
  item.roller.animate({left:"+=110"},function(){
    if(item.roller.position().left>0){
      item.roller.css("left","0px");
    }
  });  
  item.right.removeAttr('disabled');  
  var pos_left = item.roller.position().left;
  
  if(pos_left>=-110){    
    item.left.attr('disabled',1);
    item.roller.css('left',"0px");
  }
};

news = new news_roller();

