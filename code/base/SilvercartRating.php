<?php
/**
 * Copyright 2011 pixeltricks GmbH
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
 * abstract for a rating;
 * A rating may belong to a product or the store in general.
 * Decorate this class or extend it if you want to implement third party ratings.
 *
 * @package Silvercart
 * @subpackage Base
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 08.09.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartRating extends DataObject {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'RatingText'  => 'Text',
        'RatingGrade' => 'Decimal'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct',
        'Customer'          => 'Member'
    );
    
    /**
     * Default sort field and direction
     *
     * @var string
     */
    public static $default_sort = "Created DESC";

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
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.09.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
            'RatingText'        => _t('SilvercartRating.TEXT'),
            'RatingGrade'       => _t('SilvercartRating.GRADE'),
            'SilvercartProduct' => _t('SilvercartProduct.SINGULARNAME'),
            'Customer'          => _t('Member.SINGULARNAME')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Summaryfields for display in tables.
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.09.2011
     */
    public function summaryFields() {
        $summaryFields = array(
            'RatingText' => $this->fieldLabel('RatingText'),
            'RatingGrade' => $this->fieldLabel('RatingGrade')
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
    
    /**
     * calculates the average grade of ratings of a ratings class
     * 
     * @param string  $className the class name of the the rating class;
     * @param integer $precision figure number for rounding
     * @param string  $filter    the sql filter string
     * 
     * @return decimal|false the result is rounded by two digits
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 10.09.2011
     */
    public static function calculateAverageRatingGrade($className, $precision = 2, $filter = null) {
        $ratings = DataObject::get($className, $filter);
        if ($ratings->exists()) {
                $ratingGradesSum = 0;
                foreach ($ratings as $rating) {
                    $ratingGradesSum += $rating->RatingGrade;
                }
                $averageGrade = $ratingGradesSum / $ratings->count();
                return round($averageGrade, $precision);
            }
        return false;
    }
}