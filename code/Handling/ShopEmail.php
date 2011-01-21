<?php

/**
 * Die Basisklasse fuer Emails
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 03.12.2010
 * @license none
 */
class ShopEmail extends DataObject {

    /**
     * Definiert die Attribute der Klasse.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    public static $db = array(
        'Identifier' => 'Varchar(255)',
        'Subject' => 'Varchar(255)',
        'EmailText' => 'HTMLText',
        'Variables' => 'Text'
    );

    /**
     * Liefert die Eingabefelder zum Bearbeiten des Datensatzes.
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 13.12.2010
     */
    public function getCMSFields_forPopup() {
        $fields = parent::getCMSFields_forPopup();

        return $fields;
    }

    /**
     * Sendet eine Nachricht an die angegebene Email-Adresse.
     *
     * @param string $identifier Bezeichner, der angibt, welche Nachricht gesendet werden soll
     * @param string $to         Die Email-Adresse, an die die Nachricht gesendet werden soll
     * @param array  $variables  Assoziatives Array mit Variablen, auf die im Template zugegriffen werden kann
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 06.12.2010
     */
    public static function send($identifier, $to, $variables = array()) {
        $mailObj = DataObject::get_one(
                        'ShopEmail',
                        sprintf(
                                "\"Identifier\" = '%s'",
                                $identifier
                        )
        );

        if (!$mailObj) {
            return false;
        }

        if (!is_array($variables)) {
            $variables = array();
        }

        $templateVariables = new ArrayData($variables);
        $emailTextTemplate = new SSViewer_FromString($mailObj->EmailText);
        $emailText = HTTP::absoluteURLs($emailTextTemplate->process($templateVariables));
        $email = new Email(
                        Email::getAdminEmail(),
                        $to,
                        $mailObj->Subject,
                        $mailObj->EmailText
        );

        $email->setTemplate('ShopEmail');
        $email->populateTemplate(
                array(
                    'ShopEmailMessage' => $emailText
                )
        );

        $email->send();
    }

    /**
     * Ersetzt Platzhalter in dem uebergebenen Text mit den angegebenen
     * Variablen und gibt den geparsten Text zurueck.
     *
     * @param string $text      Der Text mit Platzhalten.
     * @param array  $variables Assoziatives Array mit Variablen, auf die im Text zugegriffen werden kann
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 06.12.2010
     */
    public static function populateTemplate($text, $variables) {

        if (!is_array($variables)) {
            return $text;
        }

        foreach ($variables as $placeholder => $value) {
            $text = str_replace('$' . $placeholder . '$', $value, $text);
        }

        return $text;
    }

}
