<?php
namespace Starbug\Behat\Context;

use Behat\Gherkin\Node\TableNode;

/**
 * Provides pre-built step definitions for interacting with Starbug.
 */
class StarbugContext extends RawStarbugContext {
  /**
   * Logout
   *
   * @Given I am an anonymous user
   * @Given I am not logged in
   *
   * @When I log out
   */
  public function logout() {
    $this->mink->visit("/logout");
  }

  /**
   * Creates and authenticates a user with the given role(s).
   *
   * @Given I am logged in as a/an :role
   * @Given I am logged in as a user with the :role role(s)
   *
   * @When I login as a/an :role
   */
  public function loginAs($group) {
    $user = [
      "first_name" => $this->faker->firstName,
      "last_name" => $this->faker->lastName,
      "email" => $this->faker->email,
      "password" => $this->faker->password,
      "groups" => $group . ",user"
    ];
    $this->models->get("users")->store($user);
    $this->login($user);
  }

  /**
   * Creates and authenticates a user with the given fields.
   * | first_name | Malcom |
   * | last_name | Shabazz |
   *
   * @Given I am logged in as:
   */
  public function loginWith(TableNode $fields) {
    $user = [
      "first_name" => $this->faker->firstName,
      "last_name" => $this->faker->lastName,
      "email" => $this->faker->email,
      "password" => $this->faker->password,
      "groups" => ""
    ];
    foreach ($fields->getRowsHash() as $field => $value) {
      $user[$field] = $value;
    }
    $user["groups"] .= (empty($user["groups"]) ? "" : ",") . "user";
    $this->models->get("users")->store($user);
    $this->login($user);
  }



  /**
   * Check for a data grid on the page.
   *
   * @Then I should see a :arg1 grid
   */
  public function assertGrid($arg1) {
    $this->mink->assertElementOnPage("table#".$arg1."_grid");
  }

  /**
   * @Then I should see the error :arg1 for :arg2
   */
  public function assertError($arg1, $arg2) {
    $this->mink->assertElementContainsText("[class*=-".$arg2."] .alert", $arg1);
  }

  /**
   * Assert pressence of multiple form errors
   * Example: Then I should see the following errors:"
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   * Example: And I should see the following errors:"
   *              | username | bruceWayne |
   *              | password | iLoveBats123 |
   *
   * @Then /^(?:|I )should see the following errors:$/
   */
  public function assertErrors(TableNode $fields) {
    foreach ($fields->getRowsHash() as $field => $value) {
      $this->assertError($value, $field);
    }
  }
}
