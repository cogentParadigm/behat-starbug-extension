<?php
namespace Starbug\Behat\Context;

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use PDO;
use PHPUnit\DbUnit\DataSet\CompositeDataSet;
use PHPUnit\Framework\Assert;
use Psr\Container\ContainerInterface;
use Starbug\Behat\Fixture\DbUnitApplicator;

class DbUnitDatabaseContext extends RawStarbugContext {
  protected $fixtures;
  public function __construct($fixtures = []) {
    $this->fixtures = $fixtures;
    parent::__construct();
  }

  public function setStarbugContainer(ContainerInterface $container) {
    parent::setStarbugContainer($container);
    $config = $container->get("Starbug\Core\ConfigInterface");
    if (!$container->has("behat.fixture_applicator")) {
      if ($container->has("databases.active")) {
        $params = $container->get("databases.active");
      } else {
        $database = $container->has("database_name") ? $container->get("database_name") : $container->get("db");
        $params = $config->get("db/".$database);
      }
      $pdo = new PDO('mysql:host='.$params['host'].';dbname='.$params['db'], $params['username'], $params['password']);
      $applicator = new DbUnitApplicator($pdo, $params["prefix"]);
      $container->set("behat.fixture_applicator", $applicator);
      $container->set("Starbug\Behat\Fixture\DbUnitApplicator", $applicator);
    }
    $this->applicator = $container->get("behat.fixture_applicator");
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
    $dataSet = new CompositeDataSet();
    foreach ($this->fixtures as $fixture) {
      $dataSet->addDataSet($this->applicator->createMySQLXMLDataSet($fixture));
    }
    $this->applicator->applyDataSet($dataSet);
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
    $this->applicator->applyDataSet($this->applicator->createMySQLXMLDataSet($fixture));
  }

  /**
   * Populates a fixture into the database.
   *
   * @Given I have the fixtures:
   */
  public function applyFixtures(TableNode $fixtures) {
    $fixtures = $fixtures->getColumn(0);
    $dataSet = new CompositeDataSet();
    foreach ($fixtures as $fixture) {
      $dataSet->addDataSet($this->applicator->createMySQLXMLDataSet($fixture));
    }
    $this->applicator->applyDataSet($dataSet);
  }
}
