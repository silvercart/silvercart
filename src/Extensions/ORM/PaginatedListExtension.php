<?php

namespace SilverCart\Extensions\ORM;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\ORM\PaginatedList;

/**
 * Extension for PaginatedList.
 * 
 * @package SilverCart
 * @subpackage Extensions\ORM
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 13.12.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 * 
 * @property PaginatedList $owner Owner
 */
class PaginatedListExtension extends Extension
{
    /**
     * Sets the link hash.
     * 
     * @param string $hash Hash
     * 
     * @return PaginatedList
     */
    public function setLinkHash(string $hash) : PaginatedList
    {
        $this->owner->LinkHash = $hash;
        return $this->owner;
    }
    
    /**
     * Returns the link hash.
     * 
     * @return string
     */
    public function getLinkHash() : string
    {
        if (!property_exists($this->owner, 'LinkHash')) {
            $this->owner->LinkHash = '';
        }
        $hash = (string) $this->owner->LinkHash;
        if (!empty($hash)
         && strpos($hash, '#') === false
        ) {
            $hash = "#{$hash}";
        }
        return $hash;
    }
    
    /**
     * Returns the link hash.
     * Alias for $this->getLinkHash().
     * 
     * @return string
     */
    public function LinkHash() : string
    {
        return $this->getLinkHash();
    }

    /**
     * Renders the default pagination.
     * 
     * @param string $alignment    Alignment
     * @param string $extraClasses Extra CSS classes
     * 
     * @return DBHTMLText
     */
    public function RenderPagination(string $alignment = 'center', string $extraClasses = '') : DBHTMLText
    {
        switch (strtolower($alignment)) {
            case 'start':
            case 'left':
                $alignmentClass = 'justify-content-start';
                break;
            case 'right':
            case 'end':
                $alignmentClass = 'justify-content-end';
                break;
            case 'between':
                $alignmentClass = 'justify-content-between';
                break;
            case 'around':
                $alignmentClass = 'justify-content-around';
                break;
            case 'center':
            default:
                $alignmentClass = 'justify-content-center';
                break;
        }
        return $this->owner->renderWith(self::class . '_Pagination', [
            'PaginatedList'  => $this->owner,
            'AlignmentClass' => $alignmentClass,
            'ExtraClasses'   => $extraClasses,
        ]);
    }

    /**
     * Renders the default small pagination.
     * 
     * @param string $alignment    Alignment
     * @param string $extraClasses Extra CSS classes
     * 
     * @return DBHTMLText
     */
    public function RenderPaginationSM(string $alignment = 'center', string $extraClasses = '') : DBHTMLText
    {
        return $this->RenderPagination($alignment, "pagination-sm {$extraClasses}");
    }

    /**
     * Renders the default larges pagination.
     * 
     * @param string $alignment    Alignment
     * @param string $extraClasses Extra CSS classes
     * 
     * @return DBHTMLText
     */
    public function RenderPaginationLG(string $alignment = 'center', string $extraClasses = '') : DBHTMLText
    {
        return $this->RenderPagination($alignment, "pagination-lg {$extraClasses}");
    }
    
    /**
     * Renders the default pagination info.
     * 
     * @return DBHTMLText
     */
    public function RenderPaginationInfo() : DBHTMLText
    {
        return $this->owner->renderWith(self::class . '_PaginationInfo', [
            'PaginatedList' => $this->owner,
        ]);
    }
}