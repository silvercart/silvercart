<?php

/**
 * adds
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 08.02.2011
 * @license BSD
 */
class PageDecorator extends DataObjectDecorator {

    /**
     * extends statics
     *
     * @return array configuration array
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.11
     */
    public function  extraStatics() {
        return array(
            'has_one' =>array(
                'headerPicture' => 'Image'
            )
        );
    }

    /**
     * extends getCMSFields()
     *
     * @param FieldSet &$fields field set
     *
     * @return FieldSet cms fieldset of decorated class
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 08.02.2011
     */
    public function  updateCMSFields(FieldSet &$fields) {
        $fields->addFieldToTab('Root.Content.Main', new FileIFrameField('headerPicture', _t('Page.HEADERPICTURE', 'header picture')));
        return $fields;
    }
}

