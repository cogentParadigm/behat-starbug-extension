<?php
namespace Starbug\Behat\ServiceContainer;

use Starbug\Core\ContainerFactory;

class StarbugLoader {
  private $basePath;
  public function __construct($basePath) {
    $this->basePath = $basePath;
  }
  public function boot() {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE | E_PARSE | E_ERROR);
    $factory = new ContainerFactory($this->basePath);
    $container = $factory->build([]);
    date_default_timezone_set($container->get('time_zone'));
    $url = $container->make("Starbug\Core\URL", ['base_directory' => $container->get("website_url")]);
    $request = $container->make("Starbug\Core\Request", ['url' => $url]);
    $container->set("Starbug\Core\RequestInterface", $request);
    return $container;
  }
}
