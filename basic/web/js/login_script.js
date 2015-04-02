
function login_click(item){
  var disp = $('#navbar-login').css('display');
  if(disp==="block")
    $('#navbar-login').css('display','none');
  else
    $('#navbar-login').css('display','block');
}