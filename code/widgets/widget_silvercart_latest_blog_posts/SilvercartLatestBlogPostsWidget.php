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
 * Provides a view of the latest blog posts.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 18.08.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartLatestBlogPostsWidget extends SilvercartWidget {

    /**
     * Attributes.
     *
     * @var array
     */
    public static $db = array(
        'numberOfPostsToShow'           => 'Int',
        'isContentView'                 => 'Boolean'
    );

    /**
     * Set default values.
     *
     * @var array
     */
    public static $defaults = array(
        'numberOfPostsToShow' => 5
    );
    
    /**
     * field type casting
     *
     * @var array
     */
    public static $casting = array(
        'WidgetTitle' => 'VarChar(255)'
    );
    
    /**
     * 1:n relationships.
     *
     * @var array
     */
    public static $has_many = array(
        'SilvercartLatestBlogPostsWidgetLanguages' => 'SilvercartLatestBlogPostsWidgetLanguage'
    );
    
    /**
     * Getter for the widgets title depending on the set language
     *
     * @return string 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.07.2012
     */
    public function getWidgetTitle() {
        return $this->getLanguageFieldValue('WidgetTitle');
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 13.07.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                    'Content' => _t('Silvercart.CONTENT'),
                    'Translations' => _t('SilvercartConfig.TRANSLATIONS'),
                    'NumberOfPosts' => _t('SilvercartLatestBlogPostsWidget.STOREADMIN_NUMBEROFPOSTS'),
                    'IsContentView' => _t('SilvercartLatestBlogPostsWidget.IS_CONTENT_VIEW'),
                    
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
    
    /**
     * Returns the input fields for this widget.
     *
     * @return FieldSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2012
     */
    public function getCMSFields() {
        $fields             = new FieldSet();
        
        $rootTabSet         = new TabSet('RootTabSet');
        $mainTab            = new Tab('Root', $this->fieldLabel('Content'));
        $translationsTab    = new Tab('TranslationsTab', $this->fieldLabel('Translations'));
        
        $numberOfPostsField     = new TextField('numberOfPostsToShow', $this->fieldLabel('NumberOfPosts'));
        $isContentView          = new CheckboxField('isContentView', $this->fieldLabel('IsContentView'));
        $translationsTableField = new ComplexTableField($this, 'SilvercartLatestBlogPostsWidgetLanguages', 'SilvercartLatestBlogPostsWidgetLanguage');
        
        $languageFields = SilvercartLanguageHelper::prepareCMSFields($this->getLanguage(true));
        foreach ($languageFields as $languageField) {
            $mainTab->push($languageField);
        }
        
        $fields->push($rootTabSet);
        $rootTabSet->push($mainTab);
        $rootTabSet->push($translationsTab);
        $mainTab->push($numberOfPostsField);
        $mainTab->push($isContentView);
        $translationsTab->push($translationsTableField);
        
        return $fields;
    }

    /**
     * Returns the title of this widget.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function Title() {
        return _t('SilvercartLatestBlogPostsWidget.TITLE');
    }

    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function CMSTitle() {
        return _t('SilvercartLatestBlogPostsWidget.CMSTITLE');
    }

    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function Description() {
        return _t('SilvercartLatestBlogPostsWidget.DESCRIPTION');
    }

    /**
     * We set checkbox field values here to false if they are not in the post
     * data array.
     *
     * @param array $data The post data array
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 28.08.2011
     */
    public function populateFromPostData($data) {
        if (!array_key_exists('isContentView', $data)) {
            $this->isContentView = 0;
        }

        parent::populateFromPostData($data);
    }
}


/**
 * Provides a view of the latest blog posts.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 29.02.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2012 pixeltricks GmbH
 */
class SilvercartLatestBlogPostsWidget_Controller extends SilvercartWidget_Controller {

    /**
     * Returns a configured number of blog posts.
     * Returns false if the blog module is not installed
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 18.08.2011
     */
    public function BlogPosts() {
        if (class_exists('BlogEntry')) {
            $blogEntries = DataObject::get(
                'BlogEntry',
                '',
                'Sort DESC',
                '',
                $this->numberOfPostsToShow
            );

            return $blogEntries;
        }
        return false;
    }
    
    /**
     * Field labels for display in tables.
     *
     * @param boolean $includerelations A boolean value to indicate if the labels returned include relation fields
     *
     * @return array
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2012 pixeltricks GmbH
     * @since 27.01.2012
     */
    public function fieldLabels($includerelations = true) {
        $fieldLabels = array_merge(
                parent::fieldLabels($includerelations),             array(
                'SilvercartLatestBlogPostsWidgetLanguages' => _t('SilvercartLatestBlogPostsWidgetLanguage.PLURALNAME')
                )
        );

        $this->extend('updateFieldLabels', $fieldLabels);
        return $fieldLabels;
    }
}
