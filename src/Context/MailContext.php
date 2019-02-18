<?php
namespace Starbug\Behat\Context;

use Alex\MailCatcher\Behat\MailCatcherContext;
use Alex\MailCatcher\Message;
use Exception;

class MailContext extends MailCatcherContext {

  /**
   * Check for email.
   *
   * @Then :subject should be emailed to :address
   *
   * @throws Exception When no matching message is found.
   */
  public function assertEmail($subject, $address) {
    $message = $this->getMailCatcherClient()->searchOne([
      Message::SUBJECT_CRITERIA => $subject,
      Message::TO_CRITERIA => $address
    ]);
    if (is_null($message)) {
      throw new Exception("Message not found.");
    }
  }
}
