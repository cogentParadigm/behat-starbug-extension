<?php
namespace Starbug\Behat\ServiceContainer;

use Starbug\Core\ContainerFactory;
use Starbug\Http\Url;

class StarbugLoader {
  protected $baseUrl;
  protected $basePath;
  public function __construct($baseUrl, $basePath = "/") {
    $this->baseUrl = $baseUrl;
    $this->basePath = $basePath;
  }
  public function boot() {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE | E_PARSE | E_ERROR);
    $factory = new ContainerFactory($this->basePath);
    $container = $factory->build([]);
    date_default_timezone_set($container->get('time_zone'));
    $components = parse_url($this->baseUrl);
    $url = new Url($components["host"], $container->get("website_url"));
    $url->setScheme($components["scheme"]);
    $request = $container->make("Starbug\Http\Request", ['url' => $url]);
    $container->set("Starbug\Http\RequestInterface", $request);
    return $container;
  }
}
