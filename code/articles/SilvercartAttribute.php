<?php
/**
 * abstract for an articles attributes
 * Articles of the same group share the same attibutes
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @license BSD
 * @since 23.10.2010
 * @copyright Pixeltricks GmbH
 */
class SilvercartAttribute extends DataObject {
    public static $db = array(
        'Title' => 'VarChar'
    );
    public static $belongs_many_many = array(
        'SilvercartArticleGroups' => 'SilvercartArticleGroupPage'
    );
}
