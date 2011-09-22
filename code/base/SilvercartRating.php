<?php

/**
 * abstract for a rating;
 * A rating may belong to a product or the store in general.
 * Decorate this class or extend it if you want to implement third party ratings.
 *
 * @package Silvercart
 * @subpackage base
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
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.09.2011
     */
    public static $db = array(
        'RatingText'  => 'Text',
        'RatingGrade' => 'Decimal'
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 09.09.2011
     */
    public static $has_one = array(
        'SilvercartProduct' => 'SilvercartProduct',
        'Customer'          => 'Member'
    );
    
    public static $default_sort = "Created DESC";

    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.09.2011
     */
    public function singular_name() {
        if (_t('SilvercartRating.SINGULARNAME')) {
            return _t('SilvercartRating.SINGULARNAME');
        } else {
            return parent::singular_name();
        } 
    }

    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.09.2011
     */
    public function plural_name() {
        if (_t('SilvercartRating.PLURALNAME')) {
            return _t('SilvercartRating.PLURALNAME');
        } else {
            return parent::plural_name();
        }

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
            'RatingText' => _t('SilvercartRating.TEXT'),
            'RatingGrade' => _t('SilvercartRating.GRADE'),
            'SilvercartProduct' => _t('SilvercartProduct.SINGULARNAME'),
            'Customer' => _t('Member.SINGULARNAME')
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
            'RatingText' => _t('SilvercartRating.TEXT'),
            'RatingGrade' => _t('SilvercartRating.GRADE')
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
        if ($ratings) {
                $ratingGradesSum = 0;
                foreach ($ratings as $rating) {
                    $ratingGradesSum += $rating->RatingGrade;
                }
                $averageGrade = $ratingGradesSum / $ratings->Count();
                return round($averageGrade, $precision);
            }
        return false;
    }
}