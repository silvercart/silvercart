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
     
  <a class="button" id="register" href="$PageByIdentifierCode(SilvercartRegistrationPage).Link"><% _t('SilvercartPage.REGISTER', 'Register') %></a>
  <a class="button" id="login"><% _t('SilvercartPage.LOGIN', 'Login') %></a>

<div id="modalLogin">
    <div class="hBox">
        <div class="LoginPopup">
            <div id="Search_Login">$InsertCustomHtmlForm(SilvercartQuickLogin)</div>
        </div>
    </div>
</div>
