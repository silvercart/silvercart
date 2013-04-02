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
 * @subpackage ModelAdmins
 */

/**
 * ModelAdmin for SilvercartConfig.
 * 
 * @package Silvercart
 * @subpackage ModelAdmins
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2012 pixeltricks GmbH
 * @since 16.01.2012
 * @license see license file in modules root directory
 */
class SilvercartConfigAdmin extends SilvercartLeftAndMain {

    /**
     * The code of the menu under which this admin should be shown.
     * 
     * @var string
     */
    public static $menuCode = 'config';

    /**
     * The section of the menu under which this admin should be grouped.
     * 
     * @var string
     */
    public static $menuSortIndex = 10;

    /**
     * The URL segment
     *
     * @var string
     */
    public static $url_segment = 'silvercart-config';

    /**
     * The menu title
     *
     * @var string
     */
    public static $menu_title = 'Configuration';

    /**
     * Managed models
     *
     * @var array
     */
    public static $managed_models = array(
        'SilvercartConfig' => array(
            'title'                    => 'SilvercartConfig',
            'enableFirstEntryAutoLoad' => true
        ),
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
     * Provides hook for decorators, so that they can overwrite css
     * and other definitions.
     * 
     * @param bool $skipUpdateInit Set to true to skip the parents updateInit extension
     * 
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>, Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function init($skipUpdateInit = false) {
        parent::init($skipUpdateInit);

        Requirements::css(CMS_DIR . '/css/WidgetAreaEditor.css');
        Requirements::javascript(CMS_DIR . '/javascript/WidgetAreaEditor.js');

        $this->extend('updateInit');
    }
    
    /**
     * Builds and returns the edit form
     * 
     * @param int       $id     Not used. Available because of inheritance.
     * @param FieldList $fields Not used. Available because of inheritance.
     * 
     * @return Form
     */
    public function getEditForm($id = null, $fields = null) {
        $config     = SilvercartConfig::getConfig();
        $fields     = $config->getCMSFields();
        $actions    = $config->getCMSActions();
        
        $form = new Form($this, 'EditForm', $fields, $actions);
        $form->addExtraClass('root-form');
        $form->addExtraClass('cms-edit-form cms-panel-padded center cms-tabset');
        // don't add data-pjax-fragment=CurrentForm, its added in the content template instead

        if ($form->Fields()->hasTabset()) {
            $form->Fields()->findOrMakeTab('Root')->setTemplate('CMSTabSet');
        }
        $form->setHTMLID('Form_EditForm');
        $form->loadDataFrom($config);
        $form->setTemplate($this->getTemplatesWithSuffix('_EditForm'));

        // Use <button> to allow full jQuery UI styling
        $actionFields = $actions->dataFields();
        if ($actionFields) {
            foreach ($actionFields as $action) {
                $action->setUseButtonTag(true);
            }
        }

        $this->extend('updateEditForm', $form);

        return $form;
    }

    /**
     * Save the current sites {@link SiteConfig} into the database
     *
     * @param array $data Data to save
     * @param Form  $form Form to extract data from
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function save_scconfig($data, $form) {
        $config = SilvercartConfig::getConfig();
        $form->saveInto($config);
        $config->write();

        $this->response->addHeader('X-Status', rawurlencode(_t('LeftAndMain.SAVEDUP', 'Saved.')));
        return $this->getResponseNegotiator()->respond($this->request);
    }
    
    /**
     * Adds example data to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.02.2013
     */
    public function add_example_data() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestData();
        if ($result) {
            $responseText   = _t('SilvercartConfig.ADDED_EXAMPLE_DATA');
        } else {
            $responseText   = _t('SilvercartConfig.EXAMPLE_DATA_ALREADY_ADDED');
        }
        $this->response->addHeader('X-Status', rawurlencode($responseText));
        return $this->getResponseNegotiator()->respond($this->request);
    }
    
    /**
     * Adds example configuration to SilverCart when triggered in ModelAdmin.
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 21.02.2013
     */
    public function add_example_config() {
        SilvercartConfig::enableTestData();
        $result = SilvercartRequireDefaultRecords::createTestConfiguration();
        if ($result) {
            $responseText   = _t('SilvercartConfig.ADDED_EXAMPLE_CONFIGURATION');
        } else {
            $responseText   = _t('SilvercartConfig.EXAMPLE_CONFIGURATION_ALREADY_ADDED');
        }
        $this->response->addHeader('X-Status', rawurlencode($responseText));
        return $this->getResponseNegotiator()->respond($this->request);
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
 * @license see license file in modules root directory
 * 
 * @deprecated should be outsourced into a SilvercartTask or deleted.
 */
class SilvercartConfigAdmin_RecordController {
    
    /**
     * Cleans the SilverCart database
     *
     * @return SS_HTTPResponse 
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 07.09.2011
     * 
     * @deprecated should be outsourced into a SilvercartTask or deleted.
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
            $silvercartProducts = SilvercartProduct::get();

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

