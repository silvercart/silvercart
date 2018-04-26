<?php

namespace SilverCart\View\Printer;

use SilverCart\Model\Pages\PageController;
use SilverCart\View\Printer\Printer;
use SilverStripe\Control\Director;
use SilverStripe\ORM\DataObject;

/**
 * Default controller to get the print output for supported DataObjects.
 * The controller is called by using the URL rewrite rule
 * silvercart-print/$DataObjectName/$DataObjectID
 * and requires the methods printDataObject() and CanView() on the given
 * DataObject.
 *
 * @package SilverCart
 * @subpackage Printer
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @copyright 2013 pixeltricks GmbH
 * @since 19.04.2012
 * @license see license file in modules root directory
 */
class PrinterController extends PageController {

    /**
     * Executes the print controllers logic
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.04.2012
     */
    protected function init() {
        parent::init();
        $request        = $this->getRequest();
        $params         = $request->allParams();
        $dataObjectName = str_replace('-', '\\', $params['DataObjectName']);
        $dataObjectID   = $params['DataObjectID'];
        
        if (strpos($request->getVar('url'), 'silvercart-print-many') !== false) {
            $dataObjectIDs  = explode('-', $dataObjectID);
            $output         = Printer::getPrintManyOutput($dataObjectName, $dataObjectIDs);
            if (!empty($output)) {
                print $output;
                exit();
            }
            $this->redirect(Director::baseURL());
        } else {
            $dataObject = DataObject::get_by_id($dataObjectName, $dataObjectID);
            if ($dataObject &&
                $dataObject->canView()) {
                if ($dataObject->hasMethod('printDataObject')) {
                    print $dataObject->printDataObject();
                } else {
                    if (strpos($request->getVar('url'), 'silvercart-print-inline') === false) {
                        $output = Printer::getPrintOutput($dataObject);
                    } else {
                        $output = Printer::getPrintInlineOutput($dataObject);
                    }
                    print $output;
                }
                exit();
            } else {
                $this->redirect(Director::baseURL());
            }
        }
    }
    
}
