<?php

namespace SilverCart\Model\Pages;

use SilverCart\Forms\ContactForm;
use SilverCart\Model\Pages\MetaNavigationHolderController;
use SilverCart\Model\Product\Product;
use SilverStripe\Control\HTTPRequest;

/**
 * ContactFormPage Controller class.
 *
 * @package SilverCart
 * @subpackage Model_Pages
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 27.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ContactFormPageController extends MetaNavigationHolderController {
    
    /**
     * List of allowed actions
     * 
     * @var array
     */
    private static $allowed_actions = array(
        'ContactForm',
        'productQuestion',
        'thanks',
    );
    
    /**
     * Returns the ContactForm.
     * 
     * @return ContactForm
     */
    public function ContactForm() {
        $form = new ContactForm($this);
        $action = $this->getRequest()->param('Action');
        if ($action == 'productQuestion') {
            $this->addProductQuestion($form);
        }
        return $form;
    }
    
    /**
     * Fills the contact form with a predefined product question text.
     *
     * @param ContactForm $contactForm ContactForm
     * 
     * @return string
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 13.11.2017
     */
    public function addProductQuestion(ContactForm $contactForm) {
        $urlID = $this->getRequest()->param('ID');
        if (!empty($urlID) &&
            is_numeric($urlID)) {
            $product = Product::get()->byID($urlID);
            if ($product instanceof Product &&
                $product->exists()) {
                $contactForm->Fields()->dataFieldByName('Message')->setValue(
                        _t(Product::class . '.PRODUCT_QUESTION',
                            'Please answer the following questions for the product {title} ({productnumber}):',
                            [
                                'title' => $product->Title,
                                'productnumber' => $product->ProductNumberShop,
                            ]
                        )
                );
            }
        }
    }
}
