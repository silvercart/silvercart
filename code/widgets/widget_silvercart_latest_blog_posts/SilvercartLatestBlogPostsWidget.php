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
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public static $db = array(
        'WidgetTitle'                   => 'VarChar(255)',
        'numberOfPostsToShow'           => 'Int',
        'isContentView'                 => 'Boolean'
    );
    
    /**
     * Set default values.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public static $defaults = array(
        'numberOfPostsToShow' => 5
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.08.2011
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        
        $widgetTitleField    = new TextField('WidgetTitle', _t('SilvercartLatestBlogPostsWidget.WIDGET_TITLE'));
        $numberOfPostsField  = new TextField('numberOfPostsToShow', _t('SilvercartLatestBlogPostsWidget.STOREADMIN_NUMBEROFPOSTS'));
        $isContentView       = new CheckboxField('isContentView', _t('SilvercartLatestBlogPostsWidget.IS_CONTENT_VIEW'));
        
        $fields->push($widgetTitleField);
        $fields->push($numberOfPostsField);
        $fields->push($isContentView);
        
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