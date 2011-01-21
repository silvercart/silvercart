$(document).ready(function(){
    $("#quickCartLink").hover(
        function () {
            $("#quickCartBox").css({'visibility' : 'visible', 'left' : '1100px' , 'top' : '40px'});

            $("#quickCartBox").fadeIn(500);
        },
        function () {
            $("#quickCartBox").fadeOut(500);
        }
    );
});
