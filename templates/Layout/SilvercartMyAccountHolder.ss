 <% if isRegisteredCustomer %>

    <div id="col1">
        <div id="col1_content" class="clearfix">
            <% include SilvercartBreadCrumbs %>
                $Content
        </div>
    </div>
    <div id="col3">
        <div id="col3_content" class="clearfix">
            $SubNavigation
            <% include SilvercartSideBarCart %>
        </div>
        <div id="ie_clearing"> &#160; </div>
    </div>

<% else %>

    <div id="col4">
        <div id="col4_content" class="clearfix">
            <% include SilvercartBreadCrumbs %>

            <div class="subcolumns">
                <div class="subcl">
                    <div class="c50l">
                        <h2>Sie haben schon ein Konto?</h2>
                        $InsertCustomHtmlForm(SilvercartLoginForm)
                    </div>
                </div>
                <div class="subcr">
                    <div class="c50r">
                        <h2>Wollen Sie sich registrieren?</h2>
                    </div>
                </div>
            </div>
        </div>
</div>

<% end_if %>
