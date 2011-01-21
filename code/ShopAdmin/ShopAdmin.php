<?php

/**
 * Die Verwaltungsmaske fuer die Bezahlarten.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 08.11.2010
 * @license none
 */
class ShopAdmin extends LeftAndMain {

    /**
     * Legt die Basisobjekte fest.
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
     * Legt die URL fest, unter der die Maske erreichbar ist.
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
    public static $menu_title = 'Shopverwaltung';
    /**
     * Legt die auswertbaren URL-Parameter fest.
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public static $url_rule = '/$Action';
    /**
     * Legt die erlaubten Aktionen fest.
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
     * Initialisierung
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
     * Liefert die aktuelle Sektion zurueck.
     *
     * Wird von der Navigation in der linken Spalte verwendet, um den
     * aktuellen Menuepunkt hervorzuheben.
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
     * Liefert das Formular fuer die Popupfelder zurueck.
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
     * Zeigt die Uebersicht der Bezahlarten an.
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
     * Liefert die Uebersicht der Bezahlarten als Tabelle zurueck.
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
        // Tabelle definieren
        // --------------------------------------------------------------------
        // Ueberschriften der Tabelle definieren
        $tableFields = array(
            "Name" => _t('ShopAdmin.PAYMENT_NAME', 'Name'),
            "isActive" => _t('ShopAdmin.PAYMENT_ISACTIVE', 'Aktiviert')
        );

        $table = new PaymentTableField(
                        $this,
                        "Bezahlarten",
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
                        new LiteralField("Title", _t('ShopAdmin.PAYMENT_TITLE', 'Bezahlarten')),
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
     * Zeigt die Uebersicht der Versandarten an.
     *
     * @param array $params ???
     *
     * @return void
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
     * Liefert die Uebersicht der Versandarten als Tabelle zurueck.
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
        // Tabelle definieren
        // --------------------------------------------------------------------
        // Ueberschriften der Tabelle definieren
        $tableFields = array(
            "Title" => _t('ShopAdmin.SHIPPING_TITLE', 'Title'),
            "isActive" => _t('ShopAdmin.SHIPPING_ISACTIVE', 'Aktiviert')
        );

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $popupFields = new FieldSet(
                        new TextField('isActive', _t('ShopAdmin.SHIPPING_ISACTIVE', 'Aktiviert')),
                        new TextField('minAmountForActivation', _t('ShopAdmin.SHIPPING_MINAMOUNTFORACTIVATION', 'Mindestbetrag für Modul')),
                        new TextField('maxAmountForActivation', _t('ShopAdmin.SHIPPING_MAXAMOUNTFORACTIVATION', 'Höchstbetrag für Modul'))
        );

        $table = new ShippingTableField(
                        $this,
                        "Versandarten",
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
        // Formular definieren
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.SHIPPING_TITLE', 'Versandarten')),
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
     * Zeigt die Uebersicht der Zonen an.
     *
     * @param array $params ???
     *
     * @return void
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
     * Liefert die Uebersicht der Zonen als Tabelle zurueck.
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
        // Tabelle definieren
        // --------------------------------------------------------------------
        // Ueberschriften der Tabelle definieren
        $tableFields = array(
            "Name" => _t('ShopAdmin.ZONE_NAME', 'Name'),
            "isActive" => _t('ShopAdmin.ZONE_ISACTIVE', 'Aktiviert')
        );

        // Popupfelder fuers Bearbeiten der Zahlungsart
        $popupFields = new FieldSet(
                        new TextField('isActive', _t('ShopAdmin.ZONE_ISACTIVE', 'Aktiviert')),
                        new TextField('minAmountForActivation', _t('ShopAdmin.ZONE_MINAMOUNTFORACTIVATION', 'Mindestbetrag für Modul')),
                        new TextField('maxAmountForActivation', _t('ShopAdmin.ZONE_MAXAMOUNTFORACTIVATION', 'Höchstbetrag für Modul'))
        );

        $table = new ShippingTableField(
                        $this,
                        "Zonen",
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
        // Formular definieren
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
     * Zeigt die Uebersicht der Steuersaetze an.
     *
     * @param array $params ???
     *
     * @return void
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
     * Liefert die Uebersicht der Steuersaetze als Tabelle zurueck.
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
        // Tabelle definieren
        // --------------------------------------------------------------------
        // Ueberschriften der Tabelle definieren
        $tableFields = array(
            "Title" => _t('ShopAdmin.TAX_TITLE', 'Title'),
            "Rate" => _t('ShopAdmin.TAX_RATE', 'Rate')
        );

        // Popupfelder fuers Bearbeiten des Steuersatzes
        $popupFields = new FieldSet(
                        new TextField('Title', _t('ShopAdmin.TAX_TITLE', 'Titel')),
                        new TextField('Rate', _t('ShopAdmin.TAX_RATE', 'Steuersatz in Prozent'))
        );

        $table = new TaxTableField(
                        $this,
                        "Steuersätze",
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
        // Formular definieren
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
     * Zeigt die Uebersicht der Emails an.
     *
     * @param array $params ???
     *
     * @return void
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
     * Liefert die Uebersicht der Steuersaetze als Tabelle zurueck.
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
        // Tabelle definieren
        // --------------------------------------------------------------------
        // Ueberschriften der Tabelle definieren
        $tableFields = array(
            "Identifier" => _t('ShopAdmin.EMAIL_IDENTIFIER', 'Bezeichner'),
            "Subject" => _t('ShopAdmin.EMAIL_SUBJECT', 'Betreff')
        );

        // Popupfelder fuers Bearbeiten der Email
        $popupFields = new FieldSet(
                        new TextField('Identifier', _t('ShopAdmin.EMAIL_IDENTIFIERT', 'Bezeichner')),
                        new TextField('Subject', _t('ShopAdmin.EMAIL_SUBJECT', 'Betreff')),
                        new TextareaField('EmailText', _t('ShopAdmin.EMAIL_TEXT', 'Text'), 8),
                        new TextareaField('Variables', _t('ShopAdmin.EMAIL_VARIABLES', 'Variablen'))
        );

        $table = new EmailTableField(
                        $this,
                        "Emails",
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
        // Formular definieren
        // --------------------------------------------------------------------
        $fields = new FieldSet(
                        new LiteralField("Title", _t('ShopAdmin.EMAIL_TITLE', 'EMAILS')),
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
