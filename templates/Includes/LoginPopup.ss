<script language="javascript" type="text/javascript">
$(document).ready(function() {
    jQuery.fn.ShowHide = function() {
        $('.hBox').slideToggle("slow");
    };
    
    $('#login').click(function(){
            $(this).ShowHide();
    });
});
</script>
     
  <a class="button" id="register" href="/registrierung/">Registrieren</a>
  <a class="button" id="login">Login</a>

<div id="modalLogin">
    <div class="hBox">
        <div class="LoginPopup">
            <div id="Search_Login">$InsertCustomHtmlForm(QuickLogin)</div>
        </div>
    </div>
</div>