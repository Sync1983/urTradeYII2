/* 
 */

function login_click(item){
  var disp = $('.login-window').css('display');
  if(disp==="block")
    $('.login-window').css('display','none');
  else
    $('.login-window').css('display','block');
}

function onLogin(){
  var name = $('#user-name').val();
  var pass = $('#user-pass').val();
  
}
