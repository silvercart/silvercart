<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of the SilverStripe modul FormFieldTools.
 *
 * FormFieldTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FormFieldTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FormFieldTools. If not, see <http://www.gnu.org/licenses/>.
 * 
 * @package Silvercart
 * @subpackage FormFields
 */

/**
 * ManyManyTextAutoCompleteField is an autocomplete form field for a DataObjects
 * many_many relation
 *
 * @package Silvercart
 * @subpackage FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 05.10.2011
 * @license see license file in modules root directory
 * @copyright 2011 pixeltricks GmbH
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