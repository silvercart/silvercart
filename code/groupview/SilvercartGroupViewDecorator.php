<?php

/**
 * Decorator for PageTypes which have grouped views. Provides a group view
 * specific functionality to its decorated owner.
 *
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2010 pixeltricks GmbH
 * @since 14.02.2011
 * @license BSD
 */
class SilvercartGroupViewDecorator extends DataObjectDecorator {

    /**
     * add switchGroupView to allowed_actions
     *
     * @var array
     */
    public static $allowed_actions = array(
        'switchGroupView',
    );

    protected $groupViewObject = null;

    /**
     * returns all group views
     *
     * @return DataObjectSet
     */
    public function getGroupViews() {
        $groupViewArray = array();
        foreach (SilvercartGroupViewHandler::getGroupViews() as $code => $groupView) {
            $groupViewArray[] = new $groupView();
        }
        return new DataObjectSet($groupViewArray);
    }

    /**
     * checkes, whether more than $count group views are existant.
     *
     * @param int $count count
     *
     * @return bool
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function hasMoreGroupViewsThan($count) {
        return count(SilvercartGroupViewHandler::getGroupViews()) > $count;
    }

    /**
     * switches the group view to the via URL parameter 'ID' given type (if
     * existant)
     *
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 10.02.2011
     * @see self::$productGroupViews
     */
    public function switchGroupView() {
        if (array_key_exists('ID', $this->owner->urlParams)) {
            SilvercartGroupViewHandler::setGroupView($this->owner->urlParams['ID']);
        }
        Director::redirect('/' . $this->owner->URLSegment);
    }

    /**
     * returns the required CartFormName required by the decorators owner in
     * dependence on the active group view.
     *
     * @return string
     */
    public function getCartFormName() {
        return 'ProductAddCartForm' . SilvercartGroupViewHandler::getActiveGroupViewAsUpperCamelCase();
    }

    /**
     * returns the code of the active group view
     *
     * @return string
     */
    public function getActiveGroupView() {
        return SilvercartGroupViewHandler::getActiveGroupView();
    }

    /**
     * this is used to render the ProductGroupHolder template in dependence on
     * the active group view.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupHolderGroupView() {
        $items = array();
        foreach ($this->owner->Children() as $child) {
            if ($child->hasArticlesOrChildren()) {
                $items[] = $child;
            }
        }
        $elements = array(
            'Elements' => new DataObjectSet($items),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupHolderTemplateName(),
            )
        );
        return $output;
    }

    /**
     * this is used to render the ProductGroupPage template in dependence on
     * the active group view.
     *
     * @return string
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 15.02.2011
     */
    public function RenderProductGroupPageGroupView() {
        $elements = array(
            'Elements' => $this->owner->getArticles(),
        );
        $output = $this->owner->customise($elements)->renderWith(
            array(
                $this->getProductGroupPageTemplateName(),
            )
        );
        return $output;
    }

    /**
     * returns the required ProductGroupHolder template name required by the
     * decorators owner in dependence on the active group view.
     *
     * @return string
     */
    protected function getProductGroupHolderTemplateName() {
        return 'ProductGroupHolder' . SilvercartGroupViewHandler::getActiveGroupViewAsUpperCamelCase();
    }

    /**
     * returns the required ProductGroupPage template name required by the
     * decorators owner in dependence on the active group view.
     *
     * @return string
     */
    protected function getProductGroupPageTemplateName() {
        return 'ProductGroupPage' . SilvercartGroupViewHandler::getActiveGroupViewAsUpperCamelCase();
    }
}