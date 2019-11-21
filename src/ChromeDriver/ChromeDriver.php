<?php
namespace Starbug\Behat\ChromeDriver;

use DMore\ChromeDriver\ChromeDriver as ParentDriver;

class ChromeDriver extends ParentDriver {
  protected function waitForDom() {
    parent::waitForDom();
    if ($this->evaluateScript('document.querySelector("script[src$=\"/dojo.js\"]") != null')) {
      $this->wait(10000, "typeof require != 'undefined'");
      $script = <<<JS
  window.pageIsReady = false;
  require(["dojo/ready", "dojo/parser"], function(ready, config) {
    ready(function(){
      window.pageIsReady = true;
    });
  });
JS;
      $this->executeScript($script);
      $this->wait(10000, "window.pageIsReady == true");
    }
  }
}
