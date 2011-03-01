<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Base
 */

/**
 * base class for emails
 *
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 03.12.2010
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopEmail extends DataObject {

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
        'Identifier'    => 'Varchar(255)',
        'Subject'       => 'Varchar(255)',
        'EmailText'     => 'Text',
        'Variables'     => 'Text'
    );

    /**
     * Get any user defined searchable fields labels that
     * exist. Allows overriding of default field names in the form
     * interface actually presented to the user.
     *
     * The reason for keeping this separate from searchable_fields,
     * which would be a logical place for this functionality, is to
     * avoid bloating and complicating the configuration array. Currently
     * much of this system is based on sensible defaults, and this property
     * would generally only be set in the case of more complex relationships
     * between data object being required in the search interface.
     *
     * Generates labels based on name of the field itself, if no static property
     * {@link self::field_labels} exists.
     *
     * @param boolean $includerelations a boolean value to indicate if the labels returned include relation fields
     *
     * @return array|string Array of all element labels if no argument given, otherwise the label of the field
     *
     * @uses $field_labels
     * @uses FormField::name_to_label()
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.02.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = parent::fieldLabels($includerelations);
        $fieldLabels['Identifier']  = _t('SilvercartShopEmail.IDENTIFIER', 'identifier');
        $fieldLabels['Subject']     = _t('SilvercartShopEmail.SUBJECT', 'subject');
        $fieldLabels['EmailText']   = _t('SilvercartShopEmail.EMAILTEXT', 'message');
        $fieldLabels['Variables']   = _t('SilvercartShopEmail.VARIABLES', 'variables');
        return $fieldLabels;
    }

    /**
     * Get the default summary fields for this object.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 16.02.2011
     */
    public function  summaryFields() {
        $summaryFields = parent::summaryFields();
        $summaryFields['Identifier'] = _t('SilvercartShopEmail.IDENTIFIER', 'identifier');
        $summaryFields['Subject']    = _t('SilvercartShopEmail.SUBJECT', 'subject');
        return $summaryFields;
    }

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
            'SilvercartShopEmail',
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
            SilvercartConfig::EmailSender(),
            $to,
            $mailObj->Subject,
            $mailObj->EmailText
        );

        $email->setTemplate('SilvercartShopEmail');
        $email->populateTemplate(
            array(
                'ShopEmailSubject' => $mailObj->Subject,
                'ShopEmailMessage' => $emailText,
            )
        );

        $email->send();
        if (SilvercartConfig::GlobalEmailRecipient() != '') {
            $email = new Email(
                SilvercartConfig::EmailSender(),
                SilvercartConfig::GlobalEmailRecipient(),
                $mailObj->Subject,
                $mailObj->EmailText
            );

            $email->setTemplate('SilvercartShopEmail');
            $email->populateTemplate(
                array(
                    'ShopEmailSubject' => $mailObj->Subject,
                    'ShopEmailMessage' => $emailText,
                )
            );

            $email->send();
        }
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
