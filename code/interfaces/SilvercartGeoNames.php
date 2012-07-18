<?php
/**
 * Copyright 2010, 2011 pixeltricks GmbH
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
 * @subpackage Interfaces
 */

/**
 * Interface for GeoNames.
 * GeoNames provides a detailed database of geo informations. The primary use case
 * for us is to get up-to-date country informations (name, ISO2, ISO3, etc.).
 * The service itself is for free with an optional payed acces (for warranted
 * availability, faster response times, etc.).
 * Further information on http://www.geonames.org/.
 *
 * @package Silvercart
 * @subpackage Interfaces
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.03.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @see http://www.geonames.org/
 */
class SilvercartGeoNames extends SilvercartInterface {

    protected $language;

    protected $locale;

    /**
     * Overwrites the default constructor. A Password is not needed for this
     * interface.
     *
     * @param string $user Username
     * @param string $api  API-URL
     */
    public function __construct($user = null, $api = null) {
        parent::__construct($user, null, $api);
        $this->setLanguageByLocale(Translatable::get_current_locale());
    }

    /**
     * Updates the country information for all countries. Creates new countries
     * if not exists.
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 24.03.2011
     */
    public function countryInfo() {
        $countryInfo = file($this->getApiUrlForService('countryInfoCSV'));
        foreach ($countryInfo as $key => $line) {
            $trimmed = trim($line);
            if ($key == 0 || empty($trimmed)) {
                continue;
            }

            list(
                $ISO2,
                $ISO3,
                $ISON,
                $FIPS,
                $Title,
                $Capital,
                $AreaInSqKm,
                $Population,
                $Continent,
                $Languages,
                $Currency,
                $GeoNameId,
            ) = explode("\t", $line);

            $country = DataObject::get_one('SilvercartCountry', sprintf("`SilvercartCountry`.`ISO2`='%s'", $ISO2));
            if (!$country) {
                $country = new SilvercartCountry();
            }
            $country->ISO2      = $ISO2;
            $country->ISO3      = $ISO3;
            $country->FIPS      = $FIPS;
            $country->ISON      = $ISON;
            $country->Continent = $Continent;
            $country->Currency  = $Currency;
            $country->write();
            
            SilvercartConfig::$useDefaultLanguageAsFallback = false;
            $language = SilvercartLanguageHelper::getLanguage($country->getLanguageRelation(), $this->getLocale());
            SilvercartConfig::$useDefaultLanguageAsFallback = true;
            if (!$language) {
                $language = new SilvercartCountryLanguage();
                $language->Locale               = $this->getLocale();
                $language->SilvercartCountryID  = $country->ID;
            }
            $language->Title = $Title;
            $language->write();
        }

    }

    /**
     * Builds and returns the API URL. The API URL consists of URL, servicenema,
     * username and lang.
     *
     * @param string $service The name of the service to call.
     *
     * @return string
     */
    protected function getApiUrlForService($service) {
        return $this->getApi() . $service . '?username=' . $this->getUser() . '&lang=' . $this->getLanguage();
    }

    /**
     * Returns the language
     *
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Sets the language
     *
     * @param string $language language
     *
     * @return void
     */
    public function setLanguage($language) {
        $this->language = $language;
    }

    /**
     * Sets the language by a given locale
     *
     * @param string $locale locale
     *
     * @return void
     */
    public function setLanguageByLocale($locale) {
        $this->setLocale($locale);
        $language = substr($locale, 0, 2);
        $this->setLanguage($language);
    }

    /**
     * Returns the locale
     *
     * @return string
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Sets the locale
     *
     * @param string $locale locale
     *
     * @return void
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }
    
}