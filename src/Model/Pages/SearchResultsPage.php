<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\ProductGroupPage;

/**
 * page type to display search results.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 28.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SearchResultsPage extends ProductGroupPage {

    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartSearchResultsPage';
    
    /**
     * Set allowed children for this page.
     *
     * @var array
     */
    private static $allowed_children = 'none';
    
    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    private static $icon = "silvercart/silvercart:client/img/page_icons/metanavigation_page_search-file.gif";

    /**
     * Attributes.
     *
     * @var array
     */
    private static $db = array(
        'productsPerPage' => 'Int'
    );
    
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
        return Tools::singular_name_for($this);
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
        return Tools::plural_name_for($this); 
    }

    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 20.04.2011
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'productsPerPage' => _t(ProductGroupPage::class . '.PRODUCTSPERPAGE', 'Products per page'),
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Return all fields of the backend.
     *
     * @return FieldList Fields of the CMS
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();

        $fields->removeByName('useContentFromParent');
        $fields->removeByName('DoNotShowProducts');
        $fields->removeByName('productGroupsPerPage');
        $fields->removeByName('DefaultGroupHolderView');
        $fields->removeByName('UseOnlyDefaultGroupHolderView');

        return $fields;
    }
}