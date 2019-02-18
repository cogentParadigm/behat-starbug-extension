<?php
namespace Starbug\Behat\Context;

use tPayne\BehatMailExtension\Context\MailAwareContext;
use tPayne\BehatMailExtension\Context\MailTrait;
use PHPUnit\Framework\Assert;

class MailContext implements MailAwareContext {
  use MailTrait;

  /**
   * Check for email.
   *
   * @Then :subject should be emailed to :address
   */
  public function assertEmail($subject, $address) {
    $message = array_pop($this->mail->getMessages());
    Assert::assertEquals($subject, $message->subject());
    Assert::assertEquals($address, $message->to());
  }
}
