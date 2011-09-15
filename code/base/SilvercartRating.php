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
        if ($filter === null) {
            $ratings = DataObject::get('SilvercartRating', "`ClassName` = $className");
        } else {
            $ratings = DataObject::get('SilvercartRating', "`ClassName` = $className AND $filter");
        }
        if ($ratings) {
                $ratingGradesSum = 0;
                foreach ($ratings as $rating) {
                    $ratingGradesSum += $rating->ratingGrade;
                }
                $averageGrade = $ratingGradesSum / $ratings->Count();
                return round($averageGrade, $precision);
            }
        return false;
    }
}