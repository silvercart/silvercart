<?php
/**
 * Copyright 2012 pixeltricks GmbH
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
 * @subpackage Backend
 */

/**
 * The SilverCart Dashboard
 * 
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 25.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartDashboard extends LeftAndMain {

    /**
     * The menu title
     * 
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public static $menu_title = 'Silvercart Dashboard';

    /**
     * The URL segment
     * 
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public static $url_segment = 'silvercart-dashboard';

    /**
     * Returns the dashboard fields.
     * 
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function DashboardFields() {
        $fields = new FieldSet();

        $dashboardField = new LiteralField(
            'silvercartDashboardField',
            sprintf(
                "<h2>%s</h2>",
                _t('SilvercartSiteConfig.WELCOME_TO_SILVERCART')
            )
        );
        $fields->push($dashboardField);

        $ordersByDayField = new SilvercartMetricsFieldOrdersByDay('SilvercartMetricsFieldOrdersByDay');
        $fields->push($ordersByDayField);

        return $fields;
    }

    /**
     * Loads additional ressources.
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function init() {
        Requirements::css('silvercart/css/backend/SilvercartMain.css');

        parent::init();
    }

    /**
     * Fetches the latest SilverCart news from silvercart.org
     * and formats them as HTML list.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function LatestSilvercartNews() {
        $newsString  = file_get_contents('http://www.silvercart.org/api/v1/BlogEntry?limit=6&sort=Created&dir=DESC');
        $xmlNews     = new SimpleXMLElement($newsString);
        $news        = new DataObjectSet();
        $newsEntries = 0;
        
        foreach ($xmlNews->BlogEntry as $blogEntry) {
            if ($blogEntry->Locale == 'en_US') {
                $content = $blogEntry->Content;
                $contentElems = explode('</p>', $content);
                $content = array_shift($contentElems)."</p>";

                $news->push(
                    new DataObject(
                        array(
                            'Date'      => $blogEntry->Date,
                            'Author'    => $blogEntry->Author,
                            'NewsTitle' => $blogEntry->Title,
                            'Content'   => $content
                        )
                    )
                );
                $newsEntries++;
            }

            if ($newsEntries > 2) {
                break;
            }
        }

        return $news;
    }
}
