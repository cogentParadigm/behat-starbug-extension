<?php
namespace Starbug\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;

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
  public function loginRole($group) {
    $user = [
      "first_name" => $this->faker->firstName,
      "last_name" => $this->faker->lastName,
      "email" => $this->faker->email,
      "password" => $this->faker->password,
      "groups" => $group . ",user"
    ];
    $this->db->store("users", $user);
    $this->login($user);
  }

  /**
   * Creates and authenticates a user with the given fields.
   * | first_name | Malcom |
   * | last_name | Shabazz |
   *
   * @Given I am logged in with:
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
    $this->db->store("users", $user);
    $this->login($user);
  }

  /**
   * Creates and authenticates a user with the given fields.
   * | first_name | Malcom |
   * | last_name | Shabazz |
   *
   * @Given I am logged in as:
   * @When I login as:
   */
  public function loginAs(TableNode $fields) {
    $user = $this->db->query("users")->conditions($fields->getRowsHash())->one();
    $password = $this->faker->password;
    if (!empty($user)) {
      $this->db->store("users", ["id" => $user["id"], "password" => $password]);
    }
    $this->login(["email" => $user["email"], "password" => $password]);
  }

  /**
   * Assert the presence of a form error.
   *
   * @Then I should see the error :arg1 for :arg2
   */
  public function assertError($arg1, $arg2) {
    $this->mink->assertElementContainsText("[class*=-".$arg2."] .alert", $arg1);
  }

  /**
   * Assert presence of multiple form errors.
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

  /**
   * Wait for a dialog to open.
   *
   * @Given a dialog is open
   * @When a dialog is opened
   * @When a dialog opens
   * @Then a dialog will open
   */
  public function waitForDialog() {
    $this->getSession()->wait(30000, "(function() {var elem = document.querySelector(\"div.in[role=dialog] form\"); return elem && !!( elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length ); })()");
    $dialogs = $this->getSession()->getPage()->findAll("css", "[role=dialog]");
    $target = false;
    foreach ($dialogs as $dialog) {
      if ($dialog->hasClass("in")) {
        $target = $dialog;
      }
    }
    if ($target) {
      $this->mink->setContext($target);
    }
  }

  /**
   * Assert dialog has closed.
   *
   * @Given the dialog is closed
   * @When the dialog closes
   * @Then the dialog will close
   */
  public function assertDialogClosed() {
    $this->mink->getContext()->waitFor(30, function ($node) {
      return !$node->isVisible();
    });
    Assert::assertFalse($this->mink->getContext()->isVisible());
    $this->mink->popContext();
  }

  /**
   * Wait for dialog reload.
   *
   * @Given the dialog has reloaded
   * @When the dialog is reloaded
   * @When the dialog reloads
   * @Then the dialog will reload
   */
  public function assertDialogReloaded() {
    $this->mink->getContext()->waitFor(30, function ($node) {
      return ($node->find("css", "form.submitted") || $node->find("css", "form.errors"));
    });
    Assert::assertTrue(($this->mink->getContext()->find("css", "form.submitted") || $this->mink->getContext()->find("css", "form.errors")));
  }
}
