<?php

namespace SilverCart\Model\Pages;

use Page;
use SilverStripe\ORM\DataList;
use SilverStripe\ORM\FieldType\DBHTMLText;

/**
 * This site is not visible in the frontend.
 * Its purpose is to gather the meta navigation sites in the backend for better usability.
 * Now a shop admin has a correspondence between front end site order and backend tree structure.
 *
 * @package SilverCart
 * @subpackage Model\Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class MetaNavigationHolder extends Page
{
    /**
     * DB table name
     *
     * @var string
     */
    private static $table_name = 'SilvercartMetaNavigationHolder';
    /**
     * Class attached to page icons in the CMS page tree. Also supports font-icon set.
     * 
     * @var string
     */
    private static $icon_class = 'font-icon-tree';
    
    /**
     * Uses the children of MetaNavigationHolder to render a subnavigation
     * with the SilverCart/Model/Pages/Includes/SubNavigation.ss template.
     * 
     * @param string $identifierCode param only added because it exists on parent::getSubNavigation
     *                               to avoid strict notice
     *
     * @return DBHTMLText
     */
    public function getSubNavigation($identifierCode = self::IDENTIFIER_PRODUCT_GROUP_HOLDER) : DBHTMLText
    {
        $root   = $this;
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
            $elements = [
                'SubElementsTitle'  => $root->MenuTitle,
                'SubElements'       => $root->Children(),
            ];
            $output = $this->customise($elements)->renderWith([
                'SilverCart/Model/Pages/Includes/SubNavigation',
            ]);
            $sisters = MetaNavigationHolder::get();
            if ($sisters instanceof DataList) {
                foreach ($sisters as $sister) {
                    if ($sister->ID == $root->ID
                     || $sister->Parent() instanceof MetaNavigationHolder
                    ) {
                        continue;
                    }
                    $elements = [
                        'SubElementsTitle'  => $sister->MenuTitle,
                        'SubElements'       => $sister->Children(),
                    ];
                    $output .= $this->customise($elements)->renderWith([
                        'SilverCart/Model/Pages/Includes/SubNavigation',
                    ]);
                }
            }
        }
        return DBHTMLText::create()->setValue($output);
    }
}