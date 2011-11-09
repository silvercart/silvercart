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
 * @subpackage Backend
 */

/**
 * The Silvercart configuration backend.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 31.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopConfigurationAdmin extends ModelAdmin {
    
    /**
     * We use a custom result table class name.
     *
     * @var string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 05.10.2011
     */
    protected $resultsTableClassName = 'SilvercartTableListField';
    
    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $managed_models = array(
        'SilvercartConfig' => array(
            'enableFirstEntryAutoLoad' => true
        ),
        'SilvercartPaymentMethod' => array(
            'collection_controller' => 'SilvercartPaymentMethod_CollectionController'
        ),
        'SilvercartShippingMethod',
        'SilvercartCarrier',
        'SilvercartShopEmail',
        'SilvercartWidgetSet' => array(
            'record_controller' => 'SilvercartHasManyOrderField_RecordController'
        ),
        'SilvercartCountry',
        'SilvercartZone',
        'SilvercartTax',
        'SilvercartOrderStatus',
        'SilvercartAvailabilityStatus',
        'SilvercartProductCondition',
        'SilvercartNumberRange',
        'SilvercartQuantityUnit',
        'SilvercartDeeplink',
        'SilvercartInboundShoppingCartTransfer',
        'SilvercartWidget'
    );
    /**
     * List of managed models with disabled creation and import form
     *
     * @var array
     */
    public static $disable_creation_and_import_for = array(
        'SilvercartConfig',
    );
    /**
     * List of managed models with resetted search form
     *
     * @var array
     */
    public static $reset_search_form_for = array(
        'SilvercartConfig',
    );
    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $url_segment = 'silvercart-configuration';
    /**
     * The menu title
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $menu_title = 'Silvercart Konfiguration';
    /**
     * The collection controller class to use for the shop configuration.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public static $collection_controller_class = 'SilvercartShopConfigurationAdmin_CollectionController';
    /**
     * The record controller class to use for the shop configuration.
     *
     * @var string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public static $record_controller_class = 'SilvercartShopConfigurationAdmin_RecordController';

    public static $menu_priority = -1;

    /**
     * constructor
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function __construct() {
        self::$menu_title = _t('SilvercartShopConfigurationAdmin.SILVERCART_CONFIG', 'SilverCart Konfiguration');
        parent::__construct();
    }

    /**
     * We load some additional javascript and css files here.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 27.05.2011
     */
    public function init() {
        parent::init();
        
        Requirements::css(CMS_DIR . '/css/WidgetAreaEditor.css');
        Requirements::javascript(CMS_DIR . '/javascript/WidgetAreaEditor.js');
    }
}

/**
 * Modifies the model admin search panel.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 31.01.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopConfigurationAdmin_CollectionController extends ModelAdmin_CollectionController {

    /**
     * Return a modified search form.
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 31.01.2011
     */
    public function SearchForm() {
        $form = parent::SearchForm();
        if (in_array($this->getModelClass(), SilvercartShopConfigurationAdmin::$reset_search_form_for)) {
            $form->setFields(new FieldSet());
            $form->Actions()->fieldByName('action_search')->Title = _t('SilvercartConfig.SHOW_CONFIG');
            $form->Actions()->removeByName('action_clearsearch');
        }
        return $form;
    }

    /**
     * Disable the creation of SilvercartUpdate DataObjects.
     *
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 29.03.2011
     */
    public function alternatePermissionCheck() {
        $result = true;
        if (in_array($this->getModelClass(), SilvercartShopConfigurationAdmin::$disable_creation_and_import_for)) {
            $result = false;
            $this->showImportForm = false;
        }
        return $result;
    }

}

