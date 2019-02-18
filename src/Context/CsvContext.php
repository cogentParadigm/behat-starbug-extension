<?php
namespace Starbug\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Ingenerator\BehatTableAssert\TableParser\CSVTable;
use Ingenerator\BehatTableAssert\AssertTable;
use Exception;

class CsvContext extends RawStarbugContext {

  /**
   * CSV download with exact match.
   *
   * @Then I should get a CSV matching:
   *
   * @throws Exception The most recent file in the downloads folder is not a CSV.
   */
  public function matchCsv(TableNode $expected, $options = []) {
    $actual = CSVTable::fromString($this->getMink()->getSession()->getDriver()->getContent());
    $actualRows = $actual->getRows();
    $expectedCount = count($expected->getRows());
    $actualCount = count($actualRows);
    if ($actualCount > $expectedCount) {
      $actualRows = array_slice($actualRows, 0, $expectedCount);
      $actual = new TableNode($actualRows);
    }
    $assert = new AssertTable();
    $assert->isComparable(
      $expected,
      $actual,
      $options + [
        "comparators" => $this->getComparators($expected)
      ]
    );
  }
  /**
   * CSV download with loose match.
   *
   * @Then I should get a CSV similar to:
   *
   * @throws Exception The most recent file in the downloads folder is not a CSV.
   */
  public function compareCsv(TableNode $expected) {
    $this->matchCsv($expected, ["ignoreExtraColumns" => true]);
  }
  /**
   * CSV download with exact match.
   *
   * @Then I should get a CSV matching :filepath
   */
  public function matchCsvFile(TableNode $filepath) {
    $file = fopen($filepath, "r");
    $expected = CSVTable::fromStream($file);
    fclose($file);
    $this->matchCsv($expected);
  }

  /**
   * CSV download with loose match.
   *
   * @Then I should get a CSV similar to :filepath
   */
  public function similarCsvFile(TableNode $filepath) {
    $file = fopen($filepath, "r");
    $expected = CSVTable::fromStream($file);
    fclose($file);
    $this->compareCsv($expected);
  }

  /**
   * Helper method to generate generic regex comparators.
   */
  public function getComparators(TableNode $table) {
    $comparators = [];
    $headers = $table->getRow(0);
    foreach ($headers as $header) {
      $comparators[$header] = function ($expected, $actual) {
        if (substr($expected, 0, 1) === "/") {
          return preg_match($expected, $actual);
        } else {
          return $expected === $actual;
        }
      };
    }
    return $comparators;
  }
}
