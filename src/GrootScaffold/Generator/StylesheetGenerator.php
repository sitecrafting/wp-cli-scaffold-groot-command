<?php

/**
 * StylesheetGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\Generator;

/**
 * Generate a custom theme stylesheet
 */
class StylesheetGenerator extends FileGenerator {
  public function replace_contents($contents) {
    if (!empty($this->options['theme_name'])) {
      $contents = str_replace(
        'Theme Name: Groot',
        'Theme Name: ' . $this->options['theme_name'],
        $contents
      );
    }

    if (!empty($this->options['theme_uri'])) {
      $contents = str_replace(
        'Theme URI: https://grootthe.me',
        'Theme URI: ' . $this->options['theme_uri'],
        $contents
      );
    }

    if (!empty($this->options['description'])) {
      $contents = str_replace(
         'Description: The official SiteCrafting WordPress starter theme',
        'Description: ' . $this->options['description'],
        $contents
      );
    }

    if (!empty($this->options['author'])) {
      $contents = str_replace(
        'Author: SiteCrafting',
        'Author: ' . $this->options['author'],
        $contents
      );
    }

    if (!empty($this->options['author_uri'])) {
      $contents = str_replace(
        'Author URI: https://www.sitecrafting.com',
        'Author URI: ' . $this->options['author_uri'],
        $contents
      );
    }

    echo $contents;
    return $contents;
  }
}
