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
 * Provides a view of items of a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupItemsWidget extends SilvercartWidget {
    
    /**
     * Attributes.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $db = array(
        'FrontTitle'                    => 'VarChar(255)',
        'FrontContent'                  => 'HTMLText',
        'numberOfProductsToShow'        => 'Int',
        'numberOfProductsToFetch'       => 'Int',
        'fetchMethod'                   => "Enum('random,sortOrderAsc,sortOrderDesc','random')",
        'SilvercartProductGroupPageID'  => 'Int',
        'useListView'                   => 'Boolean',
        'isContentView'                 => 'Boolean',
        'Autoplay'                      => 'Boolean(1)',
        'autoPlayDelayed'               => 'Boolean(1)',
        'autoPlayLocked'                => 'Boolean(0)',
        'buildArrows'                   => 'Boolean(1)',
        'buildNavigation'               => 'Boolean(1)',
        'buildStartStop'                => 'Boolean(1)',
        'slideDelay'                    => 'Int',
        'stopAtEnd'                     => 'Boolean(0)',
        'transitionEffect'              => "Enum('fade,horizontalSlide,verticalSlide','fade')",
        'useSlider'                     => "Boolean(0)"
    );
    
    /**
     * Set default values.
     * 
     * @var array
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public static $defaults = array(
        'numberOfProductsToShow'    => 5,
        'numberOfProductsToFetch'   => 5,
        'slideDelay'                => 5000
    );
    
    /**
     * Returns the input fields for this widget.
     * 
     * @return FieldSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function getCMSFields() {
        $fields = new FieldSet();
        
        $productGroupField = new SilvercartGroupedDropdownField(
            'SilvercartProductGroupPageID',
            _t('SilvercartProductGroupItemsWidget.STOREADMIN_FIELDLABEL'),
            SilvercartProductGroupHolder_Controller::getRecursiveProductGroupsForGroupedDropdownAsArray(null, true),
            $this->SilvercartProductGroupPageID
        );
        $titleField                 = new TextField('FrontTitle', _t('SilvercartProductGroupItemsWidget.FRONTTITLE'));
        $contentField               = new TextareaField('FrontContent', _t('SilvercartProductGroupItemsWidget.FRONTCONTENT'), 10);
        $numberOfProductsShowField  = new TextField('numberOfProductsToShow', _t('SilvercartProductGroupItemsWidget.STOREADMIN_NUMBEROFPRODUCTSTOSHOW'));
        $numberOfProductsFetchField = new TextField('numberOfProductsToFetch', _t('SilvercartProductGroupItemsWidget.STOREADMIN_NUMBEROFPRODUCTSTOFETCH'));
        $fetchMethod                = new DropdownField(
            'fetchMethod',
            _t('SilvercartProductGroupItemsWidget.FETCHMETHOD'),
            array(
                'random'        => _t('SilvercartProductGroupItemsWidget.FETCHMETHOD_RANDOM'),
                'sortOrderAsc'  => _t('SilvercartProductGroupItemsWidget.FETCHMETHOD_SORTORDERASC'),
                'sortOrderDesc' => _t('SilvercartProductGroupItemsWidget.FETCHMETHOD_SORTORDERDESC')
            )
        );
        $useListViewField           = new CheckboxField('useListView', _t('SilvercartProductGroupItemsWidget.USE_LISTVIEW'));
        $isContentView              = new CheckboxField('isContentView', _t('SilvercartProductGroupItemsWidget.IS_CONTENT_VIEW'));
        $useSlider                  = new CheckboxField('useSlider', _t('SilvercartProductGroupItemsWidget.USE_SLIDER'));
        $autoplay                   = new CheckboxField('Autoplay', _t('SilvercartProductGroupItemsWidget.AUTOPLAY'));
        $slideDelay                 = new TextField('slideDelay', _t('SilvercartProductGroupItemsWidget.SLIDEDELAY'));
        $buildArrows                = new CheckboxField('buildArrows', _t('SilvercartProductGroupItemsWidget.BUILDARROWS'));
        $buildNavigation            = new CheckboxField('buildNavigation', _t('SilvercartProductGroupItemsWidget.BUILDNAVIGATION'));
        $buildStartStop             = new CheckboxField('buildStartStop', _t('SilvercartProductGroupItemsWidget.BUILDSTARTSTOP'));
        $autoPlayDelayed            = new CheckboxField('autoPlayDelayed', _t('SilvercartProductGroupItemsWidget.AUTOPLAYDELAYED'));
        $autoPlayLocked             = new CheckboxField('autoPlayLocked', _t('SilvercartProductGroupItemsWidget.AUTOPLAYLOCKED'));
        $stopAtEnd                  = new CheckboxField('stopAtEnd', _t('SilvercartProductGroupItemsWidget.STOPATEND'));
        $transitionEffect           = new DropdownField(
            'transitionEffect',
            _t('SilvercartProductGroupItemsWidget.TRANSITIONEFFECT'),
            array(
                'fade'              => _t('SilvercartProductGroupItemsWidget.TRANSITION_FADE'),
                'horizontalSlide'   => _t('SilvercartProductGroupItemsWidget.TRANSITION_HORIZONTALSLIDE'),
                'verticalSlide'     => _t('SilvercartProductGroupItemsWidget.TRANSITION_VERTICALSLIDE')
            )
        );
        
        $rootTabSet = new TabSet('SilvercartProductGroupItemsWidget');
        $basicTab   = new Tab('basic', _t('SilvercartProductGroupItemsWidget.CMS_BASICTABNAME'));
        $sliderTab  = new Tab('anythingSlider', _t('SilvercartProductGroupItemsWidget.CMS_SLIDERTABNAME'));
        
        $fields->push($rootTabSet);
        $rootTabSet->push($basicTab);
        $rootTabSet->push($sliderTab);
        
        $basicTab->push($titleField);
        $basicTab->push($contentField);
        $basicTab->push($productGroupField);
        $basicTab->push($numberOfProductsShowField);
        $basicTab->push($numberOfProductsFetchField);
        $basicTab->push($fetchMethod);
        $basicTab->push($useListViewField);
        $basicTab->push($isContentView);
        
        $sliderTab->push($useSlider);
        $sliderTab->push($autoplay);
        $sliderTab->push($slideDelay);
        $sliderTab->push($buildArrows);
        $sliderTab->push($buildNavigation);
        $sliderTab->push($buildStartStop);
        $sliderTab->push($autoPlayDelayed);
        $sliderTab->push($autoPlayLocked);
        $sliderTab->push($stopAtEnd);
        $sliderTab->push($transitionEffect);
        
        return $fields;
    }
    
    /**
     * Returns the title of this widget.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Title() {
        return _t('SilvercartProductGroupItemsWidget.TITLE');
    }
    
    /**
     * Returns the title of this widget for display in the WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function CMSTitle() {
        return _t('SilvercartProductGroupItemsWidget.CMSTITLE');
    }
    
    /**
     * Returns the description of what this template does for display in the
     * WidgetArea GUI.
     * 
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Description() {
        return _t('SilvercartProductGroupItemsWidget.DESCRIPTION');
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
        if (!array_key_exists('useListView', $data)) {
            $this->useListView = 0;
        }
        if (!array_key_exists('Autoplay', $data)) {
            $this->autoplay = 0;
        }
        if (!array_key_exists('buildArrows', $data)) {
            $this->buildArrows = 0;
        }
        if (!array_key_exists('buildNavigation', $data)) {
            $this->buildNavigation = 0;
        }
        if (!array_key_exists('buildStartStop', $data)) {
            $this->buildStartStop = 0;
        }
        if (!array_key_exists('autoPlayDelayed', $data)) {
            $this->autoPlayDelayed = 0;
        }
        if (!array_key_exists('autoPlayLocked', $data)) {
            $this->autoPlayLocked = 0;
        }
        if (!array_key_exists('stopAtEnd', $data)) {
            $this->stopAtEnd = 0;
        }
        if (!array_key_exists('useSlider', $data)) {
            $this->useSlider = 0;
        }
        
        parent::populateFromPostData($data);
    }
}

/**
 * Provides a view of items of a definable productgroup.
 *
 * @package Silvercart
 * @subpackage Widgets
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 26.05.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @copyright 2011 pixeltricks GmbH
 */
class SilvercartProductGroupItemsWidget_Controller extends SilvercartWidget_Controller {

    protected $elements = null;
    
    /**
     * Register forms for the contained products.
     * 
     * @return void
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.07.2011
     */
    public function init() {
        if ($this->useSlider) {
            $this->elements = $this->ProductPages();
        } else {
            $this->elements = $this->Elements();
        }
        
        if ($this->elements) {
            $controller = Controller::curr();
            $elementIdx = 0;

            if ($this->useListView) {
                $productAddCartFormName = 'SilvercartProductAddCartFormList';
            } else {
                $productAddCartFormName = 'SilvercartProductAddCartFormTile';
            }

            if ($this->useSlider) {
                foreach ($this->elements as $productPage) {
                    foreach ($productPage as $elementHolder) {
                        foreach ($elementHolder->Elements as $element) {
                            $formIdentifier = 'ProductAddCartForm'.$this->ID.'_'.$elementIdx;
                            $productAddCartForm = new $productAddCartFormName(
                                $controller,
                                array('productID' => $element->ID)
                            );

                            $controller->registerCustomHtmlForm(
                                $formIdentifier,
                                $productAddCartForm
                            );

                            $element->productAddCartForm = $controller->InsertCustomHtmlForm(
                                $formIdentifier,
                                array(
                                    $element
                                )
                            );
                            $element->productAddCartFormObj = $productAddCartForm;
                            $elementIdx++;
                        }
                    }
                }
            } else {
                foreach ($this->elements as $element) {
                    $formIdentifier     = 'ProductAddCartForm'.$this->ID.'_'.$elementIdx;
                    $productAddCartForm = new $productAddCartFormName(
                        $controller,
                        array('productID' => $element->ID)
                    );

                    $controller->registerCustomHtmlForm(
                        $formIdentifier,
                        $productAddCartForm
                    );

                    $element->productAddCartForm = $controller->InsertCustomHtmlForm(
                        $formIdentifier,
                        array(
                            $element
                        )
                    );
                    $element->productAddCartFormObj = $productAddCartForm;
                    $elementIdx++;
                }
            }
        }
        
        if ($this->useSlider) {
            $autoplay           = 'false';
            $autoPlayDelayed    = 'false';
            $autoPlayLocked     = 'true';
            $stopAtEnd          = 'false';
            $buildArrows        = 'false';
            $buildStartStop     = 'false';
            $buildNavigation    = 'false';

            if ($this->Autoplay) {
                $autoplay = 'true';
            }
            if ($this->buildArrows) {
                $buildArrows = 'true';
            }
            if ($this->buildNavigation) {
                $buildNavigation = 'true';
            }
            if ($this->buildStartStop) {
                $buildStartStop = 'true';
            }
            if ($this->autoPlayDelayed) {
                $autoPlayDelayed = 'true';
            }
            if ($this->autoPlayLocked) {
                $autoPlayLocked = 'false';
            }
            if ($this->stopAtEnd) {
                $stopAtEnd = 'true';
            }

            switch ($this->transitionEffect) {
                case 'horizontalSlide':
                    $vertical           = 'false';
                    $animationTime      = 500;
                    $delayBeforeAnimate = 0;
                    $effect             = 'swing';
                    break;
                case 'verticalSlide':
                    $vertical           = 'true';
                    $animationTime      = 500;
                    $delayBeforeAnimate = 0;
                    $effect             = 'swing';
                    break;
                case 'fade':
                default:
                    $vertical           = 'false';
                    $animationTime      = 0;
                    $delayBeforeAnimate = 500;
                    $effect             = 'fade';
            }

            Requirements::css('silvercart/css/screen/sliders/theme-silvercart-default.css');
            Requirements::customScript(
                sprintf('
                    $(document).ready(function() {
                        $("#SilvercartProductGroupItemsWidgetSlider%d")
                        .anythingSlider({
                            autoPlay:           %s,
                            autoPlayDelayed:    %s,
                            autoPlayLocked:     %s,
                            stopAtEnd:          %s,
                            buildArrows:        %s,
                            buildNavigation:    %s,
                            buildStartStop:     %s,
                            delay:              %d,
                            animationTime:      %s,
                            delayBeforeAnimate: %d,
                            theme:              \'silvercart-default\',
                            vertical:           %s,
                            navigationFormatter: function(index, panel){
                                panel.css("display", "block");
                                return index;
                            }
                        })
                        .anythingSliderFx({
                            // base FX definitions
                            // ".selector" : [ "effect(s)", "size", "time", "easing" ]
                            // "size", "time" and "easing" are optional parameters, but must be kept in order if added
                            \'.panel\' : [ \'%s\', \'\', 500, \'easeInOutCirc\' ]
                        });
                    });
                    ',
                    $this->ID,
                    $autoplay,
                    $autoPlayDelayed,
                    $autoPlayLocked,
                    $stopAtEnd,
                    $buildArrows,
                    $buildNavigation,
                    $buildStartStop,
                    $this->slideDelay,
                    $animationTime,
                    $delayBeforeAnimate,
                    $vertical,
                    $effect
                )
            );
        }
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function ProductPages() {
        if (!$this->SilvercartProductGroupPageID) {
            return false;
        }
        
        if (!$this->elements) {
            if (!$this->numberOfProductsToFetch) {
                $this->numberOfProductsToFetch = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToFetch'];
            }
            if (!$this->numberOfProductsToShow) {
                $this->numberOfProductsToShow = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToShow'];
            }

            if ($this->numberOfProductsToFetch < $this->numberOfProductsToShow) {
                $this->numberOfProductsToFetch = $this->numberOfProductsToShow;
            }

            $productgroupPage = DataObject::get_by_id(
                'SilvercartProductGroupPage',
                $this->SilvercartProductGroupPageID
            );

            if (!$productgroupPage) {
                return false;
            }
            $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
            
            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $products = $productgroupPageSiteTree->getProducts($this->numberOfProductsToFetch, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC');
                    break;
                case 'sortOrderDesc':
                    $products = $productgroupPageSiteTree->getProducts($this->numberOfProductsToFetch, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END DESC');
                    break;
                case 'random':
                default:
                    $products = $productgroupPageSiteTree->getProducts($this->numberOfProductsToFetch, 'RAND()');
            } 

            $pages          = array();
            $pageProducts   = array();
            $pageNr         = 0;
            $PageProductIdx = 1;
            $isFirst        = true;

            if ($products) {
                foreach ($products as $product) {
                    $pageProducts[] = $product;
                    $PageProductIdx++;

                    if ($pageNr > 0) {
                        $isFirst = false;
                    }
                    if ($PageProductIdx > $this->numberOfProductsToShow) {
                        $pages['Page'.$pageNr] = array(
                            'Elements' => new DataObjectSet($pageProducts),
                            'IsFirst'    => $isFirst
                        );
                        $PageProductIdx = 1;
                        $pageProducts   = array();
                        $pageNr++;
                    }
                }
            }

            if (!array_key_exists('Page'.$pageNr, $pages) &&
                !empty($pageProducts)) {

                if ($pageNr > 0) {
                    $isFirst = false;
                }

                $pages['Page'.$pageNr] = array(
                    'Elements' => new DataObjectSet($pageProducts),
                    'IsFirst'  => $isFirst
                );
            }

            $this->elements = new DataObjectSet($pages);
        }
        
        return $this->elements;
    }
    
    /**
     * Returns a number of products from the chosen productgroup.
     * 
     * @return DataObjectSet
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 26.05.2011
     */
    public function Elements() {
        if (!$this->SilvercartProductGroupPageID) {
            return false;
        }
        
        if (!$this->elements) {
            if (!$this->numberOfProductsToShow) {
                $this->numberOfProductsToShow = SilvercartProductGroupItemsWidget::$defaults['numberOfProductsToShow'];
            }

            $productgroupPage = DataObject::get_by_id(
                'SilvercartProductGroupPage',
                $this->SilvercartProductGroupPageID
            );

            if (!$productgroupPage) {
                return false;
            }
            $productgroupPageSiteTree = ModelAsController::controller_for($productgroupPage);
            
            switch ($this->fetchMethod) {
                case 'sortOrderAsc':
                    $this->elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END ASC');
                    break;
                case 'sortOrderDesc':
                    $this->elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow, 'CASE WHEN SPGMSO.SortOrder THEN CONCAT(SPGMSO.SortOrder, SilvercartProduct.SortOrder) ELSE SilvercartProduct.SortOrder END DESC');
                    break;
                case 'random':
                default:
                    $this->elements = $productgroupPageSiteTree->getProducts($this->numberOfProductsToShow);
            } 
        }
        
        return $this->elements;
    }
    
    /**
     * Returns the title of the product group that items are shown.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 15.04.2011
     */
    public function ProductGroupTitle() {
        $title = '';
        
        if (!$this->SilvercartProductGroupPageID) {
            return $title;
        }
        
        $productgroupPage = DataObject::get_by_id(
            'SilvercartProductGroupPage',
            $this->SilvercartProductGroupPageID
        );
        
        if (!$productgroupPage) {
            return $title;
        }
        
        $title = $productgroupPage->MenuTitle;
        
        return $title;
    }
}