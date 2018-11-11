<?php
namespace Starbug\Behat\Context;

use Behat\Behat\Context\Context;
use Interop\Container\ContainerInterface;

interface StarbugAwareContext extends Context {
  public function setStarbugContainer(ContainerInterface $container);
}
