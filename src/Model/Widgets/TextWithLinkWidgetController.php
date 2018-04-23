<?php

namespace SilverCart\Model\Widgets;

use SilverCart\Model\Widgets\TextWidgetController;
use SilverStripe\Control\HTTP;
use SilverStripe\View\ArrayData;
use SilverStripe\View\SSViewer_FromString;

/**
 * TextWithLinkWidget Controller.
 *
 * @package SilverCart
 * @subpackage Model_Widgets
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 09.10.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TextWithLinkWidgetController extends TextWidgetController {

    /**
     * Overloaded from {@link Widget->Content()}
     * to allow for controller/form linking.
     *
     * @return string HTML
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 20.06.2013
     */
    public function Content() {
        $renderData = array(
            'Controller' => $this
        );
        $template = new SSViewer_FromString($this->getField('FreeText'));
        $freeText = HTTP::absoluteURLs($template->process(new ArrayData($renderData)));

        $data = new ArrayData(
            array(
                'FreeText'  => $freeText,
                'LinkText'  => $this->LinkText,
                'Link'      => $this->Link
            )
        );
        
        return $this->customise($data)->renderWith(get_class($this->widget));
    }
}