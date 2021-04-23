if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('nl_BE', {
        'Boolean.NO':                                           'Nee',
        'Boolean.YES':                                          'Ja',
        'SilverCart.AnErrorOccurred':                           'Er is een fout opgetreden. Gelieve opnieuw te proberen.',
        'SilverCart.PleaseChoosePaymentMethod':                 'Kies een betaalmethode!',
        'Silvercart.ShowAll':                                   'Alles tonen',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Dit e-mailadres bestaat reeds.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batchactie mislukt!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Geen objecten geselecteerd! Selecteer ten minste één object.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Geen actie geselecteerd! Gelieve een actie te selecteren om uit te voeren.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Bent u zeker dat u dit item wilt verwijderen?',
        'SilvercartProduct.ADD_TO_CART':                        'Toevoegen aan winkelwagentje',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Hoeveelheid veranderen'
    });
}
