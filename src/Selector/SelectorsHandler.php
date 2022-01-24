<?php
namespace Starbug\Behat\Selector;

use Behat\Mink\Selector\CssSelector;
use Behat\Mink\Selector\SelectorsHandler as ParentHandler;
use Behat\Mink\Selector\Xpath\Escaper;

class SelectorsHandler extends ParentHandler {
  public function __construct(array $selectors = array()) {
    $this->escaper = new Escaper();

    $this->registerSelector('named_partial', new PartialNamedSelector());
    $this->registerSelector('named_exact', new ExactNamedSelector());
    $this->registerSelector('css', new CssSelector());

    foreach ($selectors as $name => $selector) {
        $this->registerSelector($name, $selector);
    }
  }
}