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
 * @package SilverCart
 * @subpackage update
 */

/**
 * Prepares objects for the new multilingual feature
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.02.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartUpdate1_3__1 extends SilvercartUpdate {
    
    public static $defaults = array(
        'SilvercartVersion'         => '1.3',
        'SilvercartUpdateVersion'   => '1',
        'Description'               => 'This update adjust all multilingual objects to the new multilingual feature.'
    );
    
    /**
     * Executes the update logic.
     *
     * @return boolean true
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 03.02.2012
     */
    public function executeUpdate() {
        $this->updateMultilingualObject('SilvercartOrderStatus');
        $this->updateMultilingualObject('SilvercartProduct', array('MetaTitle', 'Title', 'MetaKeywords', 'MetaDescription', 'ShortDescription', 'LongDescription'));
        $this->updateMultilingualObject('SilvercartShippingMethod');
        $this->updateMultilingualObject('SilvercartQuantityUnit');
        $this->updateMultilingualObject('SilvercartProductCondition');
        $this->updateMultilingualObject('SilvercartZone');
        $this->updateMultilingualObject('SilvercartFile', array('Title', 'Description'));
        $this->updateMultilingualObject('SilvercartImage');
        $this->updateMultilingualObject('SilvercartImageSliderWidget', array('FrontTitle', 'FrontContent'));
        $this->updateMultilingualObject('SilvercartLatestBlogPostsWidget', array('WidgetTitle'));
        $this->updateMultilingualObject('SilvercartProductGroupItemsWidget', array('FrontTitle', 'FrontContent'));
        $this->updateMultilingualObject('SilvercartTextWidget', array('FreeText'));
        return true;
    }
    
    /**
     * encapsulate all updates regarding the multilingual feature
     * 
     * @param string $className  the class to update
     * @param array  $attributes an array of attributes
     *
     * @return void 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 05.02.2012
     */
    public function updateMultilingualObject($className, $attributes = array('Title')) {
        //SilvercartOrderStatus
        $objects = DataObject::get($className);
        if ($objects) {
            $relationName = $className . "Languages";
            $languageClassName = $className . "Language";
            foreach ($objects as $object) {
                $languageClass = new $languageClassName();
                $languageClass->Locale = Translatable::get_current_locale();
                foreach ($attributes as $attribute) {
                    $languageClass->{$attribute} = $object->{$attribute};
                }
                $languageClass->write();
                $object->{$relationName}()->add($languageClass);
            }
            foreach ($attributes as $attribute) {
                $sql = sprintf("ALTER TABLE %s DROP %s", $className, $attribute);
                DB::query($sql);
            }
        }
    }
}

