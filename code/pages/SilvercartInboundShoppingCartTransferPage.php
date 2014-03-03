<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * @package Silvercart
 * @subpackage Pages
 */

/**
 * Handles the transfer of shopping cart items from an external referer to
 * a current users shopping cart.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @since 01.08.2011
 * @license see license file in modules root directory
 * @copyright 2013 pixeltricks GmbH
 */
class SilvercartInboundShoppingCartTransferPage extends Page {
    
    /**
     * Returns the translated singular name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string The objects singular name 
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function singular_name() {
        return SilvercartTools::singular_name_for($this);
    }


    /**
     * Returns the translated plural name of the object. If no translation exists
     * the class name will be returned.
     * 
     * @return string the objects plural name
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 13.07.2012
     */
    public function plural_name() {
        return SilvercartTools::plural_name_for($this); 
    }
}

/**
 * Handles the transfer of shopping cart items from an external referer to
 * a current users shopping cart.
 *
 * @package Silvercart
 * @subpackage Pages
 * @author Sascha Koehler <skoehler@pixeltricks.de>,
 *         Sebastian Diel <sdiel@pixeltricks.de>
 * @since 08.04.2013
 * @copyright 2013 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class SilvercartInboundShoppingCartTransferPage_Controller extends Page_Controller {
    
    /**
     * Contains all error messages.
     *
     * @var array
     */
    protected $errorMessages = array();
    
    /**
     * We implement our own action handling here since we use the action
     * as identifier string to look up the corresponding 
     * SilvercartInboundShoppingCartTransfer object.
     *
     * @param SS_HTTPRequest $request The request parameters
     * @param string         $action  Action
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     *         Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.04.2013
     */
    public function handleAction($request, $action) {
        $this->action           = str_replace("-","_",$request->param('Action'));
        $this->requestParams    = $request->requestVars();
        
        $inboundShoppingCartTransfer = DataObject::get_one(
            'SilvercartInboundShoppingCartTransfer',
            sprintf(
                "refererIdentifier = '%s'",
                Convert::raw2sql($this->action)
            )
        );
        
        if ($inboundShoppingCartTransfer) {
            if ($inboundShoppingCartTransfer->useSharedSecret &&
                !$this->checkSharedSecretFor($inboundShoppingCartTransfer, $request)) {
                
                return $this->sharedSecretInvalid();
            } else {
                switch ($inboundShoppingCartTransfer->transferMethod) {
                    case 'keyValue':
                        return $this->handleKeyValueShoppingCartTransferWith($inboundShoppingCartTransfer, $request);
                        break;
                    case 'combinedString':
                    default:
                        return $this->handleCombinedStringShoppingCartTransferWith($inboundShoppingCartTransfer, $request);
                }
            }
            
        } else {
            return $this->refererNotFound();
        }
    }
    
    /**
     * Returns the error messages.
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    public function ErrorMessages() {
        return new DataList($this->errorMessages);
    }
    
    /**
     * Handles the transfer of the sent product data to a valid shopping cart
     * via key-value pairs.
     *
     * @param SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer The transfer object that handles this referer
     * @param SS_HTTPRequest                        $request                     The request parameters
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function handleKeyValueShoppingCartTransferWith(SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer, SS_HTTPRequest $request) {
        $error          = false;
        $requestVars    = $request->requestVars();
        $identifierIdx  = 0;
        
        if (!array_key_exists($inboundShoppingCartTransfer->keyValueProductIdentifier, $requestVars)) {
            return $this->keyValueProductIdentifierNotFound();
        }
        if (!array_key_exists($inboundShoppingCartTransfer->keyValueQuantityIdentifier, $requestVars)) {
            return $this->keyValueQuantityIdentifierNotFound();
        }
        
        $identifierCount = count($requestVars[$inboundShoppingCartTransfer->keyValueProductIdentifier]);
        
        for ($identifierIdx = 0; $identifierIdx < $identifierCount; $identifierIdx++) {
            if (array_key_exists($identifierIdx, $requestVars[$inboundShoppingCartTransfer->keyValueQuantityIdentifier])) {
                $productQuantity = $requestVars[$inboundShoppingCartTransfer->keyValueQuantityIdentifier][$identifierIdx];
            } else {
                $productQuantity = 1;
            }
            
            $product = DataObject::get_one(
                'SilvercartProduct',
                sprintf(
                    $inboundShoppingCartTransfer->productMatchingField." = '%s'",
                    Convert::raw2sql($requestVars[$inboundShoppingCartTransfer->keyValueProductIdentifier][$identifierIdx])
                )
            );
            
            if ($product) {
                $this->addProduct($product, $productQuantity);
            }
        }
        
        if (!$error) {
            $this->redirect(SilvercartPage_controller::PageByIdentifierCodeLink('SilvercartCartPage'));
        }
    }
    
    /**
     * Handles the transfer of the sent product data to a valid shopping cart
     * via one string with separators.
     *
     * @param SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer The transfer object that handles this referer
     * @param SS_HTTPRequest                        $request                     The request parameters
     *
     * @return string
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function handleCombinedStringShoppingCartTransferWith(SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer, SS_HTTPRequest $request) {
        $error       = false;
        $requestVars = $request->requestVars();
        
        if (!array_key_exists($inboundShoppingCartTransfer->combinedStringKey, $requestVars)) {
            $action                 = $this->urlParams['ID'];
            $actionElements         = explode('&', $action);
            $validCombinedKeyFound  = false;
            
            foreach ($actionElements as $actionElement) {
                if (strpos($actionElement, '=') === false) {
                    continue;
                }
                
                list($combinedStringKey, $combinedStringEntities) = explode('=', $actionElement);
                
                if ($combinedStringKey == $inboundShoppingCartTransfer->combinedStringKey) {
                    $validCombinedKeyFound = true;
                    
                    $combinedString = Convert::raw2sql($combinedStringKey);
                    $entities       = explode($inboundShoppingCartTransfer->combinedStringEntitySeparator, $combinedStringEntities);
                }
            }
            
            if (!$validCombinedKeyFound) {
                return $this->combinedStringKeyNotFound();
            }
        } else {
            $combinedString = Convert::raw2sql($requestVars[$inboundShoppingCartTransfer->combinedStringKey]);
            $entities       = explode($inboundShoppingCartTransfer->combinedStringEntitySeparator, $combinedString);
        }
        
        if (is_array($entities)) {
            foreach ($entities as $entity) {
                if (empty($entity)) {
                    continue;
                }
                
                list($productIdentifier, $productQuantity) = explode($inboundShoppingCartTransfer->combinedStringQuantitySeparator, $entity);

                $product = DataObject::get_one(
                    'SilvercartProduct',
                    sprintf(
                        $inboundShoppingCartTransfer->productMatchingField." = '%s'",
                        $productIdentifier
                    )
                );

                if ($product) {
                    $this->addProduct($product, $productQuantity);
                }
            }
        }
        
        if (!$error) {
            $this->redirect(SilvercartPage_controller::PageByIdentifierCodeLink('SilvercartCartPage'));
        }
    }

    /**
     * Add a product to the shopping cart.
     *
     * @param SilvercartProduct $product         The product object to add to the shopping cart
     * @param int               $productQuantity The quantity of the product to add
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function addProduct(SilvercartProduct $product, $productQuantity) {
        $productAdded = false;
        
        if ($product->isActive &&
            $product->SilvercartProductGroupID > 0 &&
            $product->isBuyableDueToStockManagementSettings()) {

            $productData = array(
                'productID'         => $product->ID,
                'productQuantity'   => $productQuantity
            );
            $productAdded = SilvercartShoppingCart::addProduct($productData);
        }
        
        return $productAdded;
    }
    
    /**
     * Check if a shared secret was sent and is valid for this transfer type.
     *
     * @param SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer The transfer object that handles this referer
     * @param SS_HTTPRequest                        $request                     The request parameters
     *
     * @return boolean
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function checkSharedSecretFor(SilvercartInboundShoppingCartTransfer $inboundShoppingCartTransfer, SS_HTTPRequest $request) {
        $isValid        = false;
        $requestVars    = $request->requestVars();
        
        if (array_key_exists($inboundShoppingCartTransfer->sharedSecretIdentifier, $requestVars) &&
            sha1($inboundShoppingCartTransfer->sharedSecret) === urldecode($requestVars[$inboundShoppingCartTransfer->sharedSecretIdentifier])) {
            
            $isValid = true;
        }
        
        return $isValid;
    }
    
    /**
     * Displays an error output since the referer could not be found.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function refererNotFound() {
        $this->errorMessages[] = array(
            'Error' => _t('SilvercartInboundShoppingCartTransferPage.ERROR_REFERER_NOT_FOUND')
        );
        
        return $this;
    }
    
    /**
     * Displays an error output since the key-value product identifier  is
     * missing.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function keyValueProductIdentifierNotFound() {
        $this->errorMessages[] = array(
            'Error' => _t('SilvercartInboundShoppingCartTransferPage.ERROR_KEY_VALUE_PRODUCT_IDENTIFIER_NOT_FOUND')
        );
        
        return $this;
    }
    
    /**
     * Displays an error output since the key-value quantity identifier  is
     * missing.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function keyValueQuantityIdentifierNotFound() {
        $this->errorMessages[] = array(
            'Error' => _t('SilvercartInboundShoppingCartTransferPage.ERROR_KEY_VALUE_QUANTITY_IDENTIFIER_NOT_FOUND')
        );
        
        return $this;
    }
    
    /**
     * Displays an error output since the combined string key is missing.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function combinedStringKeyNotFound() {
        $this->errorMessages[] = array(
            'Error' => _t('SilvercartInboundShoppingCartTransferPage.ERROR_COMBINED_STRING_KEY_NOT_FOUND')
        );
        
        return $this;
    }
    
    /**
     * Displays an error output since the sent shared secret is invalid.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 01.08.2011
     */
    protected function sharedSecretInvalid() {
        $this->errorMessages[] = array(
            'Error' => _t('SilvercartInboundShoppingCartTransferPage.ERROR_SHARED_SECRET_INVALID')
        );
        
        return $this;
    }
}