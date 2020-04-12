<?php
namespace Starbug\Behat\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

use Psr\Container\ContainerInterface;
use Starbug\Behat\Context\StarbugAwareContext;

/**
 * Starbug aware contexts initializer.
 *
 * Provides dependency instance and parameters to the StarbugAware contexts.
 */
class StarbugAwareInitializer implements ContextInitializer {
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }
  public function initializeContext(Context $context) {
    if (!$context instanceof StarbugAwareContext) {
      return;
    }
    $context->setStarbugContainer($this->container);
  }
}
