<?php

namespace SilverCart\Forms;

use SilverCart\Dev\Tools;
use SilverCart\Forms\CustomForm;
use SilverCart\Forms\FormFields\TextField;
use SilverCart\Model\Pages\Page;
use SilverCart\Model\Customer\Customer;
use SilverCart\Model\Newsletter\Newsletter;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Security\Member;

/**
 * The form for subscribing to or unsubscribing from the newsletter.
 *
 * @package SilverCart
 * @subpackage Forms
 * @author Sebastian Diel <sdiel@pixeltricks.de>
 * @since 26.09.2017
 * @copyright 2017 pixeltricks GmbH
 * @license see license file in modules root directory
 */
class NewsletterForm extends CustomForm {
    
    /**
     * Custom extra CSS classes.
     *
     * @var array
     */
    protected $customExtraClasses = [
        'form-horizontal',
        'grouped',
    ];
    
    /**
     * Don't enable Security token for this type of form because we'll run
     * into caching problems when using it.
     * 
     * @var boolean
     */
    protected $securityTokenEnabled = false;
    
    /**
     * List of required fields.
     *
     * @var array
     */
    private static $requiredFields = [
        'Salutation',
        'FirstName' => [
            'isFilledIn'    => true,
            'hasMinLength'  => 3,
        ],
        'Surname' => [
            'isFilledIn'    => true,
            'hasMinLength'  => 3,
        ],
        'Email' => [
            'isFilledIn'     => true,
            'isEmailAddress' => true,
        ],
        'NewsletterAction',
    ];

    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomFields() {
        $this->beforeUpdateCustomFields(function (array &$fields) {
            $member  = Customer::currentRegisteredCustomer();
            $newsletterActionSource = [
                '1' => $this->fieldLabel('ACTIONFIELD_SUBSCRIBE'),
                '2' => $this->fieldLabel('ACTIONFIELD_UNSUBSCRIBE')
            ];
            if ($member instanceof Member
             && $member->exists()
            ) {
                if ($member->SubscribedToNewsletter) {
                    array_shift($newsletterActionSource);
                    $newsletterActionTitle = Newsletter::singleton()->fieldLabel('YouAreSubscribed') . ' - ' . $this->fieldLabel('ACTIONFIELD_TITLE');
                } else {
                    array_pop($newsletterActionSource);
                    $newsletterActionTitle = Newsletter::singleton()->fieldLabel('YouAreUnsubscribed') . ' - ' . $this->fieldLabel('ACTIONFIELD_TITLE');
                }
                $fields += [
                    ReadonlyField::create('Salutation', $member->fieldLabel('Salutation'), $member->SalutationText),
                    ReadonlyField::create('FirstName', $member->fieldLabel('FirstName'), $member->FirstName),
                    ReadonlyField::create('Surname', $member->fieldLabel('Surname'), $member->Surname),
                    ReadonlyField::create('Email', $member->fieldLabel('EmailAddress'), $member->Email),
                    OptionsetField::create('NewsletterAction', $newsletterActionTitle, $newsletterActionSource),
                ];
            } else {
                $member  = Member::singleton();
                $fields += [
                    DropdownField::create('Salutation', $member->fieldLabel('Salutation'), Tools::getSalutationMap()),
                    TextField::create('FirstName', $member->fieldLabel('FirstName')),
                    TextField::create('Surname', $member->fieldLabel('Surname')),
                    EmailField::create('Email', $member->fieldLabel('EmailAddress')),
                    OptionsetField::create('NewsletterAction', $this->fieldLabel('ACTIONFIELD_TITLE'), $newsletterActionSource),
                ];
            }
        });
        return parent::getCustomFields();
    }
    
    /**
     * Returns the static form fields.
     * 
     * @return array
     */
    public function getCustomActions() {
        $this->beforeUpdateCustomActions(function (array &$actions) {
            $actions += [
                FormAction::create('submit', Page::singleton()->fieldLabel('Submit'))
                    ->setUseButtonTag(true)->addExtraClass('btn-primary')
            ];
        });
        return parent::getCustomActions();
    }
    
    /**
     * Submits the form.
     * 
     * @param array      $data Submitted data
     * @param CustomForm $form Form
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 08.11.2017
     */
    public function doSubmit($data, CustomForm $form) {
        $customer = Customer::currentRegisteredCustomer();

        if ($customer instanceof Member &&
            $customer->exists()) {
            $success = $this->handleExistingCustomer($customer, $data['NewsletterAction']);
        } else {
            $success = $this->handleAnonymousCustomer($data);
        }
        
        if ($success) {
            $this->getController()->redirect($this->getController()->Link('thanks'));
        } else {
            $this->getController()->redirectBack();
        }
    }
    
