<?php

/**
 * backend interface to CRUD the defined classes
 * 
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 23.10.2010
 * @license BSD
 */
class ArticleAdmin extends ModelAdmin {
    public static $managed_models = array(
        'Article',
        'Manufacturer',
        'ArticleImageGallery'

    );

    static $url_segment = 'articles';
    static $menu_title  = 'Artikel';
}