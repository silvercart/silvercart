<?php
/**
 * Copyright 2013 pixeltricks GmbH
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
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 20.06.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTextWithLinkWidget extends SilvercartTextWidget {
    
    /**
     * DB attributes
     * 
     * @var array
     */
    public static $db = array(
        'Link' => 'Varchar(256)',
    );
    
    /**
     * Casted Attributes.
     * 
     * @var array
     */
    public static $casting = array(
        'LinkText' => 'Text',
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartTextWithLinkWidgetLanguages' => 'SilvercartTextWithLinkWidgetLanguage'
    );

    /**
     * retirieves the attribute FreeText from related language class depending
     * on the set locale
     *
     * @return string
     */
    public function getLinkText() {
        return $this->getLanguageFieldValue('LinkText');
    }

    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function Title() {
        $title = $this->fieldLabel('Title');
        if (!empty($this->Headline)) {
            $title .= ': ' . $this->Headline;
        }
        return $title;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
            parent::fieldLabels($includerelations),
            array(
                'Link'                                  => _t('SilvercartTextWithLinkWidget.Link'),
                'LinkText'                              => _t('SilvercartTextWithLinkWidget.LinkText'),
                'SilvercartTextWithLinkWidgetLanguages' => _t('SilvercartTextWithLinkWidgetLanguage.PLURALNAME'),
                'Title'                                 => _t('SilvercartTextWithLinkWidget.TITLE'),
                'Description'                           => _t('SilvercartTextWithLinkWidget.DESCRIPTION'),
                
            )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function CMSTitle() {
        $title = $this->fieldLabel('Title');
        if (!empty($this->Headline)) {
            $title = $this->Headline;
        }
        return $title;
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function Description() {
        return $this->fieldLabel('Description');
    }
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldList
     */
    public function getCMSFields() {
        $fields = SilvercartDataObject::getCMSFields($this);
        $fields->removeByName('SilvercartTextWidgetLanguages');
        return $fields;
    }
}

/**
 * Provides a free text widget.
 * 
 * @package Silvercart
 * @subpackage Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 20.06.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTextWithLinkWidget_Controller extends SilvercartTextWidget_Controller {

    /**
     * Overloaded from {@link Widget->Content()}
     * to allow for controller/form linking.
     *
     * @return string HTML
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function Content() {
        $renderData = array(
            'Controller' => $this
        );
        $template = new SSViewer_FromString($this->getField('FreeText'));
        $freeText = HTTP::absoluteURLs($template->process(new ArrayData($renderData)));

        $data = new ArrayData(
            array(
                'FreeText'  => $freeText,
                'LinkText'  => $this->LinkText,
                'Link'      => $this->Link
            )
        );
        
        return $this->customise($data)->renderWith($this->widget->class);
    }
}

/**
 * Translation object of SilvercartTextWithLinkWidget
 * 
 * @package Silvercart
 * @subpackage Translation
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 20.06.2013
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartTextWithLinkWidgetLanguage extends SilvercartTextWidgetLanguage {
    
    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'LinkText'  => 'VarChar(255)',
    );
    
    /**
     * 1:1 or 1:n relationships.
     *
     * @var array
     */
    public static $has_one = array(
        'SilvercartTextWithLinkWidget' => 'SilvercartTextWithLinkWidget'
    );
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
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
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),
                array(
                    'LinkText' => _t('SilvercartTextWithLinkWidget.LinkText'),
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }

    /**
     * Summary fields
     *
     * @return array
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function summaryFields() {
        $summaryFields = array_merge(
                parent::summaryFields(),
                array(
                    'LinkText' => $this->fieldLabel('LinkText'),
                )
        );

        $this->extend('updateSummaryFields', $summaryFields);
        return $summaryFields;
    }
}