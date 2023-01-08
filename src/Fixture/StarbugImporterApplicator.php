<?php
namespace Starbug\Behat\Fixture;

use Starbug\Db\Operation\Migrate;
use Starbug\Imports\Import;
use Starbug\Imports\Importer;
use Starbug\Imports\Read\YamlFixtureStrategy;
use Starbug\Imports\Write\FixtureStrategy;

class StarbugImporterApplicator {

  protected $importer;
  protected $operation;

  public function __construct(Importer $importer, Migrate $operation) {
    $this->importer = $importer;
    $this->operation = $operation;
  }

  /**
   * Apply a fixture.
   *
   * @param Import $dataSet
   */
  public function applyDataSet(Import $dataSet) {
    $this->importer->run($dataSet);
  }

  public function createYmlDataSet($ymlFile) {
    $import = new Import(false);
    $import->setReadStrategy(YamlFixtureStrategy::class, ["path" => $ymlFile]);
    $import->setWriteStrategy(FixtureStrategy::class, ["operation" => $this->operation]);
    return $import;
  }
}
