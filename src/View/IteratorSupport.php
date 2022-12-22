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
     * Returns true if this object is the first in a set.
     *
     * @return bool
     */
    public function IsIteratorFirst()
    {
        return $this->iteratorPos == 0;
    }

    /**
     * Returns true if this object is the last in a set.
     *
     * @return bool
     */
    public function IsIteratorLast()
    {
        return $this->iteratorPos == $this->iteratorTotalItems - 1;
    }

    /**
     * Return the numerical position of this object in the container set. The count starts at $startIndex.
     * The default is the give the position using a 1-based index.
     *
     * @param int $startIndex Number to start count from.
     * 
     * @return int
     */
    public function IteratorPos(int $startIndex = 1)
    {
        return $this->getIteratorPos($startIndex);
    }
    
    /**
     * Returns the iterator position.
     * 
     * @return int
     */
    public function getIteratorPos($startIndex = 1) : int
    {
        return (int) $this->iteratorPos + $startIndex;
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