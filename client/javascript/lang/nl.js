if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('nl', {
        'Boolean.NO':                                           'No',
        'Boolean.YES':                                          'Yes',
        'Silvercart.ShowAll':                                   'Show all',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'This email address already exists.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batch action failed!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'No objects selected! Please select at least one object entry.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'No action selected! Please select an action to execute.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Weet u zeker dat u dit bestand wilt verwijderen?'
    });
}
