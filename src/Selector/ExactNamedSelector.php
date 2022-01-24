<?php
namespace Starbug\Behat\Selector;

use Behat\Mink\Selector\ExactNamedSelector as ParentSelector;

class ExactNamedSelector extends ParentSelector {
  public function __construct() {
    $this->registerReplacement('%notFieldTypeFilter%', "not(%buttonTypeFilter%)");

    parent::__construct();
  }
}
