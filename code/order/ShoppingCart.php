<?php
/**
 * abstract for shopping cart
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class ShoppingCart extends DataObject {

    /**
     * Contains all registered modules that get called when the shoppingcart
     * is displayed.
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public static $registeredModules = array();

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "cart";

    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "carts";

    /**
     * 1:n relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_many = array(
        'positions' => 'ShoppingCartPosition'
    );

    /**
     * defines n:m relations
     *
     * @var array configure relations
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 16.12.10
     */
    public static $many_many = array(
        'articles' => 'Article'
    );

    /**
     * initialisation
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 25.01.2011
     */
    public function init() {
        parent::init();

        $this->callMethodOnRegisteredModules('performShoppingCartConditionsCheck', array($this, Member::currentUser()));
    }

    /**
     * adds an article to the cart
     *
     * @param array $formData the sended form data
     *
     * @return bool
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 21.12.2010
     */
    public static function addArticle($formData) {
        $member = Member::currentUser();
        if ($member == false) {
            $member = new AnonymousCustomer();
            $member->write();
            $member->logIn($remember = true);
        }

        if (!$member) {
            return false;
        }

        $cart = $member->getCart(); //This must return a cart for getCart() always returns a cart.

        if (!$cart) {
            return false;
        }

        if ($formData['articleID'] && $formData['articleAmount']) {
            $article = DataObject::get_by_id('Article', $formData['articleID'], 'Created');

            if ($article) {
                $quantity = (int) bcsqrt($formData['articleAmount'] * $formData['articleAmount'], 0); //make shure value is positiv
                $article->addToCart($cart->ID, $quantity);
            }
        }

        return true;
    }

    /**
     * empties cart
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public function delete() {
        $positions = $this->positions();

        foreach ($positions as $position) {
            $position->delete();
        }
    }

    /**
     * returns quantity of all articles in the cart
     *
     * @param int $articleId if set only article quantity of this article is returned
     *
     * @return int
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.10
     */
    public function getQuantity($articleId = null) {
        $positions = $this->positions();
        $quantity  = 0;

        foreach ($positions as $position) {
            if ($articleId === null ||
                $position->article()->ID === $articleId) {

                $quantity += $position->Quantity;
            }
        }

        return $quantity;
    }

    /**
     * Returns the taxable amount of positions in the shopping cart.
     *
     * Those are regular articles and can originate from registered modules too.
     * Contains no fees.
     *
     * @param array $excludeModules An array of registered modules that shall not
     *      be taken into account.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getTaxableAmountGrossWithoutFees($excludeModules = array()) {
        $amount             = 0;
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Member::currentUser()->shoppingCart(),
                Member::currentUser(),
                true
            ),
            $excludeModules
        );

        // Articles
        foreach ($this->positions() as $position) {
            $amount += (float) $position->article()->Price->getAmount() * $position->Quantity;
        }

        // Registered Modules
        foreach ($registeredModules as $moduleName => $modulePositions) {
            foreach ($modulePositions as $modulePosition) {
                $amount += (float) $modulePosition->PriceTotal;
            }
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);

        return $amountObj;
    }

    /**
     * Returns the non taxable amount of positions in the shopping cart.
     * Those can originate from registered modules only.
     *
     * @param array $excludeModules An array of registered modules that shall not
     *      be taken into account.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getNonTaxableAmount($excludeModules = array()) {
        $amount             = 0;
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Member::currentUser()->shoppingCart(),
                Member::currentUser(),
                false,
                $excludeModules
            )
        );

        // Registered Modules
        foreach ($registeredModules as $moduleName => $modulePositions) {
            foreach ($modulePositions as $modulePosition) {
                $amount += (float) $modulePosition->PriceTotal;
            }
        }

        $amountObj = new Money;
        $amountObj->setAmount($amount);

        return $amountObj;
    }

    /**
     * Returns the end sum of the shopping cart.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getAmountTotal() {
        $amount  = 0;
        $amount += $this->getTaxableAmountGrossWithoutFees()->getAmount();
        $amount += $this->getNonTaxableAmount()->getAmount();

        $amountObj = new Money;
        $amountObj->setAmount($amount);

        return $amountObj;
    }

    /**
     * returns tax included in $this
     *
     * @return Money money object
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.11.10
     */
    public function getTax() {
        $positions          = $this->positions();
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Member::currentUser()->shoppingCart(),
                Member::currentUser(),
                true
            )
        );
        $tax                = 0.0;

        // Articles
        foreach ($positions as $position) {
            $tax += $position->article()->getTaxAmount() * $position->Quantity;
        }

        // Registered Modules
        foreach ($registeredModules as $moduleName => $moduleOutput) {
            foreach ($moduleOutput as $modulePositions) {
                foreach ($modulePositions->moduleOutput as $modulePosition) {
                    $tax += (float) $modulePosition->TaxAmount;
                }
            }
        }
        
        $taxObj = new Money;
        $taxObj->setAmount($tax);

        return $taxObj;
    }

    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * without fee taxes.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 01.02.2011
     */
    public function getTaxRatesWithoutFees() {
        $positions          = $this->positions();
        $taxes              = new DataObjectSet;
        $registeredModules  = $this->callMethodOnRegisteredModules(
            'ShoppingCartPositions',
            array(
                Member::currentUser()->shoppingCart(),
                Member::currentUser(),
                true
            )
        );

        // Articles
        foreach ($positions as $position) {
            $taxRate = $position->article()->tax()->Rate;

            if (!$taxes->find('Rate', $taxRate)) {
                $taxes->push(
                    new DataObject(
                        array(
                            'Rate'      => $taxRate,
                            'AmountRaw' => 0.0,
                        )
                    )
                );
            }
            $taxSection = $taxes->find('Rate', $taxRate);
            $taxSection->AmountRaw += $position->article()->getTaxAmount() * $position->Quantity;
        }

        // Registered Modules
        foreach ($registeredModules as $moduleName => $moduleOutput) {
            foreach ($moduleOutput as $modulePositions) {
                foreach ($modulePositions->moduleOutput as $modulePosition) {
                    $taxRate = $modulePosition->TaxRate;
                    if (!$taxes->find('Rate', $taxRate)) {
                        $taxes->push(
                            new DataObject(
                                array(
                                    'Rate'      => $taxRate,
                                    'AmountRaw' => 0.0,
                                )
                            )
                        );
                    }
                    $taxSection = $taxes->find('Rate', $taxRate);
                    $taxSection->AmountRaw += $modulePosition->TaxAmount;
                }
            }
        }

        foreach ($taxes as $tax) {
            $taxObj = new Money;
            $taxObj->setAmount($tax->AmountRaw);
            
            $tax->Amount = $taxObj;
        }

        return $taxes;
    }

    /**
     * Returns tax amounts included in the shoppingcart separated by tax rates
     * with fee taxes.
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 04.02.2011
     */
    public function getTaxRatesWithFees() {
        return $this->getTaxRatesWithoutFees();
    }

    /**
     * calculate the carts total weight
     * needed to determin the ShippingFee
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 7.11.2010
     * @return integer|boolean the cart´s weight in gramm
     */
    public function getWeightTotal() {
        $positions = $this->positions();
        $totalWeight = (int) 0;
        if ($positions) {
            foreach ($positions as $position) {
                $totalWeight +=$position->article()->Weight * $position->Quantity;
            }
            return $totalWeight;
        } else {
            return false;
        }
    }

    /**
     * deletes all shopping cart positions when cart is deleted
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.10.2010
     * @return void
     */
    public function onBeforeDelete() {
        parent::onBeforeDelete();

        $filter                = sprintf("\"shoppingCartID\" = '%s'", $this->ID);
        $shoppingCartPositions = DataObject::get('ShoppingCartPosition', $filter);

        if ($shoppingCartPositions) {
            foreach ($shoppingCartPositions as $obj) {
                $obj->delete();
            }
        }
    }

    /**
     * Register a module.
     * Registered modules will be called when the shoppingcart is displayed.
     *
     * @param string $module The module class name
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public static function registerModule($module) {
        array_push(
            self::$registeredModules,
            $module
        );
    }

    /**
     * Returns all registered modules.
     *
     * Every module contains two keys for further iteration inside templates:
     *      - ShoppingCartPositions
     *      - ShoppingCartActions
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 21.01.2011
     */
    public function registeredModules() {
        $customer           = Member::currentUser();
        $modules            = array();
        $registeredModules  = self::$registeredModules;
        $hookMethods        = array(
            'NonTaxableShoppingCartPositions',
            'TaxableShoppingCartPositions',
            'ShoppingCartActions',
            'ShoppingCartTotal'
        );

        foreach ($registeredModules as $registeredModule) {
            $registeredModuleObjPlain   = new $registeredModule();

            if ($registeredModuleObjPlain->hasMethod('loadObjectForShoppingCart')) {
                $registeredModuleObj = $registeredModuleObjPlain->loadObjectForShoppingCart($this);
            }
            
            if (!$registeredModuleObj) {
                $registeredModuleObj = $registeredModuleObjPlain;
            }
            
            if ($registeredModuleObj) {
                foreach ($hookMethods as $hookMethod) {
                    if ($registeredModuleObj->hasMethod($hookMethod)) {
                        $modules[] = array(
                            $hookMethod => $registeredModuleObj->$hookMethod($this, $customer)
                        );
                    }
                }
            }
        }

        return new DataObjectSet($modules);
    }

    /**
     * Calls a method on all registered modules and returns its output.
     *
     * @param string $methodName     The name of the method to call
     * @param array  $parameters     Additional parameters for the method call
     * @param array  $excludeModules An array of registered modules that shall not
     *                               be taken into account.
     *
     * @return array Associative array:
     *      'ModuleName' => 'ModuleOutput'
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function callMethodOnRegisteredModules($methodName, $parameters = array(), $excludeModules = array()) {
        $registeredModules  = self::$registeredModules;
        $outputOfModules    = array();

        if (!is_array($excludeModules)) {
            $excludeModules = array($excludeModules);
        }

        foreach ($registeredModules as $registeredModule) {

            // Skip excluded modules
            if (in_array($registeredModule, $excludeModules)) {
                continue;
            }

            $registeredModuleObjPlain = new $registeredModule();

            if ($registeredModuleObjPlain->hasMethod('loadObjectForShoppingCart')) {
                $registeredModuleObj = $registeredModuleObjPlain->loadObjectForShoppingCart($this);
            } else {
                $registeredModuleObj = $registeredModuleObjPlain;
            }

            if ($registeredModuleObj) {
                if ($registeredModuleObj->hasMethod($methodName)) {

                    if (!is_array($parameters)) {
                        $parameters = array($parameters);
                    }

                    $outputOfModules[$registeredModule] = call_user_func_array(
                        array(
                            $registeredModuleObj,
                            $methodName
                        ),
                        $parameters
                    );
                }
            }
        }

        return $outputOfModules;
    }
}
