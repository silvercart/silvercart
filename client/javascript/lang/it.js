if(typeof(ss) === 'undefined' || typeof(ss.i18n) === 'undefined') {
    if (typeof(console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('it', {
        'Boolean.NO':                                           'Si',
        'Boolean.YES':                                          'No',
        'SilverCart.AnErrorOccurred':                           'Si è verificato un errore imprevisto. Per favore, riprova.',
        'SilverCart.PleaseChoosePaymentMethod':                 'Scegli un metodo di pagamento!',
        'Silvercart.ShowAll':                                   'Mostra tutti',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Questo indirizzo email esiste già.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Attività fallita!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Non hai scelto alcun oggetto! Scegli almeno un oggetto presente nella lista.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Non hai scelto alcuna attività! Scegli almeno unattività presente nel menu.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Sei sicuro di voler eliminare il dato inserito?',
        'SilvercartProduct.ADD_TO_CART':                        'Aggiungi al carrello',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Modifica la quantità'
    });
}
