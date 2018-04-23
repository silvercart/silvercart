<% if $IncludeFormTag %>
<form {$AttributesHTML}>
<% end_if %>
<% include SilverCart/Forms/CustomFormMessages %>
<% loop $HiddenFields %>
    {$Field}
<% end_loop %>
    <h4><%t SilverCart\Model\Shipment\ShippingMethod.SINGULARNAME 'Shipping method' %></h4>
    {$Fields.dataFieldByName(ShippingMethod).FieldHolder}
    <hr>
    <div class="clearfix">
<% loop $Actions %>
        <button class="btn btn-small btn-primary pull-right" type="submit" id="{$ID}" title="{$Title}" value="{$Value}" name="{$Name}">{$Title} <i class="icon icon-caret-right"></i></button>
<% end_loop %>
    </div>
<% if $IncludeFormTag %>
</form>
<% end_if %>