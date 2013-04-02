<?php
/**
 * Copyright 2013 pixeltricks GmbH
 *
 * This file is part of SilverCart.
 *
 * Dutch (NL) language pack
 *
 * @package Silvercart
 * @subpackage i18n
 * @ignore
 */
__MODULE_INCLUDES__

global $lang;

if (array_key_exists('__LOCALE__', $lang) && is_array($lang['__LOCALE__'])) {
    $lang['__LOCALE__'] = array_merge($lang['en_US'], $lang['__LOCALE__']);
} else {
    $lang['__LOCALE__'] = $lang['en_US'];
}

