<?php

/**
 * table for display of taxes
 *
 * @package fashionbids
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.11.2010
 * @license none
 */
class TaxTableField extends ComplexTableField {

    /**
     * the template for the table
     *
     * @var string
     */
    protected $template = "TaxTableField";

    /**
     * constructor
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
     * returns the FieldHolder
     *
     * @return FieldHolder
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 10.11.2010
     */
    public function FieldHolder() {
        $ret = parent::FieldHolder();

        Requirements::javascript(PIXELTRICKS_CHECKOUT_BASE_PATH_REL . 'js/TaxTableField.js');

        return $ret;
    }

    /**
     * description
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
            $output->push(Object::create('TaxTableField_Item', $item, $this, $pageStart + $pageIndex));
        }
        return $output;
    }

}

/**
 * ???
 *
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 24.11.2010
 * @license none
 */
class TaxTableField_Item extends ComplexTableField_Item {

}

