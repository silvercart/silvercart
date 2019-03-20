if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('en', {
        'Boolean.NO':                                           'No',
        'Boolean.YES':                                          'Yes',
        'Silvercart.ShowAll':                                   'Show all',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'This email address already exists.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batch action failed!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'No objects selected! Please select at least one object entry.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'No action selected! Please select an action to execute.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Are you sure you want to delete this entry?',
        'SilvercartProduct.ADD_TO_CART':                        'Add to cart',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Change quantity'
    });
}
