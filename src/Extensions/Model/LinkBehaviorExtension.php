<?php

namespace SilverCart\Extensions\Model;

use SilverCart\Model\Pages\Page as SilverCartPage;
use SilverCart\Model\Pages\CartPage;
use SilverCart\Model\Pages\CheckoutStep;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\DropdownField;

/**
 * Extension to add the LinkBehavior property and CMS fields to a DataObject.
 * 
 * @package SilverCart
 * @subpackage Extensions\Model
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 21.04.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property \SilverStripe\ORM\DataObject $owner Owner
 */
class LinkBehaviorExtension extends DataExtension
{
    use \SilverStripe\Core\Config\Configurable;
    
    public const LINK_BEHAVIOR_NO_LINK = 'nolink';
    public const LINK_BEHAVIOR_PAGE    = 'page';
    public const LINK_BEHAVIOR_POPUP   = 'popup';
    public const LINK_TARGET_BLANK     = '_blank';

    /**
     * Returns the linking target for foreign links on the given page.
     * 
     * @param SilverCartPage $page  Page context
     * @param bool           $plain Plain target value or as HTMNL attribute (default)
     * 
     * @return string
     */
    public static function ForeignLinkTarget(SilverCartPage $page, bool $plain = false) : string
    {
        $target = '';
        if (array_key_exists($page->ClassName, self::config()->foreign_link_targets)) {
            $target = self::config()->foreign_link_targets[$page->ClassName];
            if (!$plain) {
                $target = "target=\"{$target}\"";
            }
        }
        return $target;
    }
    
    /**
     * DB attributes.
     * 
     * @var array
     */
    private static $db = [
        'LinkBehavior' => "Enum('page,popup,nolink','page')",
    ];
    /**
     * Foreign link targets.
     * 
     * @var string[]
     */
    private static $foreign_link_targets = [
        CartPage::class     => self::LINK_TARGET_BLANK,
        CheckoutStep::class => self::LINK_TARGET_BLANK,
    ];
    
    /**
     * Adds or removes GUI elements for the backend editing mask.
     *
     * @param FieldList $fields The original FieldList
     *
     * @return void
     */
    public function updateCMSFields(FieldList $fields) : void
    {
        $tabName      = 'Root.Main';
        $insertBefore = null;
        $enumValues   = $this->owner->dbObject('LinkBehavior')->enumValues();
        $i18nSource   = [];
        foreach ($enumValues as $value => $label) {
            if (empty($label)) {
                $label = 'empty';
            }
            $i18nSource[$value] = _t(self::class . ".LinkBehavior_{$label}", $label);
        }
        $fields->addFieldToTab($tabName, DropdownField::create('LinkBehavior', $this->owner->fieldLabel('LinkBehavior'), $i18nSource, $this->owner->LinkBehavior), $insertBefore);
    }
    
    /**
     * Updates the field labels
     *
     * @param array &$labels The original labels
     *
     * @return void
     */
    public function updateFieldLabels(&$labels) : void
    {
        $labels = array_merge($labels, [
            'LinkBehavior' => _t(self::class . '.LinkBehavior', 'Link behavior'),
        ]);
    }
    
    /**
     * Returns whether the owner of this extension has the nolink link behavior.
     * 
     * @return bool
     */
    public function LinkBehaviorNoLink() : bool
    {
        return $this->owner->LinkBehavior === self::LINK_BEHAVIOR_NO_LINK;
    }
    
    /**
     * Returns whether the owner of this extension has the page link behavior.
     * 
     * @return bool
     */
    public function LinkBehaviorPage() : bool
    {
        return $this->owner->LinkBehavior === self::LINK_BEHAVIOR_PAGE;
    }
    
    /**
     * Returns whether the owner of this extension has the popup link behavior.
     * 
     * @return bool
     */
    public function LinkBehaviorPopup() : bool
    {
        return $this->owner->LinkBehavior === self::LINK_BEHAVIOR_POPUP;
    }
}