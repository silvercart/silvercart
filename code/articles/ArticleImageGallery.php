<?php
/**
 * An articles has one image gallery
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 * @copyright Pixeltricks GmbH
 */
class ArticleImageGallery extends DataObject {
    static $singular_name = "gallery";
    static $plural_name = "galleries";
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $has_many = array(
        'articles' => 'Article',
        'pictures' => 'Image'
    );
}
