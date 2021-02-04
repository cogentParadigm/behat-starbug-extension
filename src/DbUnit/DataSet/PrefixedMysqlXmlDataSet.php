<?php
namespace Starbug\Behat\DbUnit\DataSet;

use PHPUnit\DbUnit\DataSet\MysqlXmlDataSet;

use PHPUnit\DbUnit\RuntimeException;

/**
 * Data set implementation for the output of mysqldump --xml.
 */
class PrefixedMysqlXmlDataSet extends MysqlXmlDataSet {

  protected $tablePrefix;

  public function __construct($xmlFile, $prefix = "") {
    $this->tablePrefix = $prefix;
    parent::__construct($xmlFile);
  }
  protected function getTableInfo(array &$tableColumns, array &$tableValues): void {
    if ($this->xmlFileContents->getName() != 'mysqldump') {
      throw new RuntimeException('The root element of a MySQL XML data set file must be called <mysqldump>');
    }

    foreach ($this->xmlFileContents->xpath('./database/table_data') as $tableElement) {
      if (empty($tableElement['name'])) {
        throw new RuntimeException('<table_data> elements must include a name attribute');
      }

      if (0 !== strpos($tableElement["name"], $this->tablePrefix)) {
        $tableElement->attributes()->name = $this->tablePrefix . $tableElement["name"];
      }
    }

    parent::getTableInfo($tableColumns, $tableValues);
  }
}
