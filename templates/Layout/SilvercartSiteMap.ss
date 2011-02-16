<div id="col1">
    <div id="col1_content" class="clearfix">
        <h1> Sitemap </h1>
        <p> <% _t('SilvercartPage.SITMAP_HERE','Here You can see the complete directory to our site.') %> </p>
        <br/>
        <ul class="Sitemap">
            <% control getPages %>
                <li><strong><a href="$Link">$Title</a></strong></li><br/>
            <% end_control %>
            <li><strong><% _t('SilvercartPage.CATALOG', 'catalog') %></strong></li>
        </ul>
    </div>
</div>

<div id="col2">
    <div id="col2_content" class="clearfix"></div>
</div>

<div class="subcolumns">
    <div class="SiteMap_Links">
        <% control ChildrenOf(katalog) %>
          <div <% if MultipleOf(3) %>class="c33r"<% else %>class="c33l"<% end_if %>>
             <div class="Katalog"><a href="$Link"><strong>$Title</strong></a></div>
               <% control Children %>
                   <ul>
                       <li><div class="items"><a href="$Link">$Title</a></div></li>
                   </ul>
               <% end_control %>
           </div>
        <% end_control %>
    </div>
</div>
