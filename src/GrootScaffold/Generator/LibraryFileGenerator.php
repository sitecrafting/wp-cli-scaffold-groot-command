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

    // TODO somehow make factory or loop aware of subsequent tokens, so we can capture namespaces!!
    $handledTokens = array_reduce($tokens, function(
      array $handled,
      $token
    ) use($factory) {
      $handler   = $factory->create($token, $this->options);
      $handled[] = $handler->handle();

      return $handled;
    }, []);

    return implode('', $handledTokens);
  }
}

?>
