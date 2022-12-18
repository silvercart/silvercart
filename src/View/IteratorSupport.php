<?php

namespace SilverCart\View;

/**
 * Adds some extended features to the SilverStripe basic iterator support.
 * 
 * @package SilverCart
 * @subpackage View
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 18.12.2022
 * @copyright 2022 pixeltricks GmbH
 * @license see license file in modules root directory
 */
trait IteratorSupport
{
    /**
     * Iterator position
     * 
     * @var int
     */
    protected $iteratorPos = 0;
    /**
     * Iterator total items
     * 
     * @var int
     */
    protected $iteratorTotalItems = 0;

    /**
     * Sets the basic itereator properties.
     * 
     * @param int $pos        Pos
     * @param int $totalItems Total items
     * 
     * @return void
     */
    public function setIteratorProperties(int $pos, int $totalItems) : void
    {
        $this->iteratorPos        = $pos;
        $this->iteratorTotalItems = $totalItems;
    }
    
    /**
     * Returns the iterator position.
     * 
     * @return int
     */
    public function getIteratorPos() : int
    {
        return (int) $this->iteratorPos;
    }
    
    /**
     * Returns the iterator total items.
     * 
     * @return int
     */
    public function getIteratorTotalItems() : int
    {
        return (int) $this->iteratorTotalItems;
    }
    
    /**
     * Returns leading zeros 
     * 
     * @return string
     */
    public function getIteratorLeadingZeros() : string
    {
        $pos          = $this->getIteratorPos();
        $totalItems   = $this->getIteratorTotalItems();
        $leadingZeros = '';
        for ($x = 0; $x < (strlen($totalItems) - strlen($pos)); $x++) {
            $leadingZeros .= '0';
        }
        return $leadingZeros;
    }
}