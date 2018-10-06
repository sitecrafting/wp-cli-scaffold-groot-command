<?php

/**
 * FileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\Generator;

/**
 * Generate a custom theme file
 */
class FileGenerator {
  protected $path;
  protected $options;

  public function __construct($path, $options) {
    $this->path = $path;
    $this->options = $options;
  }

  public function generate() {
    $this->write_contents($this->replace_contents($this->get_contents()));
  }

  protected function get_contents() {
    return file_get_contents($this->path);
  }

  protected function write_contents($contents) {
    return file_put_contents($this->path, $contents);
  }
}
