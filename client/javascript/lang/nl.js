if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('nl', {
        'Boolean.NO':                                           'Nee',
        'Boolean.YES':                                          'Ja',
        'SilverCart.AnErrorOccurred':                           'Een fout is opgetreden. Probeer opnieuw.',
        'SilverCart.PleaseChoosePaymentMethod':                 'Kies een betaalmethode.',
        'Silvercart.ShowAll':                                   'alles weergeven',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Dit mailadres bestaat al.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Batch actie mislukt!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Geen objecten geselecteerd! Selecteer minstens één object.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Geen actie geselecteerd! Selecteer minstens één actie om uit te voeren.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Weet u zeker dat u dit item wilt verwijderen?',
        'SilvercartProduct.ADD_TO_CART':                        'Voeg toe aan winkelwagen',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Wijzig aantal'
    });
}
