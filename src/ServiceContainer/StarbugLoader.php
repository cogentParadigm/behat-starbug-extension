<?php
namespace Starbug\Behat\ServiceContainer;

use Exception;
use Starbug\Core\ContainerFactory;
use Starbug\Http\Url as HttpUrl;
use Starbug\Core\URL as CoreUrl;

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
    if (class_exists("Starbug\Http\Url")) {
      $url = new HttpUrl($components["host"], $container->get("website_url"));
      $url->setScheme($components["scheme"]);
      $request = $container->make("Starbug\Http\Request", ['url' => $url]);
      $container->set("Starbug\Http\RequestInterface", $request);
    } else if (class_exists("Starbug\Core\URL")) {
      $url = new CoreUrl($components["host"], $container->get("website_url"));
      $url->setScheme($components["scheme"]);
      $request = $container->make("Starbug\Core\Request", ['url' => $url]);
      $container->set("Starbug\Core\RequestInterface", $request);
    } else {
      throw new Exception("Unable to bootstrap Starbug. Cannot find suitable UrlInterface.");
    }
    return $container;
  }
}
