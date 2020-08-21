if (typeof (ss) === 'undefined' || typeof (ss.i18n) === 'undefined') {
    if (typeof (console) !== 'undefined') {
        console.error('Class ss.i18n not defined');
    }
} else {
    ss.i18n.addDictionary('fr', {
        'Boolean.NO':                                           'Non',
        'Boolean.YES':                                          'Oui',
        'Silvercart.ShowAll':                                   'Afficher tous',
        'SilvercartRegistrationPage.EMAIL_EXISTS_ALREADY':      'Cette adresse email existe déjà.',
        'SilvercartGridFieldBatchController.BATCH_FAILED':      'Action par lots a échoué!',
        'SilvercartGridFieldBatchController.NO_ENTRY_SELECTED': 'Aucun objet n\'a été sélectionné! Sélectionne un objet à importer.',
        'SilvercartGridFieldBatchController.NO_ACTION_SELECTED':'Aucune action n\'a été sélectionnée! Sélectionnez une action à effectuer.',
        'TABLEFIELD.DELETECONFIRMMESSAGE':                      'Êtes-vous sûr de vouloir supprimer ces entrées?',
        'SilvercartProduct.ADD_TO_CART':                        'Ajouter au panier',
        'SilvercartProduct.CHANGE_QUANTITY_CART':               'Modifier la quantité'
    });
}