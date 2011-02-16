<?php

/**
 * abstract for shopping cart positions
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 22.11.2010
 * @license none
 */
class ShoppingCartPosition extends DataObject {

    /**
     * Singular-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $singular_name = "cart position";

    /**
     * Plural-Beschreibung zur Darstellung im Backend.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $plural_name = "cart positions";

    /**
     * attributes
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $db = array(
        'Quantity' => 'Int'
    );

    /**
     * n:m relations
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 22.11.2010
     */
    public static $has_one = array(
        'article'       => 'Article',
        'shoppingCart'  => 'ShoppingCart'
    );

    /**
     * Registers the edit-forms for this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function __construct($record = null, $isSingleton = false) {
        parent::__construct($record, $isSingleton);

        $controller = Controller::curr();

        if ($controller->hasMethod('getRegisteredCustomHtmlForm')) {
            $positionForms  = array(
                'IncrementPositionQuantityForm',
                'DecrementPositionQuantityForm',
                'RemovePositionForm'
            );

            foreach ($positionForms as $positionForm) {
                if (!$controller->getRegisteredCustomHtmlForm($positionForm.$this->ID)) {
                    $controller->registerCustomHtmlForm(
                        $positionForm.$this->ID,
                        new $positionForm(
                            $controller,
                            array(
                                'positionID' => $this->ID
                            )
                        )
                    );
                }
            }
        }
    }

    /**
     * price sum of this position
     *
     * @return Money the price sum
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 22.10.2010
     */
    public function getPrice() {
        $article = $this->article();
        $price   = 0;

        if ($article && $article->Price->getAmount()) {
            $price = $article->Price->getAmount() * $this->Quantity;
        }
        
        $priceObj = new Money();
        $priceObj->setAmount($price);
        
        return $priceObj;
    }

    /**
     * Returns the form for incrementing the amount of this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getIncrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('IncrementPositionQuantityForm'.$this->ID);
    }

    /**
     * Returns the form for decrementing the amount of this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getDecrementPositionQuantityForm() {
        return Controller::curr()->InsertCustomHtmlForm('DecrementPositionQuantityForm'.$this->ID);
    }

    /**
     * Returns the form for removing this position.
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 11.02.2011
     */
    public function getRemovePositionForm() {
        return Controller::curr()->InsertCustomHtmlForm('RemovePositionForm'.$this->ID);
    }
}
