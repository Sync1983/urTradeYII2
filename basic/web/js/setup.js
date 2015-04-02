
$("p.viewer").children("span").click(function(e){
  var target = e.target;
  $(target).hide();
  $(target).parent().children("input").show();
  $(target).parent().children("input").focus();
});

$("p.viewer").children("input").blur(function(e){
  var target = e.target;
  $(target).hide();
  $(target).parent().children("span").show();
});

function add_row(name){
  var body = $("."+name).children("tbody");
  var elem = '<tr><td><input type="text" name="name[]" placeholder="Введите имя"</td><td><input type="number" name="value[]" min=0 max=1000 placeholder="Введите наценку в %"></td><td><a href="" class="btn btn-danger" onclick="del_row(this); return false">&#x232B;</a></td></tr>';
  body.append(elem);  
}

function del_row(item){
  var row = $(item).parent().parent();
  row.remove();
  return false;
}


