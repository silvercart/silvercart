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
     * Alternative text "displayed"
     * 
     * @var string
     */
    protected $displayedText = '';
    /**
     * Alternative text "not displayed"
     * 
     * @var string
     */
    protected $notDisplayedText = '';

    /**
     * Set the composite's title to that of the first child
     *
     * @param string $name      Field name
     * @param string $title     Field title
     * @param string $showTitle Show field title
     * 
     * @return void
     */
    public function __construct(string $name, string $title = null, string $showTitle = null)
    {
        if (!$title) {
            $title = _t(self::class . '.TitleLabel', 'Title (displayed if checked)');
        }
        if (!$showTitle) {
            $showTitle = _t(self::class . '.ShowTitleLabel', 'Displayed');
        }
        $fields = [
            TextField::create($name, $title),
            CheckboxField::create("Show{$name}", $showTitle)
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

        $displayedText    = $this->getDisplayedText();
        $notDisplayedText = $this->getNotDisplayedText();

        $showField = $field->fieldByName("Show{$this->getName()}");
        if ($showField !== null) {
            $field->replaceField("Show{$this->getName()}", LiteralField::create(
                "Show{$this->getName()}",
                $field->fieldByName("Show{$this->getName()}")->Value() === 'Yes' ? $displayedText : $notDisplayedText
            )->addExtraClass('show-title'));
        }

        return $field;
    }
    
    /**
     * Returns the alternative text "displayed"
     * 
     * @return string
     */
    public function getDisplayedText(): string
    {
        if (empty($this->displayedText)) {
            $this->setDisplayedText(_t(self::class . '.Displayed', 'Displayed'));
        }
        return $this->displayedText;
    }

    /**
     * Returns the alternative text "not displayed"
     * 
     * @return string
     */
    public function getNotDisplayedText(): string
    {
        if (empty($this->notDisplayedText)) {
            $this->setNotDisplayedText(_t(self::class . '.NotDisplayed', 'Not displayed'));
        }
        return $this->notDisplayedText;
    }

    /**
     * Sets the alternative text "displayed"
     * 
     * @param string $displayedText Alternative text
     * 
     * @return $this
     */
    public function setDisplayedText(string $displayedText) : TextCheckboxGroupField
    {
        $this->displayedText = $displayedText;
        return $this;
    }

    /**
     * Sets the alternative text "not displayed"
     * 
     * @param string $notDisplayedText Alternative text
     * 
     * @return $this
     */
    public function setNotDisplayedText(string $notDisplayedText) : TextCheckboxGroupField
    {
        $this->notDisplayedText = $notDisplayedText;
        return $this;
    }
}