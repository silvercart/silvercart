<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * This site is not visible in the frontend.
 * Its purpose is to gather the meta navigation sites in the backend for better usability.
 * Now a shop admin has a correspondence between front end site order and backend tree structure.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 23.10.2010
 * @license see license file in modules root directory
 */
class SilvercartMetaNavigationHolder extends Page {

    /**
     * We set a custom icon for this page type here
     *
     * @var string
     */
    public static $icon = "silvercart/img/page_icons/metanavigation_holder";

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
    
}

/**
 * correlating controller
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @since 23.10.2010
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartMetaNavigationHolder_Controller extends Page_Controller {


    /**
     * Uses the children of SilvercartMetaNavigationHolder to render a subnavigation
     * with the SilvercartSubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return string
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $root   = $this->dataRecord;
        $output = '';
        if ($root->ClassName != 'SilvercartMetaNavigationHolder') {
            while ($root->ClassName != 'SilvercartMetaNavigationHolder') {
                $root = $root->Parent();
                if ($root->ParentID == 0) {
                    $root = null;
                    break;
                }
            }
        }
        if (!is_null($root)) {
            $elements = array(
                'SubElementsTitle'  => $root->MenuTitle,
                'SubElements'       => $root->Children(),
            );
            $output = $this->customise($elements)->renderWith(
                array(
                    'SilvercartSubNavigation',
                )
            );
            $sisters = SilvercartMetaNavigationHolder::get();
            if ($sisters instanceof DataList) {
                foreach ($sisters as $sister) {
                    if ($sister->ID == $root->ID ||
                        $sister->Parent() instanceof SilvercartMetaNavigationHolder) {
                        continue;
                    }
                    $elements = array(
                        'SubElementsTitle'  => $sister->MenuTitle,
                        'SubElements'       => $sister->Children(),
                    );
                    $output .= $this->customise($elements)->renderWith(
                        array(
                            'SilvercartSubNavigation',
                        )
                    );
                }
            }
        }
        return $output;
    }
}
