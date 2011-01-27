$(document).ready(function(){


$("#quickCartLink").hover(
   function () {
    /* use callee so don't have to name the function */
      $("#quickCartBox").css({'visibility' : 'visible', 'left' : '1100px' , 'top' : '40px'});
     
      $("#quickCartBox").fadeIn(500);
  },

    function () {
    /* use callee so don't have to name the function */
    $("#quickCartBox").fadeOut(500);
  }

);
    
});
