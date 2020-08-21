if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('nl', {
        'Boolean.NO':                                           'Nee',
        'Boolean.YES':                                          'Ja',
        'Silvercart.ShowAll':                                   'Toon alle',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Dit e-mailadres bestaat al.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batchgewijze actie mislukt!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Er werden geen objecten geselecteerd! Selecteer ten minste een invoerobject.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Er werd geen actie geselecteerd! Selecteer een actie om uit te voeren.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Bent u zeker dat u deze invoer wilt verwijderen?',
        'SilvercartProduct.ADD_TO_CART':                        'Aan winkelwagen toevoegen',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Hoeveelheid aanpassen'
    });
}
