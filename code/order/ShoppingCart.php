<?php
/**
 * ShoppingCart Objekt
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
    public static $singular_name = "Warenkorb";

    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "Warenkörbe";

    /**
     * 1:n Beziehungen
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
     * Initialisation
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
     * Legt einen Artikel in den Warenkorb.
     *
     * @param array $formData Die gesendeten Formulardaten
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
     * Loescht alle Positionen aus dem Warenkorb.
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
     * Gibt die Menge aller Artikel im Warenkorb zurueck.
     *
     * @param int $articleId Wenn angegeben, wird nur die Menge der Artikel
     *                       geliefert, deren ID der $articleId entspricht
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
     * Gibt die Summe aller Artikel im Warenkorb zurueck.
     *
     * @param bool  $withModules    Calculate the total sum with modules activated
     * @param array $excluceModules Calculate the total sum with some modules deactivated
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 22.11.10
     */
    public function getPrice($withModules = true, $excludeModules = array()) {
        $positions = $this->positions();
        $price     = 0.0;

        foreach ($positions as $position) {
            $price += (float) $position->article()->Price->getAmount() * $position->Quantity;
        }

        if ($withModules) {
            $modulesOutput = $this->callMethodOnRegisteredModules('ShoppingCartTotal');

            foreach ($modulesOutput as $moduleName => $moduleOutput) {
                if (!in_array($moduleName, $excludeModules)) {
                    $price += (float) $moduleOutput->getAmount();
                }
            }

            if ($price < 0) {
                $price = 0;
            }
        }

        $priceObj = new Money;
        $priceObj->setAmount($price);

        return $priceObj;
    }

    /**
     * Gibt die Summe aller Mehrwertsteuern im Warenkorb zurueck.
     *
     * @return Money
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 24.11.10
     */
    public function getTax() {
        $positions = $this->positions();
        $tax       = 0.0;

        foreach ($positions as $position) {
            $tax += $position->article()->getTaxAmount() * $position->Quantity;
        }
        
        $taxObj = new Money;
        $taxObj->setAmount($tax);

        return $taxObj;
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
     * Alle ShoppingCarPositions entfernen, wenn der Warenkorb selbst
     * geloescht wird.
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
        $modules            = new DataObjectSet();
        $registeredModules  = self::$registeredModules;
        $hookMethods        = array(
            'ShoppingCartPositions',
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
                        $modules->push(
                            new ArrayData(
                                array(
                                    $hookMethod => $registeredModuleObj->$hookMethod($this, $customer)
                                )
                            )
                        );
                    }
                }
            }
        }

        return $modules;
    }

    /**
     * Calls a method on all registered modules and returns its output.
     *
     * @param string $methodName The name of the method to call
     * @param array  $parameters Additional parameters for the method call
     *
     * @return array Associative array:
     *      'ModuleName' => 'ModuleOutput'
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 24.01.2011
     */
    public function callMethodOnRegisteredModules($methodName, $parameters = array()) {
        $registeredModules  = self::$registeredModules;
        $outputOfModules    = array();

        foreach ($registeredModules as $registeredModule) {
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

                    //$outputOfModules[$registeredModule] = $registeredModuleObj->$methodName($this, eval($parameterList));
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
