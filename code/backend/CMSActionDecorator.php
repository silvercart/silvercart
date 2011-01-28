<?php

/**
 * adds new functionallity to LeftAndMain
 *
 * @author Roland Lehmann <rlehmann@pixeltricks.de>
 * @copyright Pixeltricks GmbH
 * @since 14.11.2010
 * @license BSD
 */
class CMSActionDecorator extends LeftAndMainDecorator {

    /**
     * form action for a new button to create a newsletter from a blog entry
     *
     * @author Roland Lehmann <rlehmann@pixeltricks.de>
     * @since 14.11.10
     * @return void
     */
    public function sendEntryAsNewsletter() {
        //Get´s the id of the class that called this decorator.
        $id = (int) $_REQUEST['ID'];
        //Get the instance of that class
        $blogEntry = DataObject::get_by_id('BlogEntry', $id);

        if ($blogEntry) {
            $newsletterSubscribers = DataObject::get('Member', "`SubscribedToNewsletter` = true");
            foreach ($newsletterSubscribers as $subscriber) {
                if ($subscriber->Email) {
                    $email = new Email("rlehmann@pixeltricks.de", $subscriber->Email, $blogEntry->Title, '');
                    $email->setTemplate('MailNewsletter');
                    $email->populateTemplate(
                            array(
                                'Subscriber' => $subscriber,
                                'BlogEntry' => $blogEntry
                            )
                    );
                    $email->send();
                }
            }
        }
        FormResponse::status_message(sprintf('Newsletter verschickt'), 'good');
        return FormResponse::respond();
    }

}

