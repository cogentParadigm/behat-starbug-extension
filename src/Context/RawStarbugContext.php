<?php
namespace Starbug\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Psr\Container\ContainerInterface;
use Faker\Factory;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Starbug\Core\DatabaseInterface;

class RawStarbugContext extends RawMinkContext implements StarbugAwareContext {
  public function __construct() {
    $this->faker = Factory::create();
  }
  public function login($user) {
    $this->mink->visit("/login");
    $this->mink->fillField("email", $user["email"]);
    $this->mink->fillField("password", $user["password"]);
    $this->mink->pressButton("Login");
  }
  public function setStarbugContainer(ContainerInterface $container) {
    $this->db = $container->get(DatabaseInterface::class);
    $this->macro = $container->get("Starbug\Core\MacroInterface");
  }
  /**
   * Replace fake data tokens.
   *
   * @Transform /^(.*)$/
   */
  public function replaceTokens($text) {
    $tokens = $this->macro->search($text);
    if (empty($tokens)) {
      return $text;
    }

    static $last = [];
    $replacements = [];
    foreach ($tokens as $type => $typeTokens) {
      foreach ($typeTokens as $name => $token) {
        if ($type == "any") {
          $value = $this->faker->{$name};
          $replacements[$token] = $value;
          $last[$name] = $value;
        } elseif ($type == "last") {
          $replacements[$token] = $last[$name];
        } elseif ($type == "date") {
          $replacements[$token] = date($name);
        } elseif ($type == "mink") {
          $replacements[$token] = $this->getMinkParameter($name);
        }
      }
    }

    $search = array_keys($replacements);
    $replace = array_values($replacements);
    return str_replace($search, $replace, $text);
  }
  /**
   * Replace fake data tokens in tables.
   *
   * @Transform table:*
   */
  public function replaceTableTokens(TableNode $input) {
    $table = $input->getTable();
    foreach ($table as $row => $values) {
      foreach ($values as $idx => $value) {
        $table[$row][$idx] = $this->replaceTokens($value);
      }
    }
    return new TableNode($table);
  }
  /**
   * Access other contexts.
   *
   * @BeforeScenario
   */
  public function gatherContexts(BeforeScenarioScope $scope) {
      $environment = $scope->getEnvironment();
      $this->mink = $environment->getContext('Starbug\Behat\Context\MinkContext');
  }
}
