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
    parent::clickLink($link);
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
}
