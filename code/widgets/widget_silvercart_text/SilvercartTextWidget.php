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
 * @subpackage Widgets
 */

/**
 * Provides a free text widget.
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTextWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public static $db = array(
        'FreeText'  => 'HTMLText'
    );
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartText.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartText.TITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartText.DESCRIPTION');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 09.06.2011
     */
    public function getCMSFields() {
        $fields     = parent::getCMSFields();
        $textField  = new TextareaField('FreeText', _t('SilvercartText.FREETEXTFIELD_LABEL'));
        
        $fields->push($textField);
        
        return $fields;
    }
}

/**
 * Provides a free text widget.
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 09.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartTextWidget_Controller extends SilvercartWidget_Controller {
    
    /**
     * Overloaded from {@link Widget->Content()}
     * to allow for controller/form linking.
     * 
     * @return string HTML
     */
    function Content() {
        $renderData = array(
            'Controller' => $this
        );
        $template = new SSViewer_FromString($this->getField('FreeText'));
        $freeText = HTTP::absoluteURLs($template->process(new ArrayData($renderData)));

        $data = new ArrayData(
            array(
                'FreeText' => $freeText
            )
        );

        return $this->customise($data)->renderWith(array_reverse(ClassInfo::ancestry($this->widget->class)));
    }
}
