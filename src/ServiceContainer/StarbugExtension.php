<?php
namespace Starbug\Behat\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Interop\Container\ContainerInterface;

class StarbugExtension implements ExtensionInterface {

  const STARBUG_ID = "starbug";

  public function getConfigKey() {
    return self::STARBUG_ID;
  }

  public function initialize(ExtensionManager $extensionManager) {
    // Nothing to do.
  }

  public function configure(ArrayNodeDefinition $builder) {
    // Nothing to do.
  }

  public function load(ContainerBuilder $container, array $config) {
    $starbug = $this->loadStarbug($container, $config);
    $this->loadClassGenerator($container);
    $this->loadContextInitializer($container, $starbug);
  }

  public function process(ContainerBuilder $container) {
    // Nothing to do.
  }

  private function loadStarbug(ContainerBuilder $container, array $config) {
    $loader = new StarbugLoader($container->getParameter("mink.base_url"), $container->getParameter("paths.base"));
    $container->set("starbug.container", $starbug = $loader->boot());
    return $starbug;
  }

  private function loadClassGenerator(ContainerBuilder $container) {
    // $definition = new Definition('Behat\Symfony2Extension\Context\ContextClass\KernelAwareClassGenerator');
    // $definition->addTag(ContextExtension::CLASS_GENERATOR_TAG, ['priority' => 100]);
    // $container->setDefinition('symfony2_extension.class_generator.kernel_aware', $definition);
  }
  private function loadContextInitializer(ContainerBuilder $container, ContainerInterface $starbug) {
    $definition = new Definition('Starbug\Behat\Context\Initializer\StarbugAwareInitializer', [$starbug]);
    $definition->addTag(ContextExtension::INITIALIZER_TAG, ['priority' => 0]);
    $container->setDefinition('starbug.context_initializer', $definition);
  }
}
