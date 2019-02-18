<?php
namespace Starbug\Behat\Context;

use Behat\Behat\Context\Context;
use Exception;

/**
 * A very simple context for running shell commands.
 */
class ShellContext implements Context {
  private $output;
  /**
   * Run a shell command.
   *
   * @When I run :command
   */
  public function iRun($command) {
    $this->output = shell_exec($command);
  }
  /**
   * Validate command output.
   *
   * @Then I should see :string in the output
   *
   * @throws Exception if output does not contain phrase.
   */
  public function iShouldSeeInTheOutput($string) {
    if (strpos($this->output, $string) === false) {
        throw new Exception(sprintf('Did not see "%s" in output "%s"', $string, $this->output));
    }
  }
}
