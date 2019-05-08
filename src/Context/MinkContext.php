<?php
namespace Starbug\Behat\Context;

use Behat\MinkExtension\Context\MinkContext as ParentContext;
use Behat\Gherkin\Node\TableNode;

/**
 * Extensions to the Mink Extension.
 */
class MinkContext extends ParentContext {
  /**
   * Clicks link with specified id|title|alt|text
   * Example: And I click "Log In"
   *
   * @When /^(?:|I )click "(?P<link>(?:[^"]|\\")*)"$/
   */
  public function clickLink($link) {
    $link = $this->fixStepArgument($link);
    $this->getContext()->clickLink($link);
  }
  /**
   * Override to use context.
   */
  public function pressButton($button) {
    $button = $this->fixStepArgument($button);
    $this->getContext()->pressButton($button);
  }
  /**
   * Override to use context.
   */
  public function fillField($field, $value) {
    $field = $this->fixStepArgument($field);
    $value = $this->fixStepArgument($value);
    $this->getContext()->fillField($field, $value);
  }
  /**
   * Fills in form field with specified id|name|label|value
   * Example: When I enter "bwayne" for "username"
   *
   * @When /^(?:|I )enter "(?P<value>(?:[^"]|\\")*)" for "(?P<field>(?:[^"]|\\")*)"$/
   */
  public function enterField($field, $value) {
    $this->fillField($field, $value);
  }
  /**
   * Fills in form fields with provided table
   * Example: When I enter the following"
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   * Example: And I enter the following"
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   *
   * @When /^(?:|I )enter the following:$/
   */
  public function enterFields(TableNode $fields) {
    $this->fillFields($fields);
  }
  /**
   * Checks, that current page response status is equal to specified
   * Example: Then I should get a 200 HTTP response
   * Example: And I should get a 404 HTTP response
   *
   * @Then I should get a :code HTTP response
   */
  public function assertHttpResponse($code) {
    $this->assertResponseStatus($code);
  }
  /**
   * Checks, that current page response status is not equal to specified
   * Example: Then I should not get a 200 HTTP response
   * Example: And I should not get a 404 HTTP response
   *
   * @Then I should not get a :code HTTP response
   */
  public function assertNotHttpResponse($code) {
    $this->assertResponseStatusIsNot($code);
  }

  /**
   * Wait for a dialog to open.
   *
   * @When a dialog is opened
   */
  public function waitForDialog() {
    $this->getSession()->wait(5000, "function() {var elem = document.querySelector(\"[role=dialog]\"); return !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length ); }");
    $this->context = $this->getContext()->find("css", "[role=dialog]");
  }

  /**
   * Assert dialog has closed.
   *
   * @Then the dialog will close
   */
  public function assertDialogClosed() {
    $this->getSession()->wait(5000, "function() {var elem = document.querySelector(\"[role=dialog]\"); return !( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length ); }");
    Assert::assertFalse($this->context->isVisible());
    $this->context = null;
  }

  /**
   * Wait for dialog reload.
   *
   * @Then the dialog will reload
   */
  public function assertDialogReloaded() {
    $this->getContext()->waitFor(5, function ($node) {
      return ($node->find("css", "form.submitted") || $node->find("css", "form.errors"));
    });
    Assert::assertTrue(($this->getContext()->find("css", "form.submitted") || $this->getContext()->find("css", "form.errors")));
  }

  /**
   * Set a context for element traversal.
   *
   * @When I focus on :element
   */
  public function setContext($locator, $type = "named") {
    $this->context = $this->getSession()->getPage()->find($type, $locator);
  }

  /**
   * Get the current context.
   *
   * @return Behat\Mink\Element\NodeElement The node or document.
   */
  public function getContext() {
    return is_null($this->context) ? $this->getSession()->getPage() : $this->context;
  }
}
