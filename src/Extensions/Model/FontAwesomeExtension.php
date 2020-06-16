<?php

namespace SilverCart\Extensions\Model;

use BucklesHusky\FontAwesomeIconPicker\Forms\FAPickerField;
use SilverCart\Dev\Tools;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * Extension for objects with themed fontawesome icons.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.06.2020
 * @copyright 2020 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\ORM\DataObject $owner Owner
 */
class FontAwesomeExtension extends DataExtension
{
    /**
     * DB attributes
     *
     * @var array
     */
    private static $db = [
        'FontAwesomeIcon' => 'Varchar(50)',
    ];
    
    /**
     * Updates the field labels.
     * 
     * @param array &$labels Labels to update
     * 
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, Tools::field_labels_for(self::class));
    }
    
    /**
     * Updates the CMS fields.
     * 
     * @param FieldList $fields Fields to update
     * 
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        if (class_exists(FAPickerField::class)) {
            $fields->removeByName('FontAwesomeIcon');
            $fields->insertAfter('Title', FAPickerField::create('FontAwesomeIcon', $this->owner->fieldLabel('FontAwesomeIcon'))
                    ->setDescription($this->owner->fieldLabel('FontAwesomeIconDesc'))
                    ->setRightTitle($this->owner->fieldLabel('FontAwesomeIconRightTitle'))
            );
        } else {
            $fields->dataFieldByName('FontAwesomeIcon')
                    ->setDescription($this->owner->fieldLabel('FontAwesomeIconDesc'))
                    ->setRightTitle($this->owner->fieldLabel('FontAwesomeIconRightTitle'));
        }
    }
    
    /**
     * Updates the summary fields.
     * 
     * @param array &$fields Fields to update
     * 
     * @return void
     */
    public function updateSummaryFields(&$fields) : void
    {
        $fields = [
            'FontAwesomeIconHTML' => '',
        ] + $fields;
    }
    
    /**
     * Returns the font awesome icon as complete HTML code.
     * 
     * @param string $cssClasses Optional CSS classes
     * 
     * @return DBHTMLText
     */
    public function FontAwesomeIconHTML(string $cssClasses = '') : DBHTMLText
    {
        return $this->owner->renderWith(self::class, [
            'CSSClasses' => $cssClasses,
        ]);
    }
}