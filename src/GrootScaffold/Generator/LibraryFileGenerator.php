<?php

/**
 * LibraryFileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\Generator;

use GrootScaffold\TokenHandler\TokenHandlerFactory;
use GrootScaffold\TokenHandler\GrootTokenHandler;
use GrootScaffold\TokenHandler\NamespaceTokenHandler;
use GrootScaffold\TokenHandler\TransparentTokenHandler;

/**
 * Generate a custom theme stylesheet
 */
class LibraryFileGenerator extends FileGenerator {
  public function replace_contents($contents) {
    $tokens = token_get_all($contents);

    $factory = new TokenHandlerFactory();
    $handlers = $factory->generate_token_handlers($tokens, $this->options);

    $handledChunks = [];
    foreach($handlers as $handler) {
      $handledChunks[] = $handler->handle();
    }

    return implode('', $handledChunks);
  }
}

?>
