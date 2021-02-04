<?php
namespace Starbug\Behat\Fixture;

use PHPUnit\DbUnit\Operation\Composite;
use PHPUnit\DbUnit\Operation\Factory;
use PDO;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\DataSet\IDataSet;
use PHPUnit\DbUnit\DataSet\XmlDataSet;
use Starbug\Behat\DbUnit\DataSet\PrefixedMysqlXmlDataSet;

class Applicator {

  protected $conn = null;
  protected $pdo = null;
  protected $operation = null;
  protected $tablePrefix;

  public function __construct(PDO $pdo, $prefix = "") {
    $this->pdo = $pdo;
    $this->tablePrefix = $prefix;
  }

  /**
   * Sets the test dataset to use.
   *
   * @param IDataSet $dataSet
   */
  public function applyDataSet(IDataSet $dataSet) {
    $this->getOperation()->execute($this->getConnection(), $dataSet);
  }

  protected function getOperation() {
    if ($this->operation == null) {
      $this->operation = new Composite([
        Factory::TRUNCATE(true),
        Factory::INSERT()
      ]);
    }
    return $this->operation;
  }

  protected function getConnection() {
    if ($this->conn === null) {
      $this->conn = new DefaultConnection($this->pdo);
    }
    return $this->conn;
  }

  /**
   * Creates a new ArrayDataSet with the given array.
   * The array parameter is an associative array of tables where the key is
   * the table name and the value an array of rows. Each row is an associative
   * array by itself with keys representing the field names and the values the
   * actual data.
   * For example:
   * array(
   *     "addressbook" => array(
   *         array("id" => 1, "name" => "...", "address" => "..."),
   *         array("id" => 2, "name" => "...", "address" => "...")
   *     )
   * )
   *
   * @param array $data
   *
   * @return ArrayDataSet
   */
  public function createArrayDataSet(array $data) {
    return new ArrayDataSet($data);
  }

  /**
   * Creates a new FlatXmlDataSet with the given $xmlFile. (absolute path.)
   *
   * @param string $xmlFile
   *
   * @return FlatXmlDataSet
   */
  public function createFlatXMLDataSet($xmlFile) {
    return new FlatXmlDataSet($xmlFile);
  }

  /**
   * Creates a new XMLDataSet with the given $xmlFile. (absolute path.)
   *
   * @param string $xmlFile
   *
   * @return XmlDataSet
   */
  public function createXMLDataSet($xmlFile) {
    return new XmlDataSet($xmlFile);
  }

  /**
   * Create a a new PrefixedMysqlXmlDataSet with the given $xmlFile. (absolute path.)
   *
   * @param string $xmlFile
   *
   * @return PrefixedMysqlXmlDataSet
   */
  public function createMySQLXMLDataSet($xmlFile) {
    return new PrefixedMysqlXmlDataSet($xmlFile, $this->tablePrefix);
  }
}
