<?php
/**
 * Copyright 2011 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * SilverCart is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * SilverCart is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with SilverCart.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Silvercart
 * @subpackage Config
 */

/**
 * This is a configuration object and can be used to store individual css style
 * informations
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 08.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartThemeConfig extends ViewableData {
    
    /**
     * Registered button objects
     *
     * @var array
     */
    public static $buttons = array();
    
    public static $defaultHeadingColor                                          = '#429bb9';
    
    public static $defaultLinkColor                                             = '#0f517b';
    public static $defaultLinkFocusColor                                        = '#000';
    public static $defaultLinkHoverColor                                        = '#429bb9';
    public static $defaultLinkVisitedColor                                      = '#3a3436';
    
    public static $defaultTableHeadBorderColor                                  = '#429bb9';
    public static $defaultTableFootBorderColor                                  = '#429bb9';

    /**
     * Registers all default theme objects
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2012 
     */
    public static function registerObjects() {
        $DefaultButton = new SilvercartThemeConfigButton();
        self::addButton('DefaultButton', $DefaultButton);
        
        $DefaultSubmitButton = new SilvercartThemeConfigButton();
        $DefaultSubmitButton->setFontColor(         '#ffffff');
        $DefaultSubmitButton->setBorderColor(       '#444444');
        $DefaultSubmitButton->setColorStart(        '#407e95');
        $DefaultSubmitButton->setColorEnd(          '#175072');
        $DefaultSubmitButton->setHoverFontColor(    '#ffffff');
        $DefaultSubmitButton->setHoverBorderColor(  '#444444');
        $DefaultSubmitButton->setHoverColorStart(   '#34677a');
        $DefaultSubmitButton->setHoverColorEnd(     '#13415d');
        self::addButton('DefaultSubmitButton', $DefaultSubmitButton);
    }
    
    /**
     * Registers all default theme variables
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2012 
     */
    public static function registerVariables() {
        RequirementsEngine::registerCssVariable('DefaultHeadingColor',          self::$defaultHeadingColor);
        
        RequirementsEngine::registerCssVariable('DefaultLinkColor',             self::$defaultLinkColor);
        RequirementsEngine::registerCssVariable('DefaultLinkFocusColor',        self::$defaultLinkFocusColor);
        RequirementsEngine::registerCssVariable('DefaultLinkHoverColor',        self::$defaultLinkHoverColor);
        RequirementsEngine::registerCssVariable('DefaultLinkVisitedColor',      self::$defaultLinkVisitedColor);
        
        RequirementsEngine::registerCssVariable('DefaultTableHeadBorderColor',  self::$defaultTableHeadBorderColor);
        RequirementsEngine::registerCssVariable('DefaultTableFootBorderColor',  self::$defaultTableFootBorderColor);
    }

    /**
     * Returns the button with the given name
     * 
     * @param string $name Name of button to get
     * 
     * @return SilvercartThemeConfigButton 
     */
    public static function getButton($name) {
        if (!array_key_exists($name, self::$buttons)) {
            self::addButton($name, new SilvercartThemeConfigButton());
        }
        $button = self::$buttons[$name];
        return $button;
    }
    
    /**
     * Adds the button with the given name
     *
     * @param string                      $name   Name of the button
     * @param SilvercartThemeConfigButton $button Button
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 09.05.2012
     */
    public static function addButton($name, $button) {
        if (!array_key_exists($name, self::$buttons)) {
            self::$buttons[$name] = $button;
            RequirementsEngine::registerCssVariable($name, $button);
        }
    }
    
    /**
     * Returns the default css to render a gradient by the given start and end color
     *
     * @param string $colorStart Start color
     * @param string $colorEnd   End color
     * 
     * @return string 
     */
    public function getCssGradient($colorStart, $colorEnd) {
        $css = '
            background:                     -webkit-gradient(linear, left top, left bottom, from(' . $colorStart . '), to(' . $colorEnd . '));
            background:                     -webkit-linear-gradient(top,  ' . $colorStart . ' 0%,' . $colorEnd . ' 100%);
            background:                     -moz-linear-gradient(top,  ' . $colorStart . ',  ' . $colorEnd . ');
            background:                     -o-linear-gradient(top,  ' . $colorStart . ',  ' . $colorEnd . ');
            background:                     -ms-linear-gradient(top,  ' . $colorStart . ' 0%,  ' . $colorEnd . ' 100%);
            background:                     linear-gradient(top,  ' . $colorStart . ' 0%,  ' . $colorEnd . ' 100%);
            filter:                         progid:DXImageTransform.Microsoft.gradient(startColorstr="' . $colorStart . '", endColorstr="' . $colorEnd . '");';
        return $css;
    }
    
}

