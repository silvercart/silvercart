<?php

namespace SilverCart\Admin\Forms\GridField;

use SilverStripe\Core\Config\Config;
use SilverCart\ORM\DataList;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter as SilverStripeGridFieldAddExistingAutocompleter;

/**
 * This class is is responsible for adding objects to another object's has_many 
 * and many_many relation, as defined by the {@link RelationList} passed to the 
 * GridField constructor.
 * Objects can be searched through an input field (partially matching one or 
 * more fields).
 * Selecting from the results will add the object to the relation.
 * Often used alongside {@link GridFieldRemoveButton} for detaching existing 
 * records from a relatinship.
 * For easier setup, have a look at a sample configuration in 
 * {@link GridFieldConfig_RelationEditor}.
 *
 * @package SilverCart
 * @subpackage Admin_Forms_GridField_Components
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 22.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class GridFieldAddExistingAutocompleter extends SilverStripeGridFieldAddExistingAutocompleter {

    /**
     * Detect searchable fields and searchable relations
     * Only has_many relations may be searched.
     * Falls back to Title or Name if no earchableFields are defined.
     *
     * @param string $dataClass The class name to get fields for
     * 
     * @return array
     * 
     * @return array|null
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.04.2018
     */
    public function scaffoldSearchFields($dataClass) {
        $fields            = parent::scaffoldSearchFields($dataClass);
        $has_many          = Config::inst()->get($dataClass, 'has_many');
        $many_many         = Config::inst()->get($dataClass, 'many_many');
        $belongs_many_many = Config::inst()->get($dataClass, 'belongs_many_many');
        
        foreach ($fields as $key => $value) {
            if (strpos($value, '.') !== false) {
                $parts        = explode('.', $value, 2);
                $relationName = $parts[0];
                if (is_array($has_many) && array_key_exists($relationName, $has_many)) {
                    unset($fields[$key]);
                    $fields[] = $value;
                } elseif (is_array($many_many) && array_key_exists($relationName, $many_many)) {
                    unset($fields[$key]);
                } elseif (is_array($belongs_many_many) && array_key_exists($relationName, $belongs_many_many)) {
                    unset($fields[$key]);
                }
            }
        }
        
        $fields = array_values($fields);
        
        return $fields;
    }

    /**
     * Disables the linear DataList sort to support searching for Translation objects.
     *
     * @param \SilverStripe\Forms\GridField\GridField $gridField Grid field
     * @param \SilverStripe\Control\HTTPRequest       $request   Request
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2018
     */
    public function doSearch($gridField, $request) {
        DataList::set_do_linear_sort(false);
        $result = parent::doSearch($gridField, $request);
        DataList::set_do_linear_sort(true);
        return $result;
    }
}