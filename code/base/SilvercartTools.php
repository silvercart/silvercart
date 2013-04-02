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
 * @subpackage Base
 */

/**
 * Provides methods for common tasks in SilverCart.
 * 
 * @package Silvercart
 * @subpackage Base
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 16.02.2012
 * @license see license file in modules root directory
 */
class SilvercartTools extends Object {
    
    /**
     * The base url segment
     *
     * @var string
     */
    public static $baseURLSegment = null;
    
    /**
     * Indicates whether the installation is completed or not
     *
     * @var bool 
     */
    public static $isInstallationCompleted = null;
    
    /**
     * Indicates whether the current request is in an isolated environment like
     * dev/build, dev/test, installation, ...
     *
     * @var bool 
     */
    public static $isIsolatedEnvironment = null;
    
    /**
     * Indicates whether the current request is in backend
     *
     * @var bool 
     */
    public static $isBackendEnvironment = null;

    /**
     * Cache for the page hierarchy model.
     *
     * @var ArrayList
     */
    public static $pageHierarchy = array();

    /**
     * Returns the base URL segment that's used for inclusion of css and
     * javascript files via Requirements.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 16.02.2012
     */
    public static function getBaseURLSegment() {
        if (is_null(self::$baseURLSegment)) {
            $baseUrl = Director::baseUrl();

            if ($baseUrl === '/') {
                $baseUrl = '';
            }

            if (!empty($baseUrl) &&
                 substr($baseUrl, -1) != '/') {

                $baseUrl .= '/';
            }
            self::$baseURLSegment = $baseUrl;
        }

        return self::$baseURLSegment;
    }

    /**
     * Remove chars from the given string that are not appropriate for an url
     *
     * @param string $originalString String to convert
     * 
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.06.2012
     */
    public static function string2urlSegment($originalString) {
        if (function_exists('mb_strtolower')) {
            $string = mb_strtolower($originalString);
        } else {
            $string = strtolower($originalString);
        }
        $string     = Object::create('SS_Transliterator')->toASCII($string);
        $string     = str_replace('&amp;','-and-',$string);
        $string     = str_replace('&','-and-',$string);
        $string     = preg_replace('/[^A-Za-z0-9]+/','-',$string);

        if (!$string || $string == '-' || $string == '-1') {
            if (function_exists('mb_strtolower')) {
                $string = mb_strtolower($originalString);
            } else {
                $string = strtolower($originalString);
            }
        }
        $string     = trim($string, '-');
        $remove     = array('ä',    'ö',    'ü',    'Ä',    'Ö',    'Ü',    '/',    '?',    '&',    '#',    '.',    ',',    ' ', '%', '"', "'", '<', '>');
        $replace    = array('ae',   'oe',   'ue',   'Ae',   'Oe',   'Ue',   '-',    '-',    '-',    '-',    '-',    '-',    '',  '',  '',  '',  '',  '');
        $string     = str_replace($remove, $replace, $string);
        return urlencode($string);
    }

    /**
     * Writes a log entry
     *
     * @param string $method The method name of the caller
     * @param string $text   The text to log
     * @param string $class  The class name of the caller
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 06.03.2012
     */
    public static function Log($method, $text, $class = 'undefined') {
        $path = Director::baseFolder() . '/silvercart/log/' . $class . '.log';
        $text = sprintf(
            "%s - Method: '%s' - %s\n",
            date('Y-m-d H:i:s'),
            $method,
            $text
        );
        file_put_contents($path, $text, FILE_APPEND);
    }