    /**
     * Handles the newsletter subscription for existing customers.
     * For registered and logged in customers all we have to do is set the respective 
     * field in the customer object.
     * 
     * @param Member $customer         The customer
     * @param string $newsletterAction The action to do ('1' or '2')
     * 
     * @return bool
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.11.2017
     */
    protected function handleExistingCustomer($customer, $newsletterAction) {
        $success = true;
        switch ($newsletterAction) {
            case '1':
                Newsletter::subscribeRegisteredCustomer($customer, $this->getController()->UseDoubleOptIn);
                $this->setSuccessMessage(
                    _t(Newsletter::class . '.StatusSUBSCRIBED_SUCCESSFULLY',
                        'The email address "{email}" was subscribed successfully.',
                        [
                            'email' => $customer->Email,
                        ]
                    )
                );
                break;
            case '2':
            default:
                Newsletter::unSubscribeRegisteredCustomer($customer);
                $this->setSuccessMessage(
                    _t(Newsletter::class . '.StatusUNSUBSCRIBED_SUCCESSFULLY',
                        'The email address "{email}" was unsubscribed successfully.',
                        [
                            'email' => $customer->Email,
                        ]
                    )
                );
        }
        return $success;
    }
    
    /**
     * Handles the newsletter subscription for anonymous customers.
     * For unregistered customers we have to add / remove them from the datastore for 
     * unregistered newsletter recipients. If the given email address belongs to a 
     * registered customer we should not do anything but ask the user to log in first.
     * 
     * @param array  $data             Submitted data
     * @param string $newsletterAction The action to do ('1' or '2')
     * 
     * @return void
     *
     * @author Sebastian Diel <sdiel@pixeltricks.de>
     * @since 14.11.2017
     */
    protected function handleAnonymousCustomer($data) {
        $success = false;
        if (Newsletter::isEmailAllocatedByRegularCustomer($data['Email'])) {
            $this->setErrorMessage(
                _t(Newsletter::class . '.StatusREGULAR_CUSTOMER_WITH_SAME_EMAIL_EXISTS',
                    'There\'s already a registered customer with the email address "{email}". Please log in first and proceed then with the newsletter preferences: <a href="{link}">Go to the login page</a>.',
                    [
                        'email' => $data['Email'],
                        'link' => '/Security/Login/?BackURL=' . Tools::PageByIdentifierCode(Page::IDENTIFIER_NEWSLETTER_PAGE)->Link()
                    ]
                )
            );
        } else {
            if ($data['NewsletterAction'] == '1') {
                // Subscribe to newsletter. If the user is already subscribed we display a message accordingly.
                if (Newsletter::isEmailAllocatedByAnonymousRecipient($data['Email'])) {
                    $this->setErrorMessage(
                        _t(Newsletter::class . '.StatusALREADY_SUBSCRIBED',
                            'The email address "{email}" is already subscribed.',
                            ['email' => $data['Email'],]
                        )
                    );
                } else {
                    Newsletter::subscribeAnonymousCustomer($data['Salutation'], $data['FirstName'], $data['Surname'], $data['Email'], $this->getController()->UseDoubleOptIn);
                    $this->setSuccessMessage(
                        _t(Newsletter::class . '.StatusSUBSCRIBED_SUCCESSFULLY_FOR_OPT_IN',
                            'An email was sent to the address "{email}" with further instructions for the confirmation.',
                            ['email' => $data['Email'],]
                        )
                    );
                    $success = true;
                }
            } else {
                // Unsubscribe from newsletter. If no email address exists we display a message accordingly.
                if (Newsletter::isEmailAllocatedByAnonymousRecipient($data['Email'])) {
                    Newsletter::unSubscribeAnonymousCustomer($data['Email']);
                    $this->setSuccessMessage(
                        _t(Newsletter::class . '.StatusUNSUBSCRIBED_SUCCESSFULLY',
                            'The email address "{email}" was unsubscribed successfully.',
                            ['email' => $data['Email'],]
                        )
                    );
                    $success = true;
                } else {
                    $this->setErrorMessage(
                        _t(Newsletter::class . '.StatusNO_EMAIL_FOUND',
                            'We could not find the email address "{email}".',
                            ['email' => $data['Email'],]
                        )
                    );
                }
            }
        }
        return $success;
    }
    
}