/**
 * Modifies the model admin action handling and adds additional model admin
 * actions to create test data and configuration.
 *
 * @package Silvercart
 * @subpackage Backend
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 02.07.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartShopConfigurationAdmin_RecordController extends ModelAdmin_RecordController {
    
    /**
     * Adds the abillity to execute additional actions to the model admin's
     * action handling.
     *
     * @param SS_HTTPRequest $request the request object
     * 
     * @return mixed
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public function handleAction(SS_HTTPRequest $request) {
        $vars = $request->getVars();
        if (array_key_exists('addExampleData', $vars)) {
            return $this->addExampleData();
        } elseif (array_key_exists('addExampleConfig', $vars)) {
            return $this->addExampleConfig();
        } elseif (array_key_exists('cleanDataBase', $vars)) {
            return $this->cleanDataBase();
        } else {
            return parent::handleAction($request);
        }
        
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public function addExampleData() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestData();
        if ($result) {
            $extraClass = 'addedExampleData';
        } else {
            $extraClass = 'exampleDataAlreadyAdded';
        }
        if ($this->currentRecord) {
            if (Director::is_ajax()) {
                $form = $this->EditForm();
                $form->addExtraClass($extraClass);
                return new SS_HTTPResponse(
                    $form->forAjaxTemplate(), 
                    200, 
                    _t('SilvercartConfig.ADDED_EXAMPLE_DATA', "Added Example Data")
                );
            } else {
                // This is really quite ugly; fixing it will require a change in the way that customise() works. :-(
                return $this->parentController->parentController->customise(array(
                        'Right' => $this->parentController->parentController->customise(array(
                                'EditForm' => $this->EditForm()
                        ))->renderWith('ModelAdmin_right')
                ))->renderWith(array('ModelAdmin','LeftAndMain'));
                return ;
            }
        } else {
            return _t('ModelAdmin.ITEMNOTFOUND');
        }
    }
    
    /**
     * Adds example configuration to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 02.07.2011
     */
    public function addExampleConfig() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestConfiguration();
        if ($result) {
            $extraClass = 'addedExampleConfig';
        } else {
            $extraClass = 'exampleConfigAlreadyAdded';
        }
        if ($this->currentRecord) {
            if (Director::is_ajax()) {
                $form = $this->EditForm();
                $form->addExtraClass($extraClass);
                return new SS_HTTPResponse(
                    $form->forAjaxTemplate(), 
                    200, 
                    _t('SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION', "Added Example Configuration")
                );
            } else {
                // This is really quite ugly; to fix will require a change in the way that customise() works. :-(
                return $this->parentController->parentController->customise(array(
                        'Right' => $this->parentController->parentController->customise(array(
                                'EditForm' => $this->EditForm()
                        ))->renderWith('ModelAdmin_right')
                ))->renderWith(array('ModelAdmin','LeftAndMain'));
                return ;
            }
        } else {
            return _t('ModelAdmin.ITEMNOTFOUND', "I can't find that item");
        }
    }
    
    /**
     * Cleans the SilverCart database
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2011
     */
    public function cleanDataBase() {
        // check SilvercartImages
        $limit = '';
        $start = 0;
        $processingValue = 100;
        if (array_key_exists('start', $_GET)) {
            $start = $_GET['start'];
        }
        if ($start == 0) {
            if (Session::get('cleanDataBase.inProgress')) {
                $start = Session::get('cleanDataBase.startIndex');
                Session::set('cleanDataBase.startTime', time() - Session::get('cleanDataBase.lastLog') - Session::get('cleanDataBase.startTime'));
            } else {
                Session::clear('cleanDataBase');
                Session::save();
            }
        }
        
        if (!Session::get('cleanDataBase.numberOfImages')) {
            $query = new SQLQuery(
                array("COUNT(*) AS numberOfImages"),
                array("SilvercartImage")
            );

            $numberOfImages = $query->execute()->value();
            Session::set('cleanDataBase.numberOfImages', $numberOfImages);
            Session::save();
        } else {
            $numberOfImages = Session::get('cleanDataBase.numberOfImages');
        }
        
        $processedImages = $start + $processingValue;
        if ($processedImages >= $numberOfImages) {
            $processedImages = $numberOfImages;
            $callback = '';
            $progressMessage = _t('SilvercartConfig.CLEANED_DATABASE');
            Session::clear('cleanDataBase');
            Session::save();
        } else {
            
            if (!Session::get('cleanDataBase.deletedImages') && Session::get('cleanDataBase.deletedImages') !== 0) {
                Session::set('cleanDataBase.deletedImages',                         0);
                Session::set('cleanDataBase.deletedImagesBecauseOfMissingProduct',  0);
                Session::set('cleanDataBase.deletedImagesBecauseOfBrokenImage',     0);
                Session::set('cleanDataBase.deletedImagesBecauseOfMissingImage',    0);
                Session::set('cleanDataBase.reAssignedImages',                      0);
                Session::set('cleanDataBase.startTime',                             time());
                Session::set('cleanDataBase.remainingTime',                         0);
                Session::save();
            }
            $limit = ($start - Session::get('cleanDataBase.deletedImages')) . ',' . $processingValue;
            
            $newStart = $processedImages;
            $percent = floor($processedImages / ($numberOfImages/100));
            
            $callback = '';
            $callback .= '<script type="text/javascript">';
            $callback .= '(function($) {';
            $callback .= '$("#right input[name=action_cleanDataBase]").addClass("loading");';
            $callback .= 'var cleanDataBaseButton = $("#right input[name=action_cleanDataBase]");';
            $callback .= 'var form = $("#right form");';
            $callback .= 'var formAction = form.attr("action") + "?" + $(cleanDataBaseButton).attr("name").replace("action_", "") + "&start=" + ' . $newStart . ';';
            $callback .= '$.post(formAction, form.formToArray(), function(result){';
            $callback .= '$("#right #ModelAdminPanel").html(result);';
            $callback .= 'statusMessage(ss.i18n._t("SilvercartConfig.CLEANED_DATABASE"), "good");';
            $callback .= '$(cleanDataBaseButton).removeClass("loading");';
            $callback .= 'Behaviour.apply();';
            $callback .= 'if(window.onresize) window.onresize();';
            $callback .= '}, "html");';
            $callback .= '})(jQuery);';
            $callback .= '</script>';

            $deletedImages                          = Session::get('cleanDataBase.deletedImages');
            $deletedImagesBecauseOfMissingProduct   = Session::get('cleanDataBase.deletedImagesBecauseOfMissingProduct');
            $deletedImagesBecauseOfBrokenImage      = Session::get('cleanDataBase.deletedImagesBecauseOfBrokenImage');
            $deletedImagesBecauseOfMissingImage     = Session::get('cleanDataBase.deletedImagesBecauseOfMissingImage');
            $reAssignedImages                       = Session::get('cleanDataBase.reAssignedImages');
            $silvercartImages = DataObject::get('SilvercartImage', '', '', '', $limit);
            $silvercartProducts = DataObject::get('SilvercartProduct');

            foreach ($silvercartImages as $silvercartImage) {
                if ($silvercartImage->SilvercartProductID != 0) {
                    // check existance of related product
                    if (!$silvercartProducts->find('ID', $silvercartImage->SilvercartProductID)) {
                        // related product does not exist. Delete image!
                        $silvercartImage->delete();
                        $deletedImages++;
                        $deletedImagesBecauseOfMissingProduct++;
                        continue;
                    } else {
                        // related product exists. Check if image is broken
                        if (!$silvercartImage->Image() ||
                            $silvercartImage->Image()->ID == 0) {
                            // related image object is broken. Delete image!
                            $silvercartImage->delete();
                            $deletedImages++;
                            $deletedImagesBecauseOfBrokenImage++;
                        } else {
                            // related image object is OK. Check filesystem
                            if (!file_exists($silvercartImage->Image()->getFullPath())) {
                                // image file does not exist. Delete image!
                                $silvercartImage->Image()->delete();
                                $silvercartImage->delete();
                                $deletedImages++;
                                $deletedImagesBecauseOfMissingImage++;
                                continue;
                            }

                        }
                    }
                }
            }
            
            
            $timeSpent = time() - Session::get('cleanDataBase.startTime');
            $percentGtZero = $percent;
            if ($percentGtZero == 0) {
                $percentGtZero++;
            }
            $remainingTime = ($timeSpent / $percentGtZero) * (100 - $percent);
            
            $seconds = $remainingTime % 60;
            $minutes = floor($remainingTime/60);
            if ($minutes == 1) {
                $minuteString = _t('Silvercart.MIN');
            } else {
                $minuteString = _t('Silvercart.MINS');
            }
            if ($seconds == 1) {
                $secondsString = _t('Silvercart.SEC');
            } else {
                $secondsString = _t('Silvercart.SECS');
            }
            $remainingTimeString = $minutes . ' ' . $minuteString . ' ' . $seconds . ' ' . $secondsString;

            Session::set('cleanDataBase.lastLog',                               time());
            Session::set('cleanDataBase.startIndex',                            $newStart);
            Session::set('cleanDataBase.deletedImages',                         $deletedImages);
            Session::set('cleanDataBase.deletedImagesBecauseOfMissingProduct',  $deletedImagesBecauseOfMissingProduct);
            Session::set('cleanDataBase.deletedImagesBecauseOfBrokenImage',     $deletedImagesBecauseOfBrokenImage);
            Session::set('cleanDataBase.deletedImagesBecauseOfMissingImage',    $deletedImagesBecauseOfMissingImage);
            Session::set('cleanDataBase.reAssignedImages',                      $reAssignedImages);
            Session::set('cleanDataBase.remainingTime',                         $remainingTime);
            Session::save();
            
            $progressMessage = sprintf(
                    _t('SilvercartConfig.CLEAN_DATABASE_INPROGRESS'),
                    $processedImages,
                    $numberOfImages,
                    $percent,
                    $remainingTimeString
            );
        }
        
        
        if ($this->currentRecord) {
            if (Director::is_ajax()) {
                $form = $this->EditForm();
                $fields = $form->Fields();
                $reportToDisplay = sprintf(
                        _t('SilvercartConfig.CLEANED_DATABASE_REPORT'),
                        $progressMessage,
                        Session::get('cleanDataBase.deletedImages'),
                        Session::get('cleanDataBase.deletedImagesBecauseOfMissingProduct'),
                        Session::get('cleanDataBase.deletedImagesBecauseOfBrokenImage'),
                        Session::get('cleanDataBase.deletedImagesBecauseOfMissingImage'),
                        Session::get('cleanDataBase.reAssignedImages')
                ) . $callback;
                $cleanDataBaseReportField = new LiteralField('', $reportToDisplay);
                $fields->addFieldToTab('Root.General.Clean', $cleanDataBaseReportField);
                $fields->dataFieldByName('cleanDataBaseStartIndex')->setValue($newStart);
                return new SS_HTTPResponse(
                    $form->forAjaxTemplate(), 
                    200, 
                    _t('SilvercartConfig.CLEANED_DATABASE')
                );
            } else {
                // This is really quite ugly; to fix will require a change in the way that customise() works. :-(
                return $this->parentController->parentController->customise(array(
                        'Right' => $this->parentController->parentController->customise(array(
                                'EditForm' => $this->EditForm()
                        ))->renderWith('ModelAdmin_right')
                ))->renderWith(array('ModelAdmin','LeftAndMain'));
                return ;
            }
        } else {
            return _t('ModelAdmin.ITEMNOTFOUND', "I can't find that item");
        }
    }
}