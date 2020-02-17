<?php
namespace Starbug\Behat\Context;

use Behat\Behat\Hook\Scope\AfterStepScope;
use DMore\ChromeDriver\ChromeDriver;
use Starbug\Behat\Context\RawStarbugContext;

/**
 * Defines application features from the specific context.
 */
class ScreenshotsContext extends RawStarbugContext {

  /**
   * Capture a screenshot after each step.
   *
   * @AfterStep
   */
  public function stepScreenshot(AfterStepScope $scope) {
    $driver = $this->getMink()->getSession()->getDriver();
    if (!($driver instanceof ChromeDriver)) {
      return;
    }
    $feature = $scope->getFeature()->getFile();
    $line = $scope->getStep()->getLine();
    $dir = "var/public/screenshots/" . $feature;
    if (!file_exists($dir)) {
      mkdir($dir, 0755, true);
    }
    $width = $driver->evaluateScript("window.innerWidth");
    $height = $driver->evaluateScript("window.document.body.scrollHeight");
    $driver->resizeWindow($width, $height);
    $driver->captureScreenshot($dir . "/" . $line . ".png", ["clip" => [
      "x" => 0,
      "y" => 0,
      "width" => $width,
      "height" => $height,
      "scale" => 1
    ]]);
  }
}
