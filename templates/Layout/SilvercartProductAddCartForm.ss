<form class="yform" $FormAttributes>
	$CustomHtmlFormMetadata

    <fieldset>
        <legend></legend>
        $SilvercartPlugin(AddCartFormAdditionalFields)
        
        $CustomHtmlFormFieldByName(productQuantity,SilvercartProductAddCartFormField)
    </fieldset>
</form>
