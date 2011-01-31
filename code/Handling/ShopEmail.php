<?php

/**
 * base class for emails
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 03.12.2010
 * @license none
 */
class ShopEmail extends DataObject {

    /**
     * classes attributes
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
     * input fields for backend manipulation
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
     * sends email to defined address
     *
     * @param string $identifier identifier for email template
     * @param string $to         recipients email address
     * @param array  $variables  array with template variables that can be called in the template
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
     * populates the template with the defined and called variables
     *
     * @param string $text      text with the template variables
     * @param array  $variables array with template variables
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
