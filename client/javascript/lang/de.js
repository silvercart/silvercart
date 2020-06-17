if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('de', {
        'Boolean.NO':                                           'Nein',
        'Boolean.YES':                                          'Ja',
        'SilverCart.PleaseChoosePaymentMethod':                 'Bitte wählen Sie eine Zahlungsart!',
        'Silvercart.ShowAll':                                   'Alle anzeigen',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Diese E-Mail-Adresse existiert schon.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Aktion fehlgeschlagen!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Keine Objekte ausgewählt! Bitte wählen Sie mindestens ein Objekt aus der Liste aus.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Keine Aktion ausgewählt! Bitte wählen Sie eine Aktion aus der Liste aus.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?',
        'SilvercartProduct.ADD_TO_CART':                        'In den Warenkorb',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Menge ändern'
    });
}