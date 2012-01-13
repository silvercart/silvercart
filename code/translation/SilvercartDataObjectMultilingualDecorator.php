<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage translation
 */

/**
 * decorates DataObjects to make them multilingual eg SilvercartProduct
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 06.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDataObjectMultilingualDecorator extends DataObjectDecorator {
    
    protected $languageObj = null;
    
    /**
     * augments the hook of the decorated object so that the input in the fields
     * that are multilingual gets written to the related language object
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function onAfterWrite() {
         SilvercartLanguageHelper::writeLanguageObject($this->owner->getLanguage(), $this->owner->toMap());
    }
    
    /**
     * Getter for the related language object depending on the set language
     * Always returns a SilvercartProductLanguage
     *
     * @return SilvercartProductLanguage
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 06.01.2012
     */
    public function getLanguage() {
        if (is_null($this->languageObj)) {
            $relationFieldName      = $this->owner->ClassName . 'ID';
            $languageClassName      = $this->owner->ClassName . 'Language';
            $languageRelationName   = $languageClassName . 's';
            $this->languageObj = SilvercartLanguageHelper::getLanguage($this->owner->{$languageRelationName}());
            if (!$this->languageObj) {
                $this->languageObj = new $languageClassName();
                $this->languageObj->Locale = Translatable::get_current_locale();
                $this->languageObj->{$relationFieldName} = $this->owner->ID;
            }
        }
        return $this->languageObj;
    }
    
    /**
     * helper attribute for table fields
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.01.2012
     */
    public function getTableIndicator() {
        return _t('SilvercartConfig.OPEN_RECORD');
    }
}