/**
 * This is a configuration object and can be used to store individual css style
 * informations for buttons
 *
 * @package Silvercart
 * @subpackage Config
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 09.05.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartThemeConfigButton extends SilvercartThemeConfig {
    
    protected $UseGradient                                     = true;
    protected $FontColor                                       = '#333333';
    protected $BorderColor                                     = '#999999';
    protected $ColorStart                                      = '#ffffff';
    protected $ColorEnd                                        = '#d9d9d9';
    protected $HoverFontColor                                  = '#000000';
    protected $HoverBorderColor                                = '#888888';
    protected $HoverColorStart                                 = '#eeeeee';
    protected $HoverColorEnd                                   = '#c9c9c9';
    protected $ActiveFontColor                                 = '#0f517b';
    protected $ActiveBorderColor                               = '#0f517b';
    protected $ActiveColorStart                                = '#ffffff';
    protected $ActiveColorEnd                                  = '#d4e2e8';
    
    /**
     * Returns the buttons default css
     *
     * @return string
     */
    public function getCss() {
        $css = '
            color:                          ' . $this->getFontColor() . ';
            background:                     ' . $this->getColorStart() . ';
            border:                         1px ' . $this->getBorderColor() . ' solid;';
        if ($this->getUseGradient()) {
            $css .= $this->getCssGradient($this->getColorStart(), $this->getColorEnd());
        }
        return $css;
    }
    
    /**
     * Returns the buttons default css for an active state
     *
     * @return string
     */
    public function getCssActive() {
        $css = '
            color:                          ' . $this->getActiveFontColor() . ';
            background:                     ' . $this->getActiveColorStart() . ';
            border:                         1px ' . $this->getActiveBorderColor() . ' solid;';
        if ($this->getUseGradient()) {
            $css .= $this->getCssGradient($this->getActiveColorStart(), $this->getActiveColorEnd());
        }
        return $css;
    }
    
    /**
     * Returns the buttons default css for an hover state
     *
     * @return string
     */
    public function getCssHover() {
        $css = '
            color:                          ' . $this->getHoverFontColor() . ';
            background:                     ' . $this->getHoverColorStart() . ';
            border:                         1px ' . $this->getHoverBorderColor() . ' solid;';
        if ($this->getUseGradient()) {
            $css .= $this->getCssGradient($this->getHoverColorStart(), $this->getHoverColorEnd());
        }
        return $css;
    }
    
    /**
     * Returns whether to use gradients or not
     *
     * @return bool
     */
    public function getUseGradient() {
        return $this->UseGradient;
    }

    /**
     * Sets whether to use gradients or not
     *
     * @param bool $UseGradient Use gradient or not?
     * 
     * @return void
     */
    public function setUseGradient($UseGradient) {
        $this->UseGradient = $UseGradient;
    }

    /**
     * Returns the font color
     *
     * @return string
     */
    public function getFontColor() {
        return $this->FontColor;
    }

    /**
     * Sets the font color
     *
     * @param string $FontColor font color
     * 
     * @return void
     */
    public function setFontColor($FontColor) {
        $this->FontColor = $FontColor;
    }

    /**
     * Returns the border color
     *
     * @return string
     */
    public function getBorderColor() {
        return $this->BorderColor;
    }

    /**
     * Sets the border color
     *
     * @param string $BorderColor border color
     * 
     * @return void
     */
    public function setBorderColor($BorderColor) {
        $this->BorderColor = $BorderColor;
    }

    /**
     * Returns the color start
     *
     * @return string
     */
    public function getColorStart() {
        return $this->ColorStart;
    }

    /**
     * Sets the color start
     *
     * @param string $ColorStart color start
     * 
     * @return void
     */
    public function setColorStart($ColorStart) {
        $this->ColorStart = $ColorStart;
    }

    /**
     * Returns the color end
     *
     * @return string
     */
    public function getColorEnd() {
        return $this->ColorEnd;
    }

    /**
     * Sets the color end
     *
     * @param string $ColorEnd color end
     * 
     * @return void
     */
    public function setColorEnd($ColorEnd) {
        $this->ColorEnd = $ColorEnd;
    }

    /**
     * Returns the hover font color
     *
     * @return string
     */
    public function getHoverFontColor() {
        return $this->HoverFontColor;
    }

    /**
     * Sets the hover font color
     *
     * @param string $HoverFontColor hover font color
     * 
     * @return void
     */
    public function setHoverFontColor($HoverFontColor) {
        $this->HoverFontColor = $HoverFontColor;
    }

    /**
     * Returns the hover border color
     *
     * @return string
     */
    public function getHoverBorderColor() {
        return $this->HoverBorderColor;
    }

    /**
     * Sets the hover border color
     *
     * @param string $HoverBorderColor hover border color
     * 
     * @return void
     */
    public function setHoverBorderColor($HoverBorderColor) {
        $this->HoverBorderColor = $HoverBorderColor;
    }

    /**
     * Returns the hover color start
     *
     * @return string
     */
    public function getHoverColorStart() {
        return $this->HoverColorStart;
    }

    /**
     * Sets the hover color start
     *
     * @param string $HoverColorStart hover color start
     * 
     * @return void
     */
    public function setHoverColorStart($HoverColorStart) {
        $this->HoverColorStart = $HoverColorStart;
    }

    /**
     * Returns the hover color end
     *
     * @return string
     */
    public function getHoverColorEnd() {
        return $this->HoverColorEnd;
    }

    /**
     * Sets the hover color end
     *
     * @param string $HoverColorEnd hover color end
     * 
     * @return void
     */
    public function setHoverColorEnd($HoverColorEnd) {
        $this->HoverColorEnd = $HoverColorEnd;
    }

    /**
     * Returns the active font color
     *
     * @return string
     */
    public function getActiveFontColor() {
        return $this->ActiveFontColor;
    }

    /**
     * Sets the active font color
     *
     * @param string $ActiveFontColor active font color
     * 
     * @return void
     */
    public function setActiveFontColor($ActiveFontColor) {
        $this->ActiveFontColor = $ActiveFontColor;
    }

    /**
     * Returns the active border color
     *
     * @return string
     */
    public function getActiveBorderColor() {
        return $this->ActiveBorderColor;
    }

    /**
     * Sets the active border color
     *
     * @param string $ActiveBorderColor active border color
     * 
     * @return void
     */
    public function setActiveBorderColor($ActiveBorderColor) {
        $this->ActiveBorderColor = $ActiveBorderColor;
    }

    /**
     * Returns the active color start
     *
     * @return string
     */
    public function getActiveColorStart() {
        return $this->ActiveColorStart;
    }

    /**
     * Sets the active color start
     *
     * @param string $ActiveColorStart active color start
     * 
     * @return void
     */
    public function setActiveColorStart($ActiveColorStart) {
        $this->ActiveColorStart = $ActiveColorStart;
    }

    /**
     * Returns the active color end
     *
     * @return string
     */
    public function getActiveColorEnd() {
        return $this->ActiveColorEnd;
    }

    /**
     * Sets the active color end
     *
     * @param string $ActiveColorEnd active color end
     * 
     * @return void
     */
    public function setActiveColorEnd($ActiveColorEnd) {
        $this->ActiveColorEnd = $ActiveColorEnd;
    }
    
}

SilvercartThemeConfig::registerObjects();
