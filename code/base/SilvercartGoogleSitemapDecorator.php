<?php

/**
 * Product detail pages get shown in sitemap.xml
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 03.06.2011
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 */
class SilvercartGoogleSitemapDecorator extends DataObjectDecorator {

    /**
     * Add product detail pages to the sitemap.xml
     *
     * @param DataObjectSet $newPages the Page objects to be parsed for the sitemap.xml
     * 
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 3.6.2011
     * @return DataObjectSet DataObjects and Pages to be parsed for the sitemap.xml
     */
    public function updateItems($newPages) {
        $products = SilvercartProduct::get();
        if ($products) {
            foreach ($products as $product) {
                $product->AbsoluteLink = Director::protocolAndHost() . $product->Link();
                $product->ChangeFreq = "monthly";
                $product->Priority = "0.6";
                $newPages->push($product);
            }
        }
        return $newPages;
    }

}

