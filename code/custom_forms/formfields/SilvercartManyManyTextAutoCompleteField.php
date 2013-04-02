<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 */

/**
 * ManyManyTextAutoCompleteField is an autocomplete form field for a DataObjects
 * many_many relation
 *
 * @package Silvercart
 * @subpackage Forms_FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartManyManyTextAutoCompleteField extends SilvercartHasManyTextAutoCompleteField {
    
    /**
     * Class name of the field
     *
     * @var string
     */
    protected $className = 'SilvercartManyManyTextAutoCompleteField';
    
    /**
     * Executes the common field holder routine and returns the custom
     * javascript code
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 22.06.2012
     */
    public function FieldHolderScript() {
        Requirements::javascript(SilvercartTools::getBaseURLSegment() . 'silvercart/script/SilvercartManyManyTextAutoCompleteField.js');
        return parent::FieldHolderScript();
    }
    
    /**
     * Generates the autocomplete source by the given controllers relations and
     * fieldname
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.10.2011
     */
    protected function generateAutoCompleteSource() {
        $controller = $this->getController();
        $fieldname = $this->name;
        $relations = $controller->many_many();
        foreach ($relations as $relationName => $dataObject) {
            if ($relationName == $fieldname) {
                $this->setAutoCompleteSource($dataObject);
                break;
            }
        }
    }

}