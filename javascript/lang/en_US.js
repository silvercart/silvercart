if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
    //console.error('Class ss.i18n not defined');
} else {
    ss.i18n.addDictionary('en_US', {
        'Boolean.NO':                                           'No',
        'Boolean.YES':                                          'Yes',
        'Silvercart.ShowAll':                                   'Show all',
        'SilvercartConfig.ADDED_EXAMPLE_DATA':                  'Added Example Data',
        'SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION':         'Added Example Configuration',
        'SilvercartConfig.EXAMPLE_DATA_ALREADY_ADDED':          'Example Data already added',
        'SilvercartConfig.EXAMPLE_CONFIGURATION_ALREADY_ADDED': 'Example Configuration already added',
        'SilvercartConfig.CLEANED_DATABASE':                    'Database was optimized',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'This email address already exists.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batch action failed!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'No objects selected! Please select at least one object entry.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'No action selected! Please select an action to execute.',
        'SilvercartProduct.ADD_TO_CART':                        'Add to cart',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Change quantity',
        
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Are you sure you want to delete this entry?'
    });
}
