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
        'article' => 'Article',
        'shoppingCart' => 'ShoppingCart'
    );

    /**
     * price sum of this position
     *
     * @return Money the price sum
     * 
     * @author Sascha Koehler <skoehler@pixeltricks.de>
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
     * Increment the article quantity of a shopping cart position.
     * This form is parsed dynamically in the CartPage
     *
     * @return Form for incrementing the positions quantity
     * @since 20.10.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function incrementAmountForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID', $this->ID));
        $actions = new FieldSet();
        $actions->push(new FormAction('doIncrementAmount', '+'));
        $form = new Form(Controller::curr(), 'incrementAmountForm', $fields, $actions);

        return $form;
    }

    /**
     * decrement the article quantity of a shopping cart position.
     * This form is parsed dynamically in the CartPage
     *
     * @return Form for decrementing the positions quantity
     * @since 20.10.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function decrementAmountForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID', $this->ID));
        $actions = new FieldSet();
        $actions->push(new FormAction('doDecrementAmount', '-'));
        $form = new Form(Controller::curr(), 'decrementAmountForm', $fields, $actions);
        return $form;
    }

    /**
     * remove position
     * This form is parsed dynamically in the CartPage
     *
     * @return Form for removing the position
     * @since 20.10.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function removeFromCartForm() {
        $fields = new FieldSet();
        $fields->push(new HiddenField('ShoppingCartPositionID', 'ShoppingCartPositionID', $this->ID));
        $actions = new FieldSet();
        $actions->push(new FormAction('doRemoveFromCart', 'entfernen'));
        $form = new Form(Controller::curr(), 'removeFromCartForm', $fields, $actions);
        return $form;
    }

}
