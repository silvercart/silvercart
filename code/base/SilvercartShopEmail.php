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
     * n:1 relations
     * 
     * @var type array
     */
    public static $has_many = array(
        'SilvercartShopEmailLanguages' => 'SilvercartShopEmailLanguage',
    );
    
    /**
     * n:m relations
     * 
     * @var type array
     */
    public static $many_many = array(
        'AdditionalReceipients' => 'SilvercartEmailAddress',
    );
    
    /**
     * n:m relations
     *
     * @var array
     */
    public static $belongs_many_many = array(
        'SilvercartOrderStatus' => 'SilvercartOrderStatus'
    );

    /**
     * classes attributes
     *
     * @var array
     */
    public static $db = array(
        'Identifier' => 'Varchar(255)'
    );

    /**
     * Casted properties
     *
     * @var array
     */
    public static $casting = array(
        'Subject'                           => 'Text',
        'EmailText'                         => 'Text',
        'AdditionalRecipientsHtmlString'    => 'HtmlText',
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }

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
     * @since 27.04.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Identifier'                    => _t('SilvercartShopEmail.IDENTIFIER'),
                'Subject'                       => _t('SilvercartShopEmail.SUBJECT'),
                'EmailText'                     => _t('SilvercartShopEmail.EMAILTEXT'),
                'AdditionalReceipients'         => _t('SilvercartShopEmail.ADDITIONALS_RECEIPIENTS'),
                'AdditionalRecipients'          => _t('SilvercartShopEmail.ADDITIONALS_RECEIPIENTS'),
                'SilvercartShopEmailLanguages'  => _t('SilvercartShopEmailLanguage.PLURALNAME'),
                'SilvercartOrderStatus'         => _t('SilvercartOrderStatus.PLURALNAME'),
            )
        );
        
        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Get the default summary fields for this object.
     *
     * @return array
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 27.04.2012
     */
    public function  summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
                    'Identifier'                        => $this->fieldLabel('Identifier'),
                    'Subject'                           => $this->fieldLabel('Subject'),
                    'AdditionalRecipientsHtmlString'    => $this->fieldLabel('AdditionalRecipients'),
                )
        );
        
        $this->extend('updateSummary', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * input fields for backend manipulation
     *
     * @return FieldList
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 28.04.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        /*
         * insert the multilingual fields and fill them with values of the current locale
         */
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguageClassName());
        $afterFieldName = 'Identifier';
        foreach ($languageFields as $languageField) {
            $fields->insertAfter($languageField, $afterFieldName);
            $afterFieldName = $languageField->getName();
        }
        
        $emailTextField = new TextareaField('EmailText', _t('SilvercartShopEmail.EMAILTEXT', 'message'), 30);
        
        $fields->removeByName('EmailText');
        $fields->insertAfter($emailTextField, 'Subject');
        
        $config = GridFieldConfig_RelationEditor::create(100);
        $orderStatusTable = new GridField(
            'SilvercartOrderStatus',
             $this->fieldLabel('SilvercartOrderStatus'),
             $this->SilvercartOrderStatus(),
             $config
        );
        $fields->findOrMakeTab('Root.SilvercartOrderStatus', $this->fieldLabel('SilvercartOrderStatus'));
        $fields->addFieldToTab('Root.SilvercartOrderStatus', $orderStatusTable);
        
        return $fields;
    }
    
    /**
     * Returns the title
     *
     * @return string
     */
    public function getEmailText() {
        return $this->getLanguageFieldValue('EmailText');
    }
    
    /**
     * Returns the Subject
     *
     * @return string
     */
    public function getSubject() {
        return $this->getLanguageFieldValue('Subject');
    }
    
    /**
     * Returns the additional email recipients as a html string
     * 
     * @return string
     */
    public function getAdditionalRecipientsHtmlString() {
        $additionalRecipientsArray = array();
        if ($this->AdditionalReceipients()->count() > 0) {
            foreach ($this->AdditionalReceipients() as $additionalRecipient) {
                $additionalRecipientsArray[] = htmlentities($additionalRecipient->getEmailAddressWithName());
            }
        }
        $additionalRecipientsString = implode('<br/>', $additionalRecipientsArray);
        return $additionalRecipientsString;
    }

    /**
     * sends email to defined address
     *
     * @param string $identifier  identifier for email template
     * @param string $to          recipients email address
     * @param array  $variables   array with template variables that can be called in the template
     * @param array  $attachments absolute filename to an attachment file
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.06.2012
     */
    public static function send($identifier, $to, $variables = array(), $attachments = null) {
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
        $emailText = trim($mailObj->EmailText);
        if (is_null($emailText) ||
            empty($emailText)) {
            return false;
        }
        $emailSubject = trim($mailObj->Subject);
        if (is_null($emailSubject) ||
            empty($emailSubject)) {
            return false;
        }

        if (!is_array($variables)) {
            $variables = array();
        }

        $templateVariables = new ArrayData($variables);
        $emailTextTemplate = new SSViewer_FromString($mailObj->EmailText);
        $emailText = HTTP::absoluteURLs($emailTextTemplate->process($templateVariables));


        $emailSubjectTemplate = new SSViewer_FromString($mailObj->Subject);
        $emailSubject         = HTTP::absoluteURLs($emailSubjectTemplate->process($templateVariables));

        $email = new Email(
            SilvercartConfig::EmailSender(),
            $to,
            $emailSubject,
            $mailObj->EmailText
        );

        $email->setTemplate('SilvercartShopEmail');
        $email->populateTemplate(
            array(
                'ShopEmailSubject' => $emailSubject,
                'ShopEmailMessage' => $emailText,
            )
        );
        
        self::attachFiles($email, $attachments);

        $email->send();
        if (SilvercartConfig::GlobalEmailRecipient() != '') {
            $email = new Email(
                SilvercartConfig::EmailSender(),
                SilvercartConfig::GlobalEmailRecipient(),
                $emailSubject,
                $mailObj->EmailText
            );

            $email->setTemplate('SilvercartShopEmail');
            $email->populateTemplate(
                array(
                    'ShopEmailSubject' => $emailSubject,
                    'ShopEmailMessage' => $emailText,
                )
            );

            $email->send();
        }

        //Send the email to additional standard receipients from the n:m
        //relation AdditionalReceipients;
        //Email address is validated.
        if ($mailObj->AdditionalReceipients()->count() > 0) {
            foreach ($mailObj->AdditionalReceipients() as $additionalReceipient) {
                if ($additionalReceipient->getEmailAddressWithName() && Email::validEmailAddress($additionalReceipient->Email)) {
                    $to = $additionalReceipient->getEmailAddressWithName();
                } elseif ($additionalReceipient->getEmailAddress() && Email::validEmailAddress($additionalReceipient->Email)) {
                    $to = $additionalReceipient->getEmailAddress();
                } else {
                    continue;
                }
                $email = new Email(
                    SilvercartConfig::EmailSender(),
                    $to,
                    $emailSubject,
                    $mailObj->EmailText
                    );
                $email->setTemplate('SilvercartShopEmail');
                $email->populateTemplate(
                array(
                    'ShopEmailSubject' => $emailSubject,
                    'ShopEmailMessage' => $emailText,
                    )
                );
                self::attachFiles($email, $attachments);
                $email->send();
            }
        }
    }
    
    /**
     * Attaches the given files to the given email.
     *
     * @param Email $email       Email
     * @param array $attachments Attachments
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.08.2011
     */
    protected static function attachFiles(Email $email, $attachments) {
        if (!is_null($attachments)) {
            if (is_array($attachments)) {
                foreach ($attachments as $attachment) {
                    if (is_array($attachment)) {
                        $filename           = $attachment['filename'];
                        $attachedFilename   = array_key_exists('attachedFilename', $attachment) ? $attachment['attachedFilename'] : basename($filename);
                        $mimetype           = array_key_exists('mimetype', $attachment) ? $attachment['mimetype'] : null;
                    } else {
                        $filename           = $attachment;
                        $attachedFilename   = basename($attachment);
                        $mimetype           = null;
                    }
                    $email->attachFile($filename, $attachedFilename, $mimetype);
                }
            } else {
                $email->attachFile($attachments, basename($attachments));
            }
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
    
    /**
     * The given $content will be parsed with a reduced variant of the SilverStripe
     * default template parsing engine to get localized email templates. Parsing 
     * the template like that keeps other template mechanisms like <% if ... %>
     * or <% control ... %> alive to be parsed on processing the real email 
     * sending.
     *
     * @param string $content The content to parse
     * 
     * @return string
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 01.07.2011
     */
    public static function parse($content) {
        // i18n _t(...)
        $plainPattern = '<' . '% +_t\((\'([^\']*)\'|"([^"]*)")(([^)]|\)[^ ]|\) +[^% ])*)\) +%' . '>';
        $pattern = '/' . $plainPattern . '/';
        preg_match_all($pattern, $content, $matches);
        if (is_array($matches[0])) {
            foreach ($matches[0] as $index => $match) {
                $content = str_replace($match, _t($matches[2][$index]), $content);
            }
        }        
        return $content;
    }

}
