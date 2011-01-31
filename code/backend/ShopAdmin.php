<?php

/**
 * The shops backend admin interface
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 08.11.2010
 * @license none
 */
class ShopAdmin extends LeftAndMain {

    /**
     * defines the base objects
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $base_models = array(
        'PaymentMethods' => 'PaymentMethod',
        'ShippingMethods' => 'ShipppingMethod',
        'Zones' => 'Zone',
        'Taxes' => 'Tax',
        'Emails' => 'Email'
    );
    /**
     * define the url segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */
    public static $url_segment = 'shopadmin';

    /**
     * Legt die Bezeichnung in der Navigation fest.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 08.11.2010
     */

    public static $menu_title = 'shop administration';

    /**
     * defines url actions
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public static $url_rule = '/$Action';

    /**
     * defines the allowed actions
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public static $allowed_actions = array(
        'payment',
        'shipping',
        'zone',
        'tax',
        'email',
        'EditForm'
    );

    /**
     * init method
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function init() {
        parent::init();

        Requirements::javascript(PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'js/ShopAdmin_right.js');
        Requirements::css(PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'css/ShopAdmin.css');
    }

    /**
     * returns the current section
     *
     * Used by navigation in the left column to mark the selected menu entry
     *
     * @return string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 210.11.2010
     */
    public function Section() {
        $url = rtrim($_SERVER['REQUEST_URI'], '/');

        if (strrpos($url, '&')) {
            $url = substr($url, 0, strrpos($url, '&'));
        }
        $section = substr($url, strrpos($url, '/') + 1);

        if ($section != 'payment' && $section != 'shipping' && $section != 'zone' && $section != 'tax' && $section != 'email') {
            $section = Session::get('ShopAdminSection');
        }

        if ($section != 'payment' && $section != 'shipping' && $section != 'zone' && $section != 'tax' && $section != 'email') {
            $section = 'payment';
        }

        return $section;
    }

    /**
     * returns the form for the popup
     *
     * @return Form
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @since 1.1.2011
     */
    public function EditForm() {
        $section = $this->Section();

        switch ($section) {
            case 'shipping':
                return $this->ShippingTable();
                break;
            case 'zone':
                return $this->ZoneTable();
                break;
            case 'tax':
                return $this->TaxTable();
                break;
            case 'email':
                return $this->EmailTable();
                break;
            case 'payment':
            default:
                return $this->PaymentTable();
        }
    }

