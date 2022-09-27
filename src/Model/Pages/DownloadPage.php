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
 * @subpackage Model\Pages
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
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-p-download';
    
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
     */
    public function fieldLabels($includerelations = true) : array
    {
        return $this->defaultFieldLabels($includerelations, [
            'Files'   => File::singleton()->plural_name(),
        ]);
    }
}