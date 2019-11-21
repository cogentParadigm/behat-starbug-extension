<?php
namespace Starbug\Behat\ServiceContainer;

use Behat\Testwork\ServiceContainer\ExtensionManager;
use DMore\ChromeExtension\Behat\ServiceContainer\ChromeExtension as ParentExtension;
use Starbug\Behat\ServiceContainer\Driver\ChromeFactory;

class ChromeExtension extends ParentExtension {
  /**
   * {@inheritdoc}
   */
  public function initialize(ExtensionManager $extensionManager) {
    if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
      $minkExtension->registerDriverFactory(new ChromeFactory());
    }
  }

}
