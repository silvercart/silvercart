<style>
    .colorscheme-label {
        display: block;
        cursor: pointer;
        border: 1px solid #eee;
        margin-bottom: 4px;
        padding: 4px;
    }
    .colorscheme-radio {
        display: inline-block;
        float: left;
        border: 1px solid transparent;
        height: 40px;
        line-height: 40px;
        width: 90px;
    }
    .colorscheme-title {
        display: inline-block;
        border: 1px solid transparent;
        height: 20px;
        width: 60px;
        line-height: 20px;
    }
    .colorscheme-color {
        display: inline-block;
        margin-left: 4px;
        border: 1px solid #ddd;
        width: 60px;
        height: 20px;
        line-height: 20px;
    }
    .colorscheme-color + .colorscheme-color {
        margin-left: 0px;
        border-left: 0px;
        border-right: 0px;
    }
    .colorscheme-color:first-child {
        border-right: 0px;
    }
    .colorscheme-color:last-child {
        border-right: 1px;
    }
</style>
<% loop ColorSchemes %>
<label class="colorscheme-label">
    <span class="colorscheme-radio"><input type="radio" name="ColorScheme" value="{$Name}" <% if IsActive %>checked="checked"<% end_if %>/> {$Title}</span>
    <% loop BackgroundColors %>
    <span class="colorscheme-color" style="background-color: {$Color};">&nbsp;</span>
    <% end_loop %>
    <br/>
    <% loop FontColors %>
    <span class="colorscheme-color" style="background-color: {$Color};">&nbsp;</span>
    <% end_loop %>
</label>
<% end_loop %>