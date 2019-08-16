<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Admin\Forms\GridField\GridFieldConfig_RelationEditor;
use SilverCart\Model\Product\File;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use SilverStripe\ORM\HasManyList;

/**
 * DownloadPage.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @method HasManyList Files() Returns the related Files.
 */
class DownloadPage extends \Page
{
    use \SilverCart\ORM\ExtensibleDataObject;
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartDownloadPage';
    /**
     * 1:n relations
     *
     * @var array
     */
    private static $has_many = [
        'Files' => File::class,
    ];

    /**
     * returns the singular name
     * 
     * @return string
     */
    public function singular_name() : string
    {
        return Tools::singular_name_for($this);
    }
    
    /**
     * returns the plural name
     * 
     * @return string
     */
    public function plural_name() : string
    {
        return Tools::plural_name_for($this);
    }
    
    /**
     * adds a Files Tab to the page with a GridField
     * 
     * @return FieldList
     */
    public function getCMSFields() : FieldList
    {
        $this->beforeUpdateCMSFields(function(FieldList $fields) {
            $fileField = GridField::create(
                    'Files',
                    $this->fieldLabel('Files'),
                    $this->Files(),
                    GridFieldConfig_RelationEditor::create()
            );
            $fileField->getConfig()->removeComponentsByType(GridFieldAddExistingAutocompleter::class);
            $fields->findOrMakeTab('Root.Files', $this->fieldLabel('Files'));
            $fields->addFieldToTab('Root.Files', $fileField);
        });
        return parent::getCMSFields();
    }
    
    /**
     * fieldLabels method
     * 
     * @param bool $includerelations A boolean value to indicate if the labels returned include relation fields
     * 
     * @return array 
     * 
     * @author Patrick Schneider <pschneider@pixeltricks.de>
     * @since 12.07.2012
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Files'   => File::singleton()->plural_name(),
        ]);
    }
}