    /**
     * overview of payment methods
     *
     * @param array $params ???
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function payment($params) {
        Session::set('ShopAdminSection', 'payment');

        return $this->renderWith('ShopAdmin_right_payment');
    }

    /**
     * returns overview of payment methods as table
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function PaymentTable() {
        $section = $this->Section();

        // --------------------------------------------------------------------
        // define table
        // --------------------------------------------------------------------
        // define table´s heading
        $tableFields = array(
            "Name" => _t('ShopAdmin.PAYMENT_NAME', 'Name'),
            "isActive" => _t('ShopAdmin.PAYMENT_ISACTIVE', 'Aktiviert')
        );

        $table = new PaymentTableField(
                        $this,
                        _t('ShopAdmin.PAYMENTMETHODS', 'Bezahlarten'),
                        "PaymentMethod",
                        $tableFields,
                        'getCMSFields_forPopup',
                        array('1 = 1'),
                        'Created DESC'
        );
        $table->setParentClass(false);
        $table->setFieldCasting(array(
            'Created' => 'SSDatetime->Full',
            'Comment' => array('HTMLText->LimitCharacters', 150)
        ));
        $table->Markable = false;
        $table->setPageSize(20);

        $idField = new HiddenField('ID', '', $section);

        // --------------------------------------------------------------------
        // Formular definieren
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.PAYMENT_NAME', 'Bezahlarten')),
                        $table,
                        $idField
        );
        $actions = new FieldSet();

        $form = new Form(
                        $this,
                        'EditForm',
                        $fields,
                        $actions
        );

        return $form;
    }

    /**
     * shows an overview of shipping methods
     *
     * @param array $params ???
     *
     * @return ???
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function shipping($params) {
        Session::set('ShopAdminSection', 'shipping');

        return $this->renderWith('ShopAdmin_right_shipping');
    }

    /**
     * returns overview of shipping methods as table
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function ShippingTable() {
        $section = $this->Section();

        // --------------------------------------------------------------------
        // define table
        // --------------------------------------------------------------------
        // table headings
        $tableFields = array(
            "Title" => _t('ShopAdmin.SHIPPING_TITLE', 'Title'),
            "isActive" => _t('ShopAdmin.SHIPPING_ISACTIVE', 'Aktiviert')
        );

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $popupFields = new FieldSet(
                        new TextField('isActive', _t('ShopAdmin.SHIPPING_ISACTIVE', 'Aktiviert')),
                        new TextField('minAmountForActivation', _t('ShopAdmin.SHIPPING_MINAMOUNTFORACTIVATION', 'minimum amout for module')),
                        new TextField('maxAmountForActivation', _t('ShopAdmin.SHIPPING_MAXAMOUNTFORACTIVATION', 'maximum amount for module'))
        );

        $table = new ShippingTableField(
                        $this,
                        _t('ShopAdmin.SHIPPINGMETHODS', 'shipping methods'),
                        "ShippingMethod",
                        $tableFields,
                        $popupFields,
                        array('1 = 1'),
                        'Created DESC'
        );

        $table->setParentClass(false);
        $table->setFieldCasting(array(
            'Created' => 'SSDatetime->Full',
            'Comment' => array('HTMLText->LimitCharacters', 150)
        ));
        $table->Markable = false;
        $table->setPageSize(20);

        $idField = new HiddenField('ID', '', $section);

        // --------------------------------------------------------------------
        // define form
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.SHIPPINGMETHODS', 'Versandarten')),
                        $table,
                        $idField
        );
        $actions = new FieldSet();

        $form = new Form(
                        $this,
                        'EditForm',
                        $fields,
                        $actions
        );

        return $form;
    }

    /**
     * shows zone overview
     *
     * @param array $params ???
     *
     * @return ???
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function zone($params) {
        Session::set('ShopAdminSection', 'zone');

        return $this->renderWith('ShopAdmin_right_zone');
    }

    /**
     * zone overview as table
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.10.2010
     */
    public function ZoneTable() {
        $section = $this->Section();

        // --------------------------------------------------------------------
        // define table
        // --------------------------------------------------------------------
        // table's headings
        $tableFields = array(
            "Name" => _t('ShopAdmin.ZONE_NAME', 'name'),
            "isActive" => _t('ShopAdmin.ZONE_ISACTIVE', 'activated')
        );

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $popupFields = new FieldSet(
                        new TextField('isActive', _t('ShopAdmin.ZONE_ISACTIVE', 'activated')),
                        new TextField('minAmountForActivation', _t('ShopAdmin.ZONE_MINAMOUNTFORACTIVATION', 'minimum amout for module')),
                        new TextField('maxAmountForActivation', _t('ShopAdmin.ZONE_MAXAMOUNTFORACTIVATION', 'maximum amount for module'))
        );

        $table = new ShippingTableField(
                        $this,
                        _t('ShopAdmin.ZONES', 'zones'),
                        "Shipping",
                        $tableFields,
                        $popupFields,
                        array('1 = 1'),
                        'Created DESC'
        );

        $table->setParentClass(false);
        $table->setFieldCasting(array(
            'Created' => 'SSDatetime->Full',
            'Comment' => array('HTMLText->LimitCharacters', 150)
        ));
        $table->Markable = false;
        $table->setPageSize(20);

        $idField = new HiddenField('ID', '', $section);

        // --------------------------------------------------------------------
        // define form
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.ZONE_TITLE', 'Zonen')),
                        $table,
                        $idField
        );
        $actions = new FieldSet();

        $form = new Form(
                        $this,
                        'EditForm',
                        $fields,
                        $actions
        );

