<?php

/**
 * Spezielle Tabelle fuer die Auflistung von Zahlungsarten.
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 10.10.2010
 * @license none
 */
class PaymentTableField extends ComplexTableField {

    /**
     * Das Template fuer die Tabelle.
     *
     * @var string
     */
    protected $template = "PaymentTableField";

    /**
     * Konstruktor
     *
     * @param string $controller       current controller
     * @param string $name             field name
     * @param string $sourceClass      ???
     * @param string $fieldList        ???
     * @param string $detailFormFields ???
     * @param string $sourceFilter     filter results
     * @param string $sourceSort       sort order
     * @param string $sourceJoin       sql join
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public function __construct($controller, $name, $sourceClass, $fieldList, $detailFormFields = null, $sourceFilter = "", $sourceSort = "Created", $sourceJoin = "") {
        parent::__construct($controller, $name, $sourceClass, $fieldList, $detailFormFields, $sourceFilter, $sourceSort, $sourceJoin);
    }

    /**
     * Liefert den FieldHolder
     *
     * @return FieldHolder
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
   public function FieldHolder() {
        $ret = parent::FieldHolder();

        Requirements::javascript(PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'js/PaymentTableField.js');

        return $ret;
    }

    /**
     * Beschreibung
     *
     * @return DataObjectSet
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public function Items() {
        $this->sourceItems = $this->sourceItems();

        if (!$this->sourceItems) {
            return null;
        }

        $pageStart = (isset($_REQUEST['ctf'][$this->Name()]['start']) && is_numeric($_REQUEST['ctf'][$this->Name()]['start'])) ? $_REQUEST['ctf'][$this->Name()]['start'] : 0;
        $this->sourceItems->setPageLimits($pageStart, $this->pageSize, $this->totalCount);

        $output = new DataObjectSet();
        foreach ($this->sourceItems as $pageIndex => $item) {
            $output->push(Object::create('PaymentTableField_Item', $item, $this, $pageStart + $pageIndex));
        }
        return $output;
    }

}

/**
 * Single row of a {@link CommentTableField}
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 10.10.2010
 * @license none
 */
class PaymentTableField_Item extends ComplexTableField_Item {

}