    /**
     * Returns the attributed DataObjects as string (limited to 150 chars) by
     * the given DataList.
     * 
     * @param DataList $dataList  DataList to get list for
     * @param string   $dbField   Db field to use to display
     * @param int      $maxLength Maximum string length
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function AttributedDataObject($dataList, $dbField = "Title", $maxLength = 150) {
        $attributedDataObjectStr    = '';
        $attributedDataObjects      = array();

        foreach ($dataList as $component) {
            $attributedDataObjects[] = $component->{$dbField};
        }
        
        if (!empty($attributedDataObjects)) {
            $attributedDataObjectStr = implode(', ', $attributedDataObjects);

            if (strlen($attributedDataObjectStr) > $maxLength) {
                $attributedDataObjectStr = substr($attributedDataObjectStr, 0, $maxLength) . '...';
            }
        }

        return $attributedDataObjectStr;
    }

    /**
     * returns a single page by IdentifierCode
     * used to retrieve links dynamically
     *
     * @param string $identifierCode the classes name
     * 
     * @return SiteTree | false a single object of the site tree; without param the SilvercartFrontPage will be returned
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCode($identifierCode = "SilvercartFrontPage") {
        $page = SilvercartPage::get()->filter('IdentifierCode', $identifierCode)->first();

        if ($page) {
            return $page;
        } else {
            return false;
        }
    }

    /**
     * returns a page link by IdentifierCode
     *
     * @param string $identifierCode the DataObjects IdentifierCode
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 05.04.2012
     */
    public static function PageByIdentifierCodeLink($identifierCode = "SilvercartFrontPage") {
        $page = self::PageByIdentifierCode($identifierCode);
        if ($page === false) {
            return '';
        }
        return $page->Link();
    }
    
    /**
     * Returns the translated singular name of the given object. If no 
     * translation exists the class name will be returned.
     * 
     * @param DataObject $dataObject DataObject to get singular name for
     * 
     * @return string The objects singular name 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public static function singular_name_for($dataObject) {
        if (_t($dataObject->ClassName . '.SINGULARNAME')) {
            return _t($dataObject->ClassName . '.SINGULARNAME');
        } else {
            return ucwords(trim(strtolower(preg_replace('/_?([A-Z])/', ' $1', $dataObject->class))));;
        } 
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @param DataObject $dataObject DataObject to get plural name for
     * 
     * @return string the objects plural name
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.05.2012
     */
    public static function plural_name_for($dataObject) {
        if (_t($dataObject->ClassName . '.PLURALNAME')) {
            return _t($dataObject->ClassName . '.PLURALNAME');
        } else {
            $plural_name = self::singular_name_for($dataObject);
            if (substr($plural_name,-1) == 'e') {
                $plural_name = substr($plural_name,0,-1);
            } elseif (substr($plural_name,-1) == 'y') {
                $plural_name = substr($plural_name,0,-1) . 'ie';
            }
            return ucfirst($plural_name . 's');
        }

    }

