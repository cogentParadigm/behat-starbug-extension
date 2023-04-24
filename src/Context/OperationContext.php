<?php
namespace Starbug\Behat\Context;

use Starbug\Behat\Context\RawStarbugContext;
use Behat\Gherkin\Node\TableNode;
use Psr\Container\ContainerInterface;
use PHPUnit\Framework\Assert;
use Starbug\Core\Operation\Save;

class OperationContext extends RawStarbugContext {

  protected $model;
  protected $operation = Save::class;
  protected $saved;

  public function setStarbugContainer(ContainerInterface $container) {
    parent::setStarbugContainer($container);
    if ($container->has("Starbug\Db\DatabaseInterface")) {
      $this->db = $container->get("Starbug\Db\DatabaseInterface");
    } else {
      $this->db = $container->get("Starbug\Core\DatabaseInterface");
    }
    $this->operations = $container->get("Starbug\Operation\OperationFactoryInterface");
  }

  public function saveRecord(TableNode $table) {
    $input = $table->getRowsHash();
    $input = $this->interpolateFieldValues($input);
    $this->operation($this->operation, $input);
    $this->captureSavedRecord();
  }

  /**
   * Helper function to interpolate field values.
   */
  public function interpolateFieldValues($input) {
    return $input;
  }

  public function getSavedRecord() {
    return $this->saved;
  }

  protected function operation($name, $args) {
    $operation = $this->operations->get($name);
    $operation->configure(["model" => $this->model]);
    $bundle = $operation->execute($args);
    Assert::assertTrue($operation->success(), print_r($bundle->get(), true));
    return $operation;
  }

  protected function captureSavedRecord() {
    $id = $this->db->getInsertId($this->model);
    $this->saved = $this->db->query($this->model)->condition("id", $id)->one();
  }
}
