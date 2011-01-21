<?php

/**
 * Confirmation page for Closed-Opt-In
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @license BSD
 * @since 20.10.2010
 */
class RegisterConfirmationPage extends Page {

    public static $singular_name = "Registrierungsbestätigungsseite";
    public static $db = array(
        'ConfirmationMailSubject' => 'Varchar(255)',
        'ConfirmationMailMessage' => 'HTMLText',
        'ConfirmationFailureMessage' => 'HTMLText',
        'ConfirmationSuccessMessage' => 'HTMLText',
        'AlreadyConfirmedMessage' => 'HTMLText'
    );

    /**
     * Return all fields of the backend
     *
     * @return FieldSet Fields of the CMS
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $confirmationMailSubjectField = new TextField('ConfirmationMailSubject', 'Bestätigungsmail: Betreff');
        $confirmationMailTextField = new HtmlEditorField('ConfirmationMailMessage', 'Bestätigungsmail: Nachricht', 20);
        $confirmationFailureMessageTextField = new HtmlEditorField('ConfirmationFailureMessage', 'Fehlernachricht', 20);
        $confirmationSuccessMessageTextField = new HtmlEditorField('ConfirmationSuccessMessage', 'Erfolgsnachricht', 20);
        $alreadyConfirmedMessageTextField = new HtmlEditorField('AlreadyConfirmedMessage', 'Nachricht, wenn User schon aktiviert ist', 20);

        $fields->addFieldToTab('Root.Content.Main', $confirmationFailureMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $confirmationSuccessMessageTextField);
        $fields->addFieldToTab('Root.Content.Main', $alreadyConfirmedMessageTextField);
        $fields->addFieldToTab('Root.Content.ConfirmationMail', $confirmationMailSubjectField);
        $fields->addFieldToTab('Root.Content.ConfirmationMail', $confirmationMailTextField);

        return $fields;
    }
}

/**
 * Controller of this page type
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 19.10.2010
 * @copyright 2010 pixeltricks GmbH
 */
class RegisterConfirmationPage_Controller extends Page_Controller {

    /**
     * statments to be  executed on initialisation
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 19.10.2010
     * @return void
     */
    public function init() {
        parent::init();
    }

    /**
     * Final step for customer registration
     *
     * @return array message for the template
     * @author Sascha Köhler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     */
    public function doConfirmation() {
        $statusMessage = $this->ConfirmationFailureMessage;

        if (isset($_GET['h'])) {
            $hash = Convert::raw2sql(urldecode(mysql_real_escape_string($_GET['h'])));

            if ($hash) {
                $customer = DataObject::get_one(
                                'Member',
                                'ConfirmationHash LIKE \'' . $hash . '\''
                );

                // Hash wurde gefunden, also das Bestaetigungsdatum speichern.
                // Die Umtragung in die entsprechende Kundengruppe erfolgt automatisch
                // im entsprechenden Customer-Objekt.
                //
                // Dem Kunde wird eine endgueltige Bestaetigungsmail geschickt.
                if ($customer) {
                    if ($customer->OptInStatus == 1) {
                        $statusMessage = $this->AlreadyConfirmedMessage;
                    } else {
                        $customer->setField('ConfirmationDate', date('Y-m-d H:i:s', mktime()));
                        $customer->setField('OptInStatus', true);
                        $customer->write();

                        // Remove customer from intermediate group
                        $customerGroup = DataObject::get_one(
                                        'Group',
                                        'ID=6'
                        );
                        $customer->Groups()->remove($customerGroup);

                        // Add customer to group with confirmed members
                        $customerGroup = DataObject::get_one(
                                        'Group',
                                        'ID=5'
                        );
                        $customer->Groups()->add($customerGroup);
                        $customer->logIn();

                        $this->sendConfirmationMail($customer);
                        $statusMessage = $this->ConfirmationSuccessMessage;
                    }
                }
            }
        }

        return $this->customise(array(
            'message' => $statusMessage
        ));
    }

    /**
     * sendOptInMail
     *
     * @param Member $customer receiver of the email
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.10.2010
     * @return void
     */
    public function sendConfirmationMail($customer) {
        $email = new Email(
                        Email::getAdminEmail(),
                        $customer->Email,
                        $this->ConfirmationMailSubject,
                        ''
        );

        $message = str_replace(
                        array(
                            '__SALUTATION__',
                            '__SURNAME__',
                            '__EMAIL__'
                        ),
                        array(
                            $customer->Salutation,
                            $customer->Surname,
                            $customer->Email
                        ),
                        $this->ConfirmationMailMessage
        );

        $email->setTemplate('MailRegistrationConfirmation');
        $email->populateTemplate(
                array(
                    'message' => $message
                )
        );

        $email->send();
    }
}
