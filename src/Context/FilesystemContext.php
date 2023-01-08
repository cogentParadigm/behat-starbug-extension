<?php
namespace Starbug\Behat\Context;

use Starbug\Behat\Context\RawStarbugContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use GuzzleHttp\Psr7\UploadedFile;
use PHPUnit\Framework\Assert;
use Psr\Container\ContainerInterface;

class FilesystemContext extends RawStarbugContext {

  protected $timestamp;
  protected $lastFile;

  public function setStarbugContainer(ContainerInterface $container) {
    parent::setStarbugContainer($container);
    $this->adapters = $container->get("filesystem.adapters");
    $this->filesystems = $container->get("League\Flysystem\MountManager");
    $this->models = $container->get("Starbug\Core\ModelFactoryInterface");
    $this->uploader = $container->get("Starbug\Files\FileUploaderInterface");
  }

  /**
   * Clean database before scenarios tagged with @database.
   *
   * @BeforeScenario @filesystem
   */
  public function beforeFilesystemScenario(BeforeScenarioScope $scope) {
    $this->cleanFilesystem();
    $this->timestamp = time();
  }

  /**
   * Cleans filesystem
   *
   * @Given a clean filesystem
   */
  public function cleanFilesystem() {
    foreach ($this->adapters as $adapter) {
      $filesystem = $this->filesystems->getFilesystem($adapter);
      $contents = $filesystem->listContents("");
      foreach ($contents as $file) {
        if ($file["type"] == "file") {
          $filesystem->delete($file["path"]);
        } elseif ($file["type"] == "dir") {
          $filesystem->deleteDir($file["path"]);
        }
      }
    }
  }

  /**
   * Place a file at a specific location.
   *
   * @Given the file :source is at :destination
   */
  public function placeFile($source, $destination) {
    $parts = explode("/", $destination);
    array_pop($parts);
    $dir = implode("/", $parts);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    copy($source, $destination);
  }

  /**
   * Assert that a file exists.
   *
   * @Then the file :path will exist
   */
  public function assertFileExists($path) {
    Assert::assertFileExists($path);
  }

  /**
   * Assert that a file does not exist.
   *
   * @Then the file :path will not exist
   */
  public function assertFileNotExists($path) {
    Assert::assertFileNotExists($path);
  }

  /**
   * Assert that a new file exists, based on pattern.
   *
   * @Then a new file like :path will exist
   */
  public function assertNewFilePatternExists($path) {
    $found = false;
    $files = glob($path);
    foreach ($files as $file) {
      if (filemtime($file) >= $this->timestamp) {
        $found = $file;
        $this->lastFile = $file;
      }
    }
    Assert::assertNotFalse($found);
  }

  /**
   * Assert that a file has a specific mime type.
   *
   * @Then :path will be a file of type :type
   */
  public function assertFileMimeType($path, $type) {
    Assert::assertEquals($type, $this->models->get("files")->getMime($path));
  }

  /**
   * Assert that the last file has a specific mime type.
   *
   * @Then the file will be of type :type
   */
  public function assertLastFileMimeType($type) {
    $this->assertFileMimeType($this->lastFile, $type);
  }

  /**
   * Upload a file.
   *
   * @When I upload :path to :field
   */
  public function uploadFile($path, $field) {
    $filename = explode("/", $path);
    $filename = array_pop($filename);
    $uploaded = $this->uploader->upload(
      [],
      new UploadedFile($path, filesize($path), 0, $filename)
    );
    if (!empty($uploaded)) {
      $this->mink->fillField($field, $uploaded["id"]);
    }
  }
}
