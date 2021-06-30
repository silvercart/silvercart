<?php

namespace SilverCart\Forms\FormFields;

use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;

/**
 * Field group to show a text field with the display configuration field.
 * 
 * @package SilverCart
 * @subpackage Forms\FormFields
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 19.03.2019
 * @copyright 2019 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class TextCheckboxGroupField extends CompositeField
{
    /**
     * Schema component.
     *
     * @var string
     */
    protected $schemaComponent = 'TextCheckboxGroupField';

    /**
     * Set the composite's title to that of the first child
     *
     * @param string $name  Field name
     * @param string $title Field title
     * 
     * @return void
     * 
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 19.03.2019
     */
    public function __construct(string $name, string $title = null)
    {
        if (!$title) {
            $title = _t(self::class . '.TitleLabel', 'Title (displayed if checked)');
        }
        $fields = [
            TextField::create($name, $title),
            CheckboxField::create("Show{$name}", _t(self::class . '.ShowTitleLabel', 'Displayed'))
        ];
        parent::__construct($fields);
        $this->setName($name);
        $this->setTitle($title);
    }

    /**
     * Don't use the custom template for readonly states
     *
     * {@inheritDoc}
     */
    public function performReadonlyTransformation() : TextCheckboxGroupField
    {
        $field = parent::performReadonlyTransformation();

        $field->setTemplate(CompositeField::class);
        $field->setTitle($this->Title());

        $field->replaceField($this->getName(), LiteralField::create(
            $this->getName(),
            $field->fieldByName($this->getName())->Value()
        ));

        $displayedText    = _t(self::class . '.Displayed', 'Displayed');
        $notDisplayedText = _t(self::class . '.NotDisplayed', 'Not displayed');

        $showField = $field->fieldByName("Show{$this->getName()}");
        if ($showField !== null) {
            $field->replaceField("Show{$this->getName()}", LiteralField::create(
                "Show{$this->getName()}",
                $field->fieldByName("Show{$this->getName()}")->Value() === 'Yes' ? $displayedText : $notDisplayedText
            )->addExtraClass('show-title'));
        }

        return $field;
    }
}