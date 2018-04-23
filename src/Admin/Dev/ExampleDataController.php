<?php

namespace SilverCart\Admin\Dev;

use SilverCart\Admin\Dev\ExampleData;
use SilverStripe\Control\Controller;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\i18n\i18n;
use Translatable;

/**
 * Provides example data for documentation or example display purposes.
 *
 * @package SilverCart
 * @subpackage Admin_Dev
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 17.04.2018
 * @copyright 2018 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class ExampleDataController extends Controller {
    
    /**
     * Allowed actions.
     *
     * @var array
     */
    private static $allowed_actions = [
        'renderemail',
    ];
    
    /**
     * Action to render an example email.
     * 
     * @param HTTPRequest $request Request
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 17.04.2018
     */
    public function renderemail(HTTPRequest $request) {
        i18n::config()->update('default_locale', Translatable::get_current_locale());
        i18n::set_locale(Translatable::get_current_locale());
        $templateName = $request->param('ID');
        print ExampleData::render_example_email($templateName);
        exit();
    }
}