    /**
     * Checks if the installation is complete. We assume a complete
     * installation if the Member table has the field "SilvercartShoppingCartID"
     * that is decorated via "SilvercartCustomer".
     * 
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2012
     */
    public static function isInstallationCompleted() {
        if (is_null(self::$isInstallationCompleted)) {
            $installationComplete   = false;

            if ((array_key_exists('SCRIPT_NAME', $_SERVER) && strpos($_SERVER['SCRIPT_NAME'], 'install.php') !== false) ||
                (array_key_exists('QUERY_STRING', $_SERVER) && strpos($_SERVER['QUERY_STRING'], 'successfullyinstalled') !== false) ||
                (array_key_exists('QUERY_STRING', $_SERVER) && strpos($_SERVER['QUERY_STRING'], 'deleteinstallfiles') !== false) ||
                (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'successfullyinstalled') !== false) ||
                (array_key_exists('REQUEST_URI', $_SERVER) && strpos($_SERVER['REQUEST_URI'], 'deleteinstallfiles') !== false)) {
                $installationComplete = false;
            } else {
                $memberFieldList        = array();
                $queryRes               = DB::query("SHOW TABLES");
                if ($queryRes->numRecords() > 0) {
                    $queryRes               = DB::query("SHOW COLUMNS FROM Member");

                    foreach ($queryRes as $key => $value) {
                        $memberFieldList[] = $value['Field'];
                    }

                    if (in_array('SilvercartShoppingCartID', $memberFieldList)) {
                        $installationComplete = true;
                    }
                }
            }
            self::$isInstallationCompleted = $installationComplete;
        }
        return self::$isInstallationCompleted;
    }
    
    /**
     * Checks whether the current request is a special, isolated environment
     *
     * @return boolean 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 25.01.2013
     */
    public static function isIsolatedEnvironment() {
        if (is_null(self::$isIsolatedEnvironment)) {
            self::$isIsolatedEnvironment = false;
            if ((array_key_exists('url', $_REQUEST) && (strpos($_REQUEST['url'], '/Security/login') !== false || strpos($_REQUEST['url'], 'dev/build') !== false || self::isInstallationCompleted() == false)) ||
                (array_key_exists('QUERY_STRING', $_SERVER) && (strpos($_SERVER['QUERY_STRING'], 'dev/tests') !== false || strpos($_SERVER['QUERY_STRING'], 'dev/build') !== false)) ||
                (array_key_exists('SCRIPT_NAME', $_SERVER) && strpos($_SERVER['SCRIPT_NAME'], 'install.php') !== false) ||
                (SapphireTest::is_running_test()) ||
                ($_SERVER['SCRIPT_NAME'] === FRAMEWORK_DIR.'/cli-script.php')) {
                self::$isIsolatedEnvironment = true;
            }
        }
        return self::$isIsolatedEnvironment;
    }
    
    /**
     * Prepares a given email address to use for request handling.
     * CAUTION: This is used for EVERY post requested variable named 'Email'
     * and called in _config.php
     *
     * @param string $emailAddress Email address to prepare
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 04.07.2012
     */
    public static function prepareEmailAddress($emailAddress) {
        $preparedEmailAddress = str_replace('/', '', $emailAddress);
        return $preparedEmailAddress;
    }
    
    /**
     * Checks whether the current url location is in backend
     * 
     * @return boolean
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 26.11.2012
     */
    public static function isBackendEnvironment() {
        if (is_null(self::$isBackendEnvironment)) {
            $isBackendEnvironment = false;

            $controller = Controller::curr();
            $request    = $controller->getRequest();

            if (strpos($request->getVar('url'), 'admin/') === 0 ||
                strpos($request->getVar('url'), '/admin/') === 0) {
                $isBackendEnvironment = true;
            }
            self::$isBackendEnvironment = $isBackendEnvironment;
        }
        return self::$isBackendEnvironment;
    }

    /**
     * Returns a flat array containing the IDs of all child pages of the given page.
     *
     * @param int $pageId The root page ID
     *
     * @return array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 23.08.2012
     */
    public static function getFlatChildPageIDsForPage($pageId) {
        $pageIDs = array($pageId);
        $pageObj = DataObject::get_by_id('SiteTree', $pageId);
        
        if ($pageObj) {
            foreach ($pageObj->Children() as $pageChild) {
                $pageIDs = array_merge($pageIDs, self::getFlatChildPageIDsForPage($pageChild->ID));
            }
        }
        
        return $pageIDs;
    }

    /**
     * Builds a hierarchy from the current page to the top product group page
     * or holder.
     *
     * @param SiteTree $currPage The page to start from
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public static function getPageHierarchy($currPage) {
        if (!array_key_exists('SiteTree_'.$currPage->ID, self::$pageHierarchy)) {
            $level      = 0;
            $hierarchy  = array(
                'SiteTree_'.$currPage->ID => array(
                    'Page'  => $currPage,
                    'Level' => $level
                )
            );

            while ($currPage->hasMethod('getParent') &&
                $currPage->getParent()) {

                $parent = $currPage->getParent();

                if ($parent) {
                    $level++;
                    $hierarchy['SiteTree_'.$parent->ID] = array(
                        'Page'  => $parent,
                        'Level' => $level
                    );
                    $currPage = $parent;
                } else {
                    break;
                }
            }

            self::$pageHierarchy['SiteTree_'.$currPage->ID] = array();

            foreach ($hierarchy as $pageID => $pageInfo) {
                self::$pageHierarchy['SiteTree_'.$currPage->ID][$pageID] = array(
                    'Page'  => $pageInfo['Page'],
                    'Level' => ($pageInfo['Level'] - $level) * -1
                );
            }
        }

        return self::$pageHierarchy['SiteTree_'.$currPage->ID];
    }

    /**
     * Removes a prefix from a checkout address data array.
     *
     * @param string $prefix Prefix
     * @param array  $data   Checkout address data
     *
     * @return array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 2012-12-14
     */
    public static function extractAddressDataFrom($prefix, $data) {
        $addressData = array();
        $dataFields = array(
            $prefix.'_TaxIdNumber'      => 'TaxIdNumber',
            $prefix.'_Company'          => 'Company',
            $prefix.'_Salutation'       => 'Salutation',
            $prefix.'_FirstName'        => 'FirstName',
            $prefix.'_Surname'          => 'Surname',
            $prefix.'_Addition'         => 'Addition',
            $prefix.'_Street'           => 'Street',
            $prefix.'_StreetNumber'     => 'StreetNumber',
            $prefix.'_Postcode'         => 'Postcode',
            $prefix.'_City'             => 'City',
            $prefix.'_Phone'            => 'Phone',
            $prefix.'_PhoneAreaCode'    => 'PhoneAreaCode',
            $prefix.'_Fax'              => 'Fax',
            $prefix.'_Country'          => 'CountryID',
            $prefix.'_PostNumber'       => 'PostNumber',
            $prefix.'_Packstation'      => 'Packstation',
            $prefix.'_IsPackstation'    => 'IsPackstation',
        );

        if (is_array($data)) {
            foreach ($dataFields as $shippingFieldName => $dataFieldName) {
                if (isset($data[$shippingFieldName])) {
                    $addressData[$dataFieldName] = $data[$shippingFieldName];
                }
            }
        }

        if (array_key_exists('TaxIdNumber', $addressData) &&
            array_key_exists('Company', $addressData) &&
            !empty($addressData['TaxIdNumber']) &&
            !empty($addressData['Company'])) {

            $addressData['isCompanyAddress'] = true;
        } else {
            $addressData['isCompanyAddress'] = false;
        }

        return $addressData;
    }

    /**
     * Tries to find the given page ID in the page hierarchy structure.
     *
     * @param int $searchPageID The page ID to find
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public static function findPageIdInHierarchy($searchPageID) {
        $foundPageId = false;
        $hierarchy   = self::getPageHierarchy(Controller::curr());

        foreach ($hierarchy as $pageID => $pageInfo) {
            if ($pageInfo['Page']->ID === $searchPageID) {
                $foundPageId = true;
                break;
            }
        }

        return $foundPageId;
    }

    /**
     * Tries to find the given page ID in the page hierarchy structure and
     * returns the corresponding page.
     *
     * @param int $searchPageID The page ID to find
     *
     * @return SiteTree
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public static function getPageLevelByPageId($searchPageID) {
        $level     = false;
        $hierarchy = self::getPageHierarchy(Controller::curr());

        foreach ($hierarchy as $pageID => $pageInfo) {
            if ($pageInfo['Page']->ID == $searchPageID) {
                $level = $pageInfo['Level'];
                break;
            }
        }

        return $level;
    }

    /**
     * Checks if the given page IDs are siblings of the same level.
     *
     * @param int $checkPageID1 The first page ID to check
     * @param int $checkPageID2 The second page ID to check
     *
     * @return boolean
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 18.10.2012
     */
    public static function pageIsSiblingOf($checkPageID1, $checkPageID2) {
        $isSibling = false;

        $level1 = self::getPageLevelByPageId($checkPageID1);
        $level2 = self::getPageLevelByPageId($checkPageID2);

        if ($level1 === $level2) {
            $isSibling = true;
        }

        return $isSibling;
    }
    
    /**
     * Checks on silvercart.org whether there is an update available.
     * 
     * @return boolean
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.01.2013
     */
    public static function checkForUpdate() {
        $updateAvailable = false;
        try {
            $checkForUpdateUrl = sprintf(
                    'http://www.silvercart.org/scsc/checkForUpdate/%s.%s',
                    SilvercartConfig::SilvercartVersion(),
                    SilvercartConfig::SilvercartMinorVersion()
            );
            $ch = curl_init($checkForUpdateUrl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_REFERER, Director::absoluteBaseURL());
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
        } catch (Exception $exc) {
            $result = 0;
        }
        if ((int) $result == 1) {
            // update available
            $updateAvailable = true;
        }
        return $updateAvailable;
    }
}
