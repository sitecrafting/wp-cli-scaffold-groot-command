<?php

/**
 * LibraryFileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\Generator;

use GrootScaffold\TokenHandler\TokenHandlerFactory;

/**
 * Generate a custom theme stylesheet
 */
class LibraryFileGenerator extends FileGenerator {
  public function replace_contents($contents) {
    $tokens = token_get_all($contents);
    $factory = new TokenHandlerFactory();

    // Get a Generator of parsed token chunks
    $parsed = (function($tokens) {
      foreach ($tokens as $token) {
        yield $token;
      }
    })($tokens);

    // TODO somehow make factory or loop aware of subsequent tokens, so we can capture namespaces!!
    $iter = new \ArrayIterator($tokens);
    $handledChunks = [];
    foreach($parsed as $chunk) {
      $handler         = $factory->create($chunk, $this->options);
      $handledChunks[] = $handler->handle();
    }

    return implode('', $handledChunks);
  }
}

?>
