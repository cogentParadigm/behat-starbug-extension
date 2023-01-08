<?php
namespace Starbug\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use Psr\Container\ContainerInterface;
use Starbug\Behat\Fixture\StarbugImporterApplicator;

class DatabaseContext extends RawStarbugContext {
  protected $fixtures;
  public function __construct($fixtures = []) {
    $this->fixtures = $fixtures;
    parent::__construct();
  }

  public function setStarbugContainer(ContainerInterface $container) {
    parent::setStarbugContainer($container);
    $this->applicator = $container->get(StarbugImporterApplicator::class);
  }
  /**
   * Clean database before scenarios tagged with @database.
   *
   * @BeforeScenario @database
   */
  public function beforeDatabaseScenario(BeforeScenarioScope $scope) {
    $this->cleanDatabase();
    $this->cleanErrors();
  }

  /**
   * Cleans Database
   *
   * @Given a clean database
   */
  public function cleanDatabase() {
    foreach ($this->fixtures as $fixture) {
      $this->applyFixture($fixture);
    }
  }

  /**
   * Clean errors.
   *
   * @Given a clean error state
   */
  public function cleanErrors() {
    $this->db->errors->set([]);
  }

  /**
   * Creates an entity with specified fields
   *
   * @Given there is a/an :entity record with:
   * @Given there is a/an :entity entity with:
   */
  public function createEntity($entity, TableNode $fields) {
    $this->db->store($entity, $fields->getRowsHash());
  }

  /**
   * Assert record exists in database.
   *
   * @Then there should be a/an :entity record with:
   * @Then there should be a/an :entity entity with:
   */
  public function assertRecordExists($entity, TableNode $expected) {
    $record = $this->db->query($entity)->conditions($expected->getRowsHash())->one();
    Assert::assertNotEmpty($record);
  }

  /**
   * Assert a logged error message containing a specified value
   *
   * @Then there should be a/an :entity record with a/an :field containing :value
   */
  public function assertFieldValueContains($entity, $field, $value) {
    $record = $this->db->query($entity)->where($field . " LIKE \"%" . $value . "%\"")->one();
    Assert::assertNotEmpty($record);
  }

  /**
   * Assert record does not exist in database.
   *
   * @Then there should not be a/an :entity record with:
   * @Then there should not be a/an :entity entity with:
   */
  public function assertRecordNotExists($entity, TableNode $expected) {
    $record = $this->db->query($entity)->conditions($expected->getRowsHash())->one();
    Assert::assertEmpty($record);
  }

  /**
   * Populates a fixture into the database.
   *
   * @Given I have the fixture :fixture
   */
  public function applyFixture($fixture) {
    $this->applicator->applyDataSet($this->applicator->createYmlDataSet($fixture));
  }

  /**
   * Populates a fixture into the database.
   *
   * @Given I have the fixtures:
   */
  public function applyFixtures(TableNode $fixtures) {
    $fixtures = $fixtures->getColumn(0);
    foreach ($fixtures as $fixture) {
      $this->applyFixture($fixture);
    }
  }
}
