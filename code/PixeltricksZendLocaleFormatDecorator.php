<?php
/**
 * Bugfix for the Zend Locale Format class.
 *
 * Altered line 141: substr => iconv_substr
 * Altered line 153: and ($rest != '¤')
 *
 * @package teleapotheke
 * @author Sascha Koehler <skoehler@pixeltricks.de>
 * @copyright 2011 pixeltricks GmbH
 * @since 02.02.2011
 * @license none
 */
class PixeltricksZendLocaleFormatDecorator extends Zend_Locale_Format {

    /**
     * Contains the decorated class.
     *
     * @var Zend_Locale_Format
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    protected $cZend_Locale_Format;

    /**
     * Constructor.
     *
     * @param Zend_Locale_Format $cZend_Locale_Format the original class
     *
     * @return void
     *
     * @author Sascha Koehler <skoehler@pixeltricks.de>
     * @copyright 2011 pixeltricks GmbH
     * @since 02.02.2011
     */
    function __construct(Zend_Locale_Format $cZend_Locale_Format) {
        $this->cZend_Locale_Format = $cZend_Locale_Format;
    }

    /**
     * Returns a locale formatted number depending on the given options.
     * The seperation and fraction sign is used from the set locale.
     * ##0.#  -> 12345.12345 -> 12345.12345
     * ##0.00 -> 12345.12345 -> 12345.12
     * ##,##0.00 -> 12345.12345 -> 12,345.12
     *
     * @param   string  $input    Localized number string
     * @param   array   $options  Options: number_format, locale, precision. See {@link setOptions()} for details.
     * @return  string  locale formatted number
     * @throws Zend_Locale_Exception
     */
    public static function toNumber($value, array $options = array())
    {
        // load class within method for speed
        require_once 'Zend/Locale/Math.php';

        $value             = Zend_Locale_Math::normalize($value);
        $options           = self::_checkOptions($options) + self::$_options;
        $options['locale'] = (string) $options['locale'];

        // Get correct signs for this locale
        $symbols = Zend_Locale_Data::getList($options['locale'], 'symbols');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        // Get format
        $format = $options['number_format'];
        if ($format === null) {
            $format  = Zend_Locale_Data::getContent($options['locale'], 'decimalnumber');
            if (iconv_strpos($format, ';') !== false) {
                if (call_user_func(Zend_Locale_Math::$comp, $value, 0, $options['precision']) < 0) {
                    $format = iconv_substr($format, iconv_strpos($format, ';') + 1);
                } else {
                    $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
                }
            }
        } else {
            // seperate negative format pattern when available
            if (iconv_strpos($format, ';') !== false) {
                if (call_user_func(Zend_Locale_Math::$comp, $value, 0, $options['precision']) < 0) {
                    $format = iconv_substr($format, iconv_strpos($format, ';') + 1);
                } else {
                    $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
                }
            }

            if (strpos($format, '.')) {
                if (is_numeric($options['precision'])) {
                    $value = Zend_Locale_Math::round($value, $options['precision']);
                } else {
                    if (substr($format, strpos($format, '.') + 1, 3) == '###') {
                        $options['precision'] = null;
                    } else {
                        $options['precision'] = strlen(substr($format, strpos($format, '.') + 1,
                                                              strrpos($format, '0') - strpos($format, '.')));
                        $format = substr($format, 0, strpos($format, '.') + 1) . '###'
                                . substr($format, strrpos($format, '0') + 1);
                    }
                }
            } else {
                $value = Zend_Locale_Math::round($value, 0);
                $options['precision'] = 0;
            }
            $value = Zend_Locale_Math::normalize($value);
        }

        if (strpos($format, '0') === false) {
            require_once 'Zend/Locale/Exception.php';
            throw new Zend_Locale_Exception('Wrong format... missing 0');
        }

        // get number parts
        $pos = iconv_strpos($value, '.');
        if ($pos !== false) {
            if ($options['precision'] === null) {
                $precstr = iconv_substr($value, $pos + 1);
            } else {
                $precstr = iconv_substr($value, $pos + 1, $options['precision']);
                if (iconv_strlen($precstr) < $options['precision']) {
                    $precstr = $precstr . str_pad("0", ($options['precision'] - iconv_strlen($precstr)), "0");
                }
            }
        } else {
            if ($options['precision'] > 0) {
                $precstr = str_pad("0", ($options['precision']), "0");
            }
        }

        if ($options['precision'] === null) {
            if (isset($precstr)) {
                $options['precision'] = iconv_strlen($precstr);
            } else {
                $options['precision'] = 0;
            }
        }

        // get fraction and format lengths
        if (strpos($value, '.') !== false) {
            $number = substr((string) $value, 0, strpos($value, '.'));
        } else {
            $number = $value;
        }

        $prec = call_user_func(Zend_Locale_Math::$sub, $value, $number, $options['precision']);
        $prec = Zend_Locale_Math::normalize($prec);
        if (iconv_strpos($prec, '-') !== false) {
            $prec = iconv_substr($prec, 1);
        }

        if (($prec == 0) and ($options['precision'] > 0)) {
            $prec = "0.0";
        }

        if (($options['precision'] + 2) > iconv_strlen($prec)) {
            $prec = str_pad((string) $prec, $options['precision'] + 2, "0", STR_PAD_RIGHT);
        }

        if (iconv_strpos($number, '-') !== false) {
            $number = iconv_substr($number, 1);
        }
        $group  = iconv_strrpos($format, ',');
        $group2 = iconv_strpos ($format, ',');
        $point  = iconv_strpos ($format, '0');
        // Add fraction
        $rest = "";
        if (($value < 0) && (strpos($format, '.'))) {
            $rest   = iconv_substr(iconv_substr($format, strpos($format, '.') + 1), -1, 1);
        }

        if ($options['precision'] == '0') {
            $format = iconv_substr($format, 0, $point) . iconv_substr($format, iconv_strrpos($format, '#') + 2);
        } else {
            $format = iconv_substr($format, 0, $point) . $symbols['decimal']
                               . iconv_substr($prec, 2)
                               . iconv_substr($format, iconv_strrpos($format, '#') + 1 + strlen($prec));
        }

        if (($value < 0) and ($rest != '0') and ($rest != '#') and ($rest != '¤')) {
            $format .= $rest;
        }

        // Add seperation
        if ($group == 0) {
            // no seperation
            $format = $number . iconv_substr($format, $point);
        } else if ($group == $group2) {
            // only 1 seperation
            $seperation = ($point - $group);
            for ($x = iconv_strlen($number); $x > $seperation; $x -= $seperation) {
                if (iconv_substr($number, 0, $x - $seperation) !== "") {
                    $number = iconv_substr($number, 0, $x - $seperation) . $symbols['group']
                            . iconv_substr($number, $x - $seperation);
                }
            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
        } else {

            // 2 seperations
            if (iconv_strlen($number) > ($point - $group)) {
                $seperation = ($point - $group);
                $number = iconv_substr($number, 0, iconv_strlen($number) - $seperation) . $symbols['group']
                        . iconv_substr($number, iconv_strlen($number) - $seperation);

                if ((iconv_strlen($number) - 1) > ($point - $group + 1)) {
                    $seperation2 = ($group - $group2 - 1);
                    for ($x = iconv_strlen($number) - $seperation2 - 2; $x > $seperation2; $x -= $seperation2) {
                        $number = iconv_substr($number, 0, $x - $seperation2) . $symbols['group']
                                . iconv_substr($number, $x - $seperation2);
                    }
                }

            }
            $format = iconv_substr($format, 0, iconv_strpos($format, '#')) . $number . iconv_substr($format, $point);
        }
        // set negative sign
        if (call_user_func(Zend_Locale_Math::$comp, $value, 0, $options['precision']) < 0) {
            if (iconv_strpos($format, '-') === false) {
                $format = $symbols['minus'] . $format;
            } else {
                $format = str_replace('-', $symbols['minus'], $format);
            }
        }

        return (string) $format;
    }
}
