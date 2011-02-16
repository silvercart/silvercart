<?php
/**
 * backend interface to CRUD the defined classes
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license none
 */
class SilvercartArticleAdmin extends ModelAdmin {

    /**
     * Managed models
     *
     * @var array
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $managed_models = array(
        'SilvercartArticle',
        'SilvercartManufacturer',
        'SilvercartArticleImageGallery'
    );

    /**
     * The URL segment
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $url_segment = 'articles';

    /**
     * The menu title
     *
     * @var string
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    public static $menu_title = 'Artikel';

    /**
     * constructor
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @copyright 2010 pixeltricks GmbH
     * @since 02.02.2011
     */
    public function  __construct() {
        self::$menu_title = _t('SilvercartArticle.SINGULARNAME');

        parent::__construct();
    }
}
