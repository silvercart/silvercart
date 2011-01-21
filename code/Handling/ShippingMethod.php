<?php

/**
 * Theses are the shipping methods the shop offers
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 20.10.2010
 * @license BSD
 */
class ShippingMethod extends DataObject {

    public static $singular_name = "Versandart";
    public static $plural_name = "Versandarten";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_one = array(
        'carrier' => 'Carrier'
    );
    public static $has_many = array(
        'orders' => 'Order',
        'translations' => 'ShippingMethodTexts',
        'shippingFees' => 'ShippingFee'
    );
    public static $many_many = array(
        'zones' => 'Zone'
    );
    public static $belongs_many_many = array(
        'PaymentMethods' => 'PaymentMethod'
    );
    public static $summary_fields = array(
        'Title' => 'Bezeichnung',
        'carrier.Title' => 'Frachtführer'
    );
    public static $field_labels = array(
        'Title' => 'Bezeichnung'
    );
    public static $searchable_fields = array(
        'Title'
    );

    /**
     * default instances will be created if no instance exists at all
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 20.10.2010
     * @return void
     */
    public function requireDefaultRecords() {
        parent::requireDefaultRecords();
        $obj = DataObject::get($this->ClassName);
        if (!$obj) {
            $shippingMethod = new ShippingMethod();
            $shippingMethod->Title = 'Paket';
            $shippingMethod->write();
        }
    }

    /**
     * customizes the backends fields, mainly for ModelAdmin
     *
     * @return FieldSet the fields for the backend
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 28.10.10
     */
    public function getCMSFields() {
        $fields = parent::getCMSFields();
        $fields->removeByName('countries'); //not needed because relations can not be made
        $fields->removeByName('PaymentMethods');
        $fields->removeByName('orders');
        $fields->removeByName('zones');
        $zonesTable = new ManyManyComplexTableField(
                $this,
                'zones',
                'Zone',
                null,
                'getCMSFields_forPopup'
                );
        $fields->addFieldToTab('Root.Zone', $zonesTable);
        return $fields;
    }

    /**
     * determins the right shipping fee for a shipping method depending on the cart´s weight
     *
     * @return ShippingFee the most convenient shipping fee for this shipping method
     * @since 9.11.2010
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     */
    public function getShippingFee() {
        $cartWeightTotal = Member::currentUser()->shoppingCart()->getWeightTotal();
        if ($cartWeightTotal) {
            $filter = sprintf("`shippingMethodID` = '%s' AND `MaximumWeight` >= '%s'", $this->ID, $cartWeightTotal);
            $fees = DataObject::get('ShippingFee', $filter);
            if ($fees) {
                $fees->sort('PriceAmount');
                $fee = $fees->First();
                return $fee;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * pseudo attribute which can be called with $this->TitleWithCarrierAndFee
     *
     * @return string carrier + title + fee
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 15.11.2010
     */
    public function getTitleWithCarrierAndFee() {
        if ($this->getShippingFee()) {
            $titleWithCarrierAndFee = $this->carrier()->Title . "-" . $this->Title ." (+". number_format($this->getShippingFee()->Price->getAmount(), 2, ',', '') . "€)";
            return $titleWithCarrierAndFee;
        }
    }

}
