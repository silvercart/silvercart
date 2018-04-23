<?php

namespace SilverCart\Model\Pages;

use SilverCart\Dev\Tools;
use SilverCart\Model\Pages\MetaNavigationHolder;
use SilverStripe\ORM\DataList;

/**
 * MetaNavigationHolder Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MetaNavigationHolderController extends \PageController {

    /**
     * Uses the children of MetaNavigationHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getSubNavigation($identifierCode = 'SilvercartProductGroupHolder') {
        $root   = $this->dataRecord;
        $output = '';
        if ($root->ClassName != MetaNavigationHolder::class) {
            while ($root->ClassName != MetaNavigationHolder::class) {
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
                    'SilverCart/Model/Pages/Includes/SubNavigation',
                )
            );
            $sisters = MetaNavigationHolder::get();
            if ($sisters instanceof DataList) {
                foreach ($sisters as $sister) {
                    if ($sister->ID == $root->ID ||
                        $sister->Parent() instanceof MetaNavigationHolder) {
                        continue;
                    }
                    $elements = array(
                        'SubElementsTitle'  => $sister->MenuTitle,
                        'SubElements'       => $sister->Children(),
                    );
                    $output .= $this->customise($elements)->renderWith(
                        array(
                            'SilverCart/Model/Pages/Includes/SubNavigation',
                        )
                    );
                }
            }
        }
        return Tools::string2html($output);
    }
}