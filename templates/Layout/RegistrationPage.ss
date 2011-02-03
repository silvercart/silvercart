<div id="col1">
    <div id="col1_content" class="clearfix">
        <% include BreadCrumbs %>
        <h2>$Title</h2>
        $Content
        $Form
        $PageComments
        <% if CurrentMember %>
        <p>
            <% sprintf(_t('Page.ALREADY_REGISTERED','Hello %s, You have already registered.'),$CurrentMember.FirstName) %>
        </p>
        <% else %>
        $InsertCustomHtmlForm(RegisterRegularCustomerForm)
        <% end_if %>
    </div>
</div>
<div id="col3">
    <div id="col3_content" class="clearfix">
            <% include SideBarCart %>
    </div>
    <div id="ie_clearing"> &#160; </div>
</div>