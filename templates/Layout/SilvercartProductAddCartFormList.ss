<form class="yform" $FormAttributes>
	$CustomHtmlFormMetadata

    <fieldset>
        <legend></legend>
        $SilvercartPlugin(AddCartFormListAdditionalFields)
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
    </fieldset>
</form>
