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
 * @subpackage Metrics
 */

/**
 * Field for displaying the number of orders by day.
 * 
 * @package Silvercart
 * @subpackage Metrics
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 22.01.2012
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartMetricsFieldOrdersByDay extends SilvercartMetricsField {

    /**
     * Contains retrieved orders for caching purposes.
     * 
     * @var DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    protected $orders = null;

    /**
     * Returns all orders for the requested time span.
     * 
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function getOrders($daysToRetrieve = 30) {
        if ($this->orders !== null) {
            return $this->orders;
        }

        $orders    = new DataObjectSet();
        $startDate = date('Y-m-d 00:00:00', mktime(0, 0, 0, date('m'), date('d') - $daysToRetrieve, date('Y')));
        $endDate   = date('Y-m-d 23:59:59', mktime());

        $records = DB::query(
            sprintf(
                "SELECT
                    COUNT(ID) AS numberOfOrders,
                    DATE(Created) AS groupedDate
                 FROM
                    SilvercartOrder
                 WHERE
                    Created > '%s' AND Created < '%s'
                 GROUP BY
                    DAY(Created)",
                 $startDate,
                 $endDate
            )
        );

        if ($records) {
            foreach ($records as $record) {
                $orders->push(
                    new DataObject(
                        array(
                            'groupedDate'    => $record['groupedDate'],
                            'numberOfOrders' => $record['numberOfOrders']
                        )
                    )
                );
            }
        }

        $this->orders = $orders;

        return $orders;
    }

    /**
     * Returns a string containing a javascript array for the jqplot date axis.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function getOrderLine() {
        $orderLine = '';
        $orders    = $this->getOrders();

        if ($orders) {
            foreach ($orders as $order) {
                $orderLine .= sprintf(
                    "['%s', %d],",
                    date('Y-m-d', strtotime($order->groupedDate)),
                    $order->numberOfOrders
                );
            }
        }

        if (!empty($orderLine)) {
            $orderLine = substr($orderLine, 0, -1);
        }

        return $orderLine;
    }

    /**
     * Indicates wether there are orders or not.
     * 
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function HasOrderLine() {
        $hasOrderLine = false;
        $orders       = $this->getOrders();

        if ($orders &&
            $orders->Count() > 0) {

            $hasOrderLine = true;
        }

        return $hasOrderLine;
    }

    /**
     * Returns the title of this chart.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function getChartHeadline() {
        return _t('SilvercartMetricsFieldOrdersByDay.CHART_HEADLINE');
    }

    /**
     * Returns the title of this field.
     * 
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 25.01.2012
     */
    public function getFieldHeadline() {
        return _t('SilvercartMetricsFieldOrdersByDay.FIELD_HEADLINE');
    }
}
