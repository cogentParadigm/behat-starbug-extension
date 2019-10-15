<?php
namespace Starbug\Behat\Context;

use Behat\MinkExtension\Context\MinkContext as ParentContext;
use Interop\Container\ContainerInterface;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;

/**
 * Extensions to the Mink Extension.
 */
class MinkContext extends ParentContext implements StarbugAwareContext {
  protected $context = false;
  protected $contextHistory = [];

  public function setStarbugContainer(ContainerInterface $container) {
    $this->setMinkParameter("files_path", $container->get("base_directory"));
  }
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
   * Set context for element traversal.
   *
   * @param NodeElement|null $context The new context.
   *
   * @return void
   */
  public function setContext(?NodeElement $context) {
    $this->contextHistory[] = $this->context = $context;
  }

  /**
   * Exits the current context and returns to the previous one.
   *
   * @return void
   */
  public function popContext() {
    array_pop($this->contextHistory);
    $this->context = end($this->contextHistory);
  }

  /**
   * Get the current context.
   *
   * @return Behat\Mink\Element\NodeElement The node or document.
   */
  public function getContext() {
    return (false == $this->context) ? $this->getSession()->getPage() : $this->context;
  }
}
