<form class="form" $FormAttributes >
	$CustomHtmlFormMetadata
	<% loop Actions %>
    <button class="btn" id="{$ID}" name="{$Name}" value="{$description}" type="submit" data-toggle="tooltip" data-placement="top" title="{$description}" data-title="{$description}">
      <i class="icon-minus"></i>
    </button>
	<% end_loop %>

    $CustomHtmlFormSpecialFields
</form>
