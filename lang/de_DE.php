<?php

/**
 * German (Germany) language pack
 * @package modules: silvercart
 */
i18n::include_locale_file('silvercart', 'en_US');

global $lang;

if (array_key_exists('de_DE', $lang) && is_array($lang['de_DE'])) {
    $lang['de_DE'] = array_merge($lang['en_US'], $lang['de_DE']);
} else {
    $lang['de_DE'] = $lang['en_US'];
}

$lang['de_DE']['Address']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Address']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['AddressHolder']['EDIT'] = 'edit';
$lang['de_DE']['AddressHolder']['INVOICEADDRESS'] = 'invoice address';
$lang['de_DE']['AddressHolder']['PLURALNAME'] = array(
    'Address holders',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AddressHolder']['SHIPPINGADDRESS'] = 'shipping address';
$lang['de_DE']['AddressHolder']['SINGULARNAME'] = array(
    'address holder',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['AddressHolder']['TITLE'] = 'address overview';
$lang['de_DE']['AddressHolder']['URL_SEGMENT'] = 'address-overview';
$lang['de_DE']['AddressPage']['PLURALNAME'] = array(
    'Addressdetailsseits',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AddressPage']['SINGULARNAME'] = array(
    'Addressdetailsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['AddressPage']['TITLE'] = 'address details';
$lang['de_DE']['AddressPage']['URL_SEGMENT'] = 'address-details';
$lang['de_DE']['AnonymousCustomer']['ANONYMOUSCUSTOMER'] = 'anonymous customer';
$lang['de_DE']['AnonymousCustomer']['PLURALNAME'] = array(
    'Anonymous Customers',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['AnonymousCustomer']['SINGULARNAME'] = array(
    'Anonymous Customer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Article']['ADD_TO_CART'] = 'add to cart';
$lang['de_DE']['Article']['CHOOSE_MASTER'] = '-- choose master --';
$lang['de_DE']['Article']['DESCRIPTION'] = 'article description';
$lang['de_DE']['Article']['MASTERARTICLE'] = 'master article';
$lang['de_DE']['Article']['PLURALNAME'] = array(
    'Artikel',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Article']['PRICE'] = 'price';
$lang['de_DE']['Article']['PRICE_SINGLE'] = 'price single';
$lang['de_DE']['Article']['QUANTITY'] = 'quantity';
$lang['de_DE']['Article']['SINGULARNAME'] = array(
    'Artikel',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Article']['TITLE'] = 'article';
$lang['de_DE']['Article']['VAT'] = 'VAT';
$lang['de_DE']['Article']['WEIGHT'] = 'weight';
$lang['de_DE']['ArticleCategoryHolder']['PLURALNAME'] = array(
    'Article category holders',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleCategoryHolder']['SINGULARNAME'] = array(
    'article category holder',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleCategoryHolder']['TITLE'] = 'category overview';
$lang['de_DE']['ArticleCategoryHolder']['URL_SEGMENT'] = 'categoryoverview';
$lang['de_DE']['ArticleCategoryPage']['ARTICLES'] = 'articles';
$lang['de_DE']['ArticleCategoryPage']['CATEGORY_PICTURE'] = 'category picture';
$lang['de_DE']['ArticleCategoryPage']['COLUMN_TITLE'] = 'title';
$lang['de_DE']['ArticleCategoryPage']['COLUM_TITLE'] = 'title';
$lang['de_DE']['ArticleCategoryPage']['PLURALNAME'] = array(
    'Article categoriessss',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleCategoryPage']['SINGULARNAME'] = array(
    'Article categoriesss',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['PLURALNAME'] = array(
    'article groups',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['SINGULARNAME'] = array(
    'article group overview',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleGroupHolder']['URL_SEGMENT'] = 'articlegroups';
$lang['de_DE']['ArticleGroupPage']['ATTRIBUTES'] = 'attributes';
$lang['de_DE']['ArticleGroupPage']['GROUP_PICTURE'] = 'group picture';
$lang['de_DE']['ArticleGroupPage']['PLURALNAME'] = array(
    'article groups',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleGroupPage']['SINGULARNAME'] = array(
    'article group',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticleImageGallery']['PLURALNAME'] = array(
    'galleries',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleImageGallery']['SINGULARNAME'] = array(
    'gallery',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticlePage']['ADD_TO_CART'] = 'add to cart';
$lang['de_DE']['ArticlePage']['PLURALNAME'] = array(
    'Article detailss',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticlePage']['QUANTITY'] = 'quantity';
$lang['de_DE']['ArticlePage']['SINGULARNAME'] = array(
    'article details',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ArticlePage']['URL_SEGMENT'] = 'articledetails';
$lang['de_DE']['ArticleTexts']['PLURALNAME'] = array(
    'Artikelübersetzungstexte',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ArticleTexts']['SINGULARNAME'] = array(
    'Artikelübersetzungstext',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Attribute']['PLURALNAME'] = array(
    'Attributs',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Attribute']['SINGULARNAME'] = array(
    'Attribute',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['BusinessCustomer']['BUSINESSCUSTOMER'] = 'business customer';
$lang['de_DE']['BusinessCustomer']['PLURALNAME'] = array(
    'Business Customers',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['BusinessCustomer']['SINGULARNAME'] = array(
    'Business Customer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Carrier']['PLURALNAME'] = array(
    'Frachtführer',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Carrier']['SINGULARNAME'] = array(
    'Frachtführer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CartPage']['PLURALNAME'] = array(
    'Carts',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CartPage']['SINGULARNAME'] = array(
    'cart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CartPage']['URL_SEGMENT'] = 'cart';
$lang['de_DE']['CheckoutFormStep1']['EMPTYSTRING_COUNTRY'] = '--country--';
$lang['de_DE']['CheckoutFormStep2']['EMPTYSTRING_PAYMENTMETHOD'] = '--choose payment method--';
$lang['de_DE']['CheckoutFormStep3']['EMPTYSTRING_SHIPPINGMETHOD'] = '--choose shipping method--';
$lang['de_DE']['CheckoutStep']['PLURALNAME'] = array(
    'Checkout Steps',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CheckoutStep']['SINGULARNAME'] = array(
    'Checkout Step',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CheckoutStep']['URL_SEGMENT'] = 'checkout';
$lang['de_DE']['ContactFormPage']['PLURALNAME'] = array(
    'Contact form pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ContactFormPage']['SINGULARNAME'] = array(
    'contact form page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ContactFormPage']['TITLE'] = 'contact';
$lang['de_DE']['ContactFormPage']['URL_SEGMENT'] = 'contact';
$lang['de_DE']['ContactFormResponsePage']['CONTACT_CONFIRMATION'] = 'contact confirmation';
$lang['de_DE']['ContactFormResponsePage']['PLURALNAME'] = array(
    'Contact form response pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ContactFormResponsePage']['SINGULARNAME'] = array(
    'contact form response page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ContactFormResponsePage']['URL_SEGMENT'] = 'contactconfirmation';
$lang['de_DE']['Country']['ATTRIBUTED_PAYMENTMETHOD'] = 'attributed payment method';
$lang['de_DE']['Country']['ATTRIBUTED_ZONES'] = 'zugeordnete Zone';
$lang['de_DE']['Country']['PLURALNAME'] = array(
    'countries',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Country']['SINGULARNAME'] = array(
    'country',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['CustomerCategory']['PLURALNAME'] = array(
    'Kundengruppen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['CustomerCategory']['SINGULARNAME'] = array(
    'Kundengruppe',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPage']['PLURALNAME'] = array(
    'My datas',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['DataPage']['SINGULARNAME'] = array(
    'my data',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPage']['TITLE'] = 'my data';
$lang['de_DE']['DataPage']['URL_SEGMENT'] = 'my-data';
$lang['de_DE']['DataPrivacyStatementPage']['PLURALNAME'] = array(
    'Privacy policy pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['DataPrivacyStatementPage']['SINGULARNAME'] = array(
    'privacy policy page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['DataPrivacyStatementPage']['TITLE'] = 'data privacy statement';
$lang['de_DE']['DataPrivacyStatementPage']['URL_SEGMENT'] = 'data-privacy-statement';
$lang['de_DE']['EditAddressForm']['EMPTYSTRING_PLEASECHOOSE'] = '--please choose--';
$lang['de_DE']['EmailTemplates']['PLURALNAME'] = array(
    'Emailvorlagen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['EmailTemplates']['SINGULARNAME'] = array(
    'Emailvorlage',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['PLURALNAME'] = array(
    'Footer navigations',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['SINGULARNAME'] = array(
    'footer navigation',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['FooterNavigationHolder']['URL_SEGMENT'] = 'footernavigation';
$lang['de_DE']['FrontPage']['DEFAULT_CONTENT'] = '<h2>Welcome to <strong>SilverCart</strong> Webshop!</h2>';
$lang['de_DE']['FrontPage']['PLURALNAME'] = array(
    'Front pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['FrontPage']['SINGULARNAME'] = array(
    'front page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['HandlingCost']['PLURALNAME'] = array(
    'Handling Costs',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['HandlingCost']['SINGULARNAME'] = array(
    'Handling Cost',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['InvoiceAddress']['PLURALNAME'] = array(
    'Rechnungsadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['InvoiceAddress']['SINGULARNAME'] = array(
    'Rechnungsadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Manufacturer']['PLURALNAME'] = array(
    'Hersteller',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Manufacturer']['SINGULARNAME'] = array(
    'Hersteller',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['PLURALNAME'] = array(
    'Meta navigations',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['SINGULARNAME'] = array(
    'meta navigation',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MetaNavigationHolder']['URL_SEGMENT'] = 'metanavigation';
$lang['de_DE']['MyAccountHolder']['PLURALNAME'] = array(
    'Account pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['MyAccountHolder']['SINGULARNAME'] = array(
    'account page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['MyAccountHolder']['TITLE'] = 'my account';
$lang['de_DE']['MyAccountHolder']['URL_SEGMENT'] = 'my account';
$lang['de_DE']['Order']['PLURALNAME'] = array(
    'Bestellungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Order']['SINGULARNAME'] = array(
    'Bestellung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderAddress']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderAddress']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['PLURALNAME'] = array(
    'Order conirmation pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['SINGULARNAME'] = array(
    'order conirmation page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderConfirmationPage']['URL_SEGMENT'] = 'order-conirmation';
$lang['de_DE']['OrderDetailPage']['PLURALNAME'] = array(
    'Order detailss',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderDetailPage']['SINGULARNAME'] = array(
    'order details',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderDetailPage']['TITLE'] = 'order details';
$lang['de_DE']['OrderDetailPage']['URL_SEGMENT'] = 'order-details';
$lang['de_DE']['OrderHolder']['PLURALNAME'] = array(
    'My oderss',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderHolder']['SINGULARNAME'] = array(
    'my oders',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderHolder']['TITLE'] = 'my oders';
$lang['de_DE']['OrderHolder']['URL_SEGMENT'] = 'my-oders';
$lang['de_DE']['OrderInvoiceAddress']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderInvoiceAddress']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderPosition']['PLURALNAME'] = array(
    'Order Positions',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderPosition']['SINGULARNAME'] = array(
    'Order Position',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderShippingAddress']['PLURALNAME'] = array(
    'Adressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderShippingAddress']['SINGULARNAME'] = array(
    'Adresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderStatus']['PLURALNAME'] = array(
    'Bestellstatus',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderStatus']['SINGULARNAME'] = array(
    'Bestellstatus',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['OrderStatusTexts']['PLURALNAME'] = array(
    'Order Status Textss',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['OrderStatusTexts']['SINGULARNAME'] = array(
    'Order Status Texts',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Page']['ABOUT_US'] = 'about us';
$lang['de_DE']['Page']['ABOUT_US_URL_SEGMENT'] = 'about-us';
$lang['de_DE']['Page']['ACCESS_CREDENTIALS_CALL'] = 'Please fill in Your access credentials:';
$lang['de_DE']['Page']['ADDRESS'] = 'address';
$lang['de_DE']['Page']['ADDRESSINFORMATION'] = 'address information';
$lang['de_DE']['Page']['ADDRESS_DATA'] = 'address data';
$lang['de_DE']['Page']['ALREADY_REGISTERED'] = 'Hello %s, You have already registered.';
$lang['de_DE']['Page']['API_CREATE'] = 'can create objects via the API';
$lang['de_DE']['Page']['API_DELETE'] = 'can delete objects via the API';
$lang['de_DE']['Page']['API_EDIT'] = 'can edit objects via the API';
$lang['de_DE']['Page']['API_VIEW'] = 'can read objects via the API';
$lang['de_DE']['Page']['ARTICLENAME'] = 'article name';
$lang['de_DE']['Page']['BILLING_ADDRESS'] = 'billing address';
$lang['de_DE']['Page']['BIRTHDAY'] = 'birthday';
$lang['de_DE']['Page']['CANCEL'] = 'cancel';
$lang['de_DE']['Page']['CART'] = 'cart';
$lang['de_DE']['Page']['CATALOG'] = 'catalog';
$lang['de_DE']['Page']['CHANGE_PAYMENTMETHOD_CALL'] = 'Please choose another payment method or contact the shop owner.';
$lang['de_DE']['Page']['CHANGE_PAYMENTMETHOD_LINK'] = 'choose another payment method';
$lang['de_DE']['Page']['CHECKOUT'] = 'checkout';
$lang['de_DE']['Page']['CHECK_FIELDS_CALL'] = 'Please check Your input on the following fields:';
$lang['de_DE']['Page']['CONTACT_FORM'] = 'contact form';
$lang['de_DE']['Page']['DETAILS'] = 'details';
$lang['de_DE']['Page']['DIDNOT_RETURN_RESULTS'] = 'did not return any results in our shop.';
$lang['de_DE']['Page']['EMAIL_ADDRESS'] = 'email address';
$lang['de_DE']['Page']['EMPTY_CART'] = 'empty';
$lang['de_DE']['Page']['ERROR_LISTING'] = 'The following errors have occured:';
$lang['de_DE']['Page']['ERROR_OCCURED'] = 'An error has occured.';
$lang['de_DE']['Page']['FIND'] = 'find:';
$lang['de_DE']['Page']['GOTO'] = 'go to %s page';
$lang['de_DE']['Page']['GOTO_CART'] = 'go to cart';
$lang['de_DE']['Page']['GOTO_CONTACT_LINK'] = 'go to contact page';
$lang['de_DE']['Page']['HEADERPICTURE'] = 'header picture';
$lang['de_DE']['Page']['INCLUDED_VAT'] = 'included VAT';
$lang['de_DE']['Page']['I_ACCEPT'] = 'I accept the';
$lang['de_DE']['Page']['I_HAVE_READ'] = 'I have read the';
$lang['de_DE']['Page']['MYACCOUNT'] = 'my account';
$lang['de_DE']['Page']['NEWSLETTER'] = 'newsletter';
$lang['de_DE']['Page']['NEXT'] = 'next';
$lang['de_DE']['Page']['NO_ORDERS'] = 'You do not have any orders yet';
$lang['de_DE']['Page']['NO_RESULTS'] = 'Sorry, but Your query did not return any results.';
$lang['de_DE']['Page']['ORDERD_ARTICLES'] = 'ordered articles';
$lang['de_DE']['Page']['ORDER_COMPLETED'] = 'Your order is completed';
$lang['de_DE']['Page']['ORDER_DATE'] = 'order date';
$lang['de_DE']['Page']['ORDER_THANKS'] = 'Many thanks for Your oder.';
$lang['de_DE']['Page']['PASSWORD'] = 'password';
$lang['de_DE']['Page']['PASSWORD_CASE_EMPTY'] = 'If You leave this field empty, Your password will not be changed.';
$lang['de_DE']['Page']['PAYMENT_NOT_WORKING'] = 'The choosen payment module does not work.';
$lang['de_DE']['Page']['PLUS_SHIPPING'] = 'plus shipping';
$lang['de_DE']['Page']['PREV'] = 'prev';
$lang['de_DE']['Page']['PROCESSING_FEE'] = 'processing fee';
$lang['de_DE']['Page']['REMARKS'] = 'REMARKS';
$lang['de_DE']['Page']['REMOVE_FROM_CART'] = 'remove';
$lang['de_DE']['Page']['REVOCATION'] = 'revocation instructions';
$lang['de_DE']['Page']['SESSION_EXPIRED'] = 'Your session has expired.';
$lang['de_DE']['Page']['SHIPPING_ADDRESS'] = 'shipping address';
$lang['de_DE']['Page']['SHIPPING_AND_BILLING'] = 'shipping and billing address';
$lang['de_DE']['Page']['SHOP_WITHOUT_REGISTRATION'] = 'shop without registration';
$lang['de_DE']['Page']['SHOWINPAGE'] = 'set language to %s';
$lang['de_DE']['Page']['SITMAP_HERE'] = 'Here You can see the complete directory to our site.';
$lang['de_DE']['Page']['STEPS'] = 'steps';
$lang['de_DE']['Page']['SUBTOTAL'] = 'subtotal';
$lang['de_DE']['Page']['SUM'] = 'sum';
$lang['de_DE']['Page']['TAX'] = 'incl. %s%% VAT';
$lang['de_DE']['Page']['TERMSOFSERVICE_PRIVACY'] = 'terms of service and privacy statement';
$lang['de_DE']['Page']['THE_QUERY'] = 'The query';
$lang['de_DE']['Page']['TITLE_IMPRINT'] = 'imprint';
$lang['de_DE']['Page']['TITLE_TERMS'] = 'terms of service';
$lang['de_DE']['Page']['TOTAL'] = 'total';
$lang['de_DE']['Page']['URL_SEGMENT_IMPRINT'] = 'imprint';
$lang['de_DE']['Page']['URL_SEGMENT_TERMS'] = 'terms-of-service';
$lang['de_DE']['Page']['VIEW_ORDERS_TEXT_AND_LINK'] = 'You can check the status of Your order any time in Your <a href="/%s/%s">order overview</a>';
$lang['de_DE']['Page']['WELCOME_PAGE_TITLE'] = 'welcome';
$lang['de_DE']['Page']['WELCOME_PAGE_URL_SEGMENT'] = 'welcome';
$lang['de_DE']['PaymentMethod']['BASIC_SETTINGS'] = 'basic settings';
$lang['de_DE']['PaymentMethod']['FEE'] = 'payment method fee';
$lang['de_DE']['PaymentMethod']['PLURALNAME'] = array(
    'Bezahlarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentMethod']['SHIPPINGMETHOD'] = 'shipping method';
$lang['de_DE']['PaymentMethod']['SINGULARNAME'] = array(
    'Bezahlart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentMethod']['STANDARD_ORDER_STATUS'] = 'standard order status for this payment method';
$lang['de_DE']['PaymentMethod']['TITLE'] = 'payment method';
$lang['de_DE']['PaymentMethodTexts']['PLURALNAME'] = array(
    'Bezahlartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentMethodTexts']['SINGULARNAME'] = array(
    'Bezahlartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentNotification']['PLURALNAME'] = array(
    'Payment Notifications',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['PaymentNotification']['SINGULARNAME'] = array(
    'Payment Notification',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['PaymentNotification']['TITLE'] = 'payment notification';
$lang['de_DE']['PaymentNotification']['URL_SEGMENT'] = 'payment-notification';
$lang['de_DE']['Price']['PLURALNAME'] = array(
    'prices',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Price']['SINGULARNAME'] = array(
    'price',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['ALREADY_REGISTERES_MESSAGE_TEXT'] = 'message: user already registered';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATIONMAIL_SUBJECT'] = 'confirmation mail: subject';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATIONMAIL_TEXT'] = 'confirmation mail: text';
$lang['de_DE']['RegisterConfirmationPage']['CONFIRMATION_MAIL'] = 'confirmation mail';
$lang['de_DE']['RegisterConfirmationPage']['FAILURE_MESSAGE_TEXT'] = 'failure message';
$lang['de_DE']['RegisterConfirmationPage']['PLURALNAME'] = array(
    'Registrierungsbestätigungsseits',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['SINGULARNAME'] = array(
    'Registrierungsbestätigungsseite',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegisterConfirmationPage']['SUCCESS_MESSAGE_TEXT'] = 'success message';
$lang['de_DE']['RegisterConfirmationPage']['TITLE'] = 'register confirmation page';
$lang['de_DE']['RegisterConfirmationPage']['URL_SEGMENT'] = 'register-confirmation';
$lang['de_DE']['RegisterWelcomePage']['PLURALNAME'] = array(
    'Register welcome pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegisterWelcomePage']['SINGULARNAME'] = array(
    'register welcome page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL'] = 'activation mail';
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL_SUBJECT'] = 'activation mail subject';
$lang['de_DE']['RegistrationPage']['ACTIVATION_MAIL_TEXT'] = 'activation mail text';
$lang['de_DE']['RegistrationPage']['CONFIRMATION_TEXT'] = '<h1>Complete registration</h1><p>Please confirm Your activation or copy the link to Your Browser.</p><p><a href="$ConfirmationLink">Confirm registration</a></p><p>In case You did not register please ignore this mail.</p><p>Your shop team</p>';
$lang['de_DE']['RegistrationPage']['CUSTOMER_SALUTATION'] = 'Dear customer\,';
$lang['de_DE']['RegistrationPage']['PLEASE_COFIRM'] = 'please confirm Your registration';
$lang['de_DE']['RegistrationPage']['PLURALNAME'] = array(
    'Registration pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegistrationPage']['SINGULARNAME'] = array(
    'registration page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['RegistrationPage']['SUCCESS_TEXT'] = '<h1>Registration completed successfully!</h1><p>Many thanks for Your registration.</p><p>Have a nice time on our website!</p><p>Your webshop team</p>';
$lang['de_DE']['RegistrationPage']['THANKS'] = 'Many thanks for Your registration';
$lang['de_DE']['RegistrationPage']['TITLE'] = 'registration page';
$lang['de_DE']['RegistrationPage']['URL_SEGMENT'] = 'registration';
$lang['de_DE']['RegistrationPage']['YOUR_REGISTRATION'] = 'your registration';
$lang['de_DE']['RegularCustomer']['PLURALNAME'] = array(
    'Regular Customers',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['RegularCustomer']['REGULARCUSTOMER'] = 'regular customer';
$lang['de_DE']['RegularCustomer']['SINGULARNAME'] = array(
    'Regular Customer',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SearchResultsPage']['PLURALNAME'] = array(
    'Search results pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['SearchResultsPage']['SINGULARNAME'] = array(
    'search results page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['SearchResultsPage']['TITLE'] = 'search results';
$lang['de_DE']['SearchResultsPage']['URL_SEGMENT'] = 'search-results';
$lang['de_DE']['ShippingAddress']['PLURALNAME'] = array(
    'Versandadressen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingAddress']['SINGULARNAME'] = array(
    'Versandadresse',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFee']['EMPTYSTRING_CHOOSEZONE'] = '--choose zone--';
$lang['de_DE']['ShippingFee']['PLURALNAME'] = array(
    'Versandtarife',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingFee']['SINGULARNAME'] = array(
    'Versandtarif',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFee']['TITLE'] = 'shipping fee';
$lang['de_DE']['ShippingFee']['ZONE_WITH_DESCRIPTION'] = 'zone (only carrier\'s zones available)';
$lang['de_DE']['ShippingFeesPage']['PLURALNAME'] = array(
    'Shipping fees pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingFeesPage']['SINGULARNAME'] = array(
    'shipping fees page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingFeesPage']['TITLE'] = 'shipping fees';
$lang['de_DE']['ShippingFeesPage']['URL_SEGMENT'] = 'shipping-fees';
$lang['de_DE']['ShippingMethod']['PLURALNAME'] = array(
    'Versandarten',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingMethod']['SINGULARNAME'] = array(
    'Versandart',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShippingMethodTexts']['PLURALNAME'] = array(
    'Lieferartübersetzungen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShippingMethodTexts']['SINGULARNAME'] = array(
    'Lieferartübersetzung',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShopAdmin']['PAYMENT_ISACTIVE'] = 'activated';
$lang['de_DE']['ShopAdmin']['PAYMENT_MAXAMOUNTFORACTIVATION'] = 'Höchstbetrag für Modul';
$lang['de_DE']['ShopAdmin']['PAYMENT_MINAMOUNTFORACTIVATION'] = 'Mindestbetrag für Modul';
$lang['de_DE']['ShopEmail']['PLURALNAME'] = array(
    'Shop Emails',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShopEmail']['SINGULARNAME'] = array(
    'Shop Email',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShoppingCart']['PLURALNAME'] = array(
    'Warenkörbe',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShoppingCart']['SINGULARNAME'] = array(
    'Warenkorb',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['ShoppingCartPosition']['PLURALNAME'] = array(
    'Warenkorbpositionen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['ShoppingCartPosition']['SINGULARNAME'] = array(
    'Warenkorbposition',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Tax']['PLURALNAME'] = array(
    'Steuersätze',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Tax']['SINGULARNAME'] = array(
    'Steuersatz',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['TermsAndConditionsPage']['PLURALNAME'] = array(
    'Terms and conditions pags',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['TermsAndConditionsPage']['SINGULARNAME'] = array(
    'terms and conditions page',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Zone']['COUNTRIES'] = 'countries';
$lang['de_DE']['Zone']['PLURALNAME'] = array(
    'Zonen',
    50,
    'Pural name of the object, used in dropdowns and to generally identify a collection of this object in the interface'
);
$lang['de_DE']['Zone']['SINGULARNAME'] = array(
    'Zone',
    50,
    'Singular name of the object, used in dropdowns and to generally identify a single object in the interface'
);
$lang['de_DE']['Address']['MISTER'] = 'Herr';
$lang['de_DE']['Address']['MISSIS'] = 'Frau';