        return $form;
    }

    /**
     * tax's overview
     *
     * @param array $params ???
     *
     * @return ???
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function tax($params) {
        Session::set('ShopAdminSection', 'tax');

        return $this->renderWith('ShopAdmin_right_tax');
    }

    /**
     * tax's overview as a table
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 24.11.2010
     */
    public function TaxTable() {
        $section = $this->Section();

        // --------------------------------------------------------------------
        // define table
        // --------------------------------------------------------------------
        // define table's headings
        $tableFields = array(
            "Title" => _t('ShopAdmin.TAX_TITLE', 'title'),
            "Rate" => _t('ShopAdmin.TAX_RATE', 'rate')
        );

        // Popupfelder fuers Bearbeiten des Steuersatzes
        $popupFields = new FieldSet(
                        new TextField('Title', _t('ShopAdmin.TAX_TITLE', 'title')),
                        new TextField('Rate', _t('ShopAdmin.TAX_RATE', 'tax rate in percent'))
        );

        $table = new TaxTableField(
                        $this,
                        _t('ShopAdmin.TAXRATES', 'tax rates'),
                        "Tax",
                        $tableFields,
                        $popupFields,
                        array('1 = 1'),
                        'Created DESC'
        );

        $table->setParentClass(false);
        $table->setFieldCasting(array(
            'Created' => 'SSDatetime->Full',
            'Comment' => array('HTMLText->LimitCharacters', 150)
        ));
        $table->Markable = false;
        $table->setPageSize(20);

        $idField = new HiddenField('ID', '', $section);

        // --------------------------------------------------------------------
        // define form
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.TAX_TITLE', 'Steuersätze')),
                        $table,
                        $idField
        );
        $actions = new FieldSet();

        $form = new Form(
                        $this,
                        'EditForm',
                        $fields,
                        $actions
        );

        return $form;
    }

    /**
     * shows email's overview
     *
     * @param array $params ???
     *
     * @return ???
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    public function email($params) {
        Session::set('ShopAdminSection', 'email');

        return $this->renderWith('ShopAdmin_right_email');
    }

    /**
     * returns tax rate's overview as table
     *
     * @return Form
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 03.12.2010
     */
    public function EmailTable() {
        $section = $this->Section();

        // --------------------------------------------------------------------
        // define table
        // --------------------------------------------------------------------
        // define table's headings
        $tableFields = array(
            "Identifier" => _t('ShopAdmin.EMAIL_IDENTIFIER', 'identifier'),
            "Subject" => _t('ShopAdmin.EMAIL_SUBJECT', 'subject')
        );

        // Popupfelder fuers Bearbeiten der Email
        $popupFields = new FieldSet(
                        new TextField('Identifier', _t('ShopAdmin.EMAIL_IDENTIFIERT', 'identifier')),
                        new TextField('Subject', _t('ShopAdmin.EMAIL_SUBJECT', 'subject')),
                        new TextareaField('EmailText', _t('ShopAdmin.EMAIL_TEXT', 'text'), 8),
                        new TextareaField('Variables', _t('ShopAdmin.EMAIL_VARIABLES', 'variables'))
        );

        $table = new EmailTableField(
                        $this,
                        _t('ShopAdmin.EMAILS', 'emails'),
                        "ShopEmail",
                        $tableFields,
                        $popupFields,
                        array('1 = 1'),
                        'Created DESC'
        );

        $table->setParentClass(false);
        $table->setFieldCasting(array(
            'Created' => 'SSDatetime->Full',
            'Comment' => array('HTMLText->LimitCharacters', 150)
        ));
        $table->Markable = false;
        $table->setPageSize(20);

        $idField = new HiddenField('ID', '', $section);

        // --------------------------------------------------------------------
        // define form
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.EMAIL_TITLE', 'title')),
                        $table,
                        $idField
        );
        $actions = new FieldSet();

        $form = new Form(
                        $this,
                        'EditForm',
                        $fields,
                        $actions
        );

        return $form;
    }

}
