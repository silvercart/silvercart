<?php

/**
 * abstract for a rating;
 * A rating may belong to a product or the store in general.
 * Decorate this class or extend it if you want to implement third party ratings.
 *
 * @package Silvercart
 * @subpackage Pages
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
        'RatingGrade' => 'VarChar(20)'
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
}