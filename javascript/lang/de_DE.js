if(typeof(ss) == 'undefined' || typeof(ss.i18n) == 'undefined') {
    //console.error('Class ss.i18n not defined');
} else {
    ss.i18n.addDictionary('de_DE', {
        'Boolean.NO':                                           'Nein',
        'Boolean.YES':                                          'Ja',
        'SilvercartConfig.ADDED_EXAMPLE_DATA':                  'Beispieldaten wurden hinzugefügt',
        'SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION':         'Beispielkonfiguration wurde angelegt',
        'SilvercartConfig.EXAMPLE_DATA_ALREADY_ADDED':          'Beispieldaten wurden bereits hinzugefügt',
        'SilvercartConfig.EXAMPLE_CONFIGURATION_ALREADY_ADDED': 'Beispielkonfiguration wurde bereits angelegt',
        'SilvercartConfig.CLEANED_DATABASE':                    'Die Datenbank wurde optimiert',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Diese E-Mail-Adresse existiert schon.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Aktion fehlgeschlagen!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Keine Objekte ausgewählt! Bitte wählen Sie mindestens ein Objekt aus der Liste aus.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Keine Aktion ausgewählt! Bitte wählen Sie eine Aktion aus der Liste aus.',
        
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Sind Sie sicher, dass Sie diesen Eintrag löschen wollen?'
    });
}