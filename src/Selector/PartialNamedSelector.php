<?php
namespace Starbug\Behat\Selector;

use Behat\Mink\Selector\PartialNamedSelector as ParentSelector;

class PartialNamedSelector extends ParentSelector {
  public function __construct() {
    $this->registerReplacement('%notFieldTypeFilter%', "not(%buttonTypeFilter%)");

    parent::__construct();
  }
}
