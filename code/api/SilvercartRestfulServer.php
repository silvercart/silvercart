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
 * @subpackage API
 */

/**
 * Adds basic authentication to RestfulServer.
 *
 * @package Silvercart
 * @subpackage API
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 2013-02-22
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartRestfulServer extends RestfulServer {

    /**
     * Defines the URL for this RestfulServer.
     *
     * @var string
     * @see silvercart/_config.php => Director::addRules(...)
     */
    protected static $api_base = "api/silvercart/";

    /**
     * Authenticates the user.
     *
     * @return bool|Member
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-22
     */
    protected function authenticate() {
        /*
         * The following block is a work-around for the broken apache/fcgi
         * http basic authentication with PHP (see https://github.com/symfony/symfony/issues/1813).
         *
         * To make this work you have add the following rewrite rule to your
         * vhosts.conf or .htaccess:
         *
         * RewriteEngine on
         * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L]
         *
         */
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            if (isset($_SERVER['HTTP_AUTHORIZATION']) && (strlen($_SERVER['HTTP_AUTHORIZATION']) > 0)) {
                list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

                if (strlen($_SERVER['PHP_AUTH_USER']) == 0 || strlen($_SERVER['PHP_AUTH_PW']) == 0) {
                    unset($_SERVER['PHP_AUTH_USER']);
                    unset($_SERVER['PHP_AUTH_PW']);
                }
            }
        }

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        $member = MemberAuthenticator::authenticate(
            array(
                'Email'     => $_SERVER['PHP_AUTH_USER'],
                'Password'  => $_SERVER['PHP_AUTH_PW'],
            ),
            null
        );

        if ($member) {
            $member->LogIn(false);
            return $member;
        } else {
            return false;
        }
    }

    /**
     * Returns a SilvercartXMLDataFormatter.
     *
     * @param boolean $includeAcceptHeader Determines wether to inspect and prioritize any HTTP Accept headers
     *
     * @return DataFormatter
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 2013-02-22
     */
    protected function getDataFormatter($includeAcceptHeader = false) {
        $formatter      = new SilvercartXMLDataFormatter();
        $relationDepth  = $this->request->getVar('relationdepth');

        // set custom fields
        if ($customAddFields = $this->request->getVar('add_fields')) {
            $formatter->setCustomAddFields(explode(',',$customAddFields));
        }
        if ($customFields = $this->request->getVar('fields')) {
            $formatter->setCustomFields(explode(',',$customFields));
        }
        $formatter->setCustomRelations(
            $this->getAllowedRelations($this->urlParams['ClassName'])
        );

        $apiAccess = singleton($this->urlParams['ClassName'])->stat('api_access');
        if (is_array($apiAccess)) {
            $formatter->setCustomAddFields(array_intersect((array) $formatter->getCustomAddFields(), (array) $apiAccess['view']));

            if ($formatter->getCustomFields()) {
                $formatter->setCustomFields(array_intersect((array) $formatter->getCustomFields(), (array) $apiAccess['view']));
            } else {
                $formatter->setCustomFields((array) $apiAccess['view']);
            }
            if ($formatter->getCustomRelations()) {
                $formatter->setCustomRelations(array_intersect((array) $formatter->getCustomRelations(), (array) $apiAccess['view']));
            } else {
                $formatter->setCustomRelations((array) $apiAccess['view']);
            }
        }

        // Set the relation depth
        if (!is_numeric($relationDepth)) {
            $relationDepth = 1;
        }

        $formatter->setRelationDepth((int) $relationDepth);
        $formatter->setRelationDetailDepth($formatter->getRelationDepth());

        return $formatter;
    }
}