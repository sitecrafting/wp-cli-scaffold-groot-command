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
    $handledChunks = [];
    foreach($this->generate_token_handlers($tokens) as $handler) {
      $handledChunks[] = $handler->handle();
    }

    return implode('', $handledChunks);
  }

  protected function generate_token_handlers($tokens) {
    // Get a group of token chunks
    $group = [];
    while ($tokens) {
      $token = array_shift($tokens);
      $group[] = $token;

      if ($this->is_groot_hook_comment($token)) {

        // Groot hooks are always just single tokens
        yield new GrootTokenHandler([$token], $this->options);

      } elseif ($token[0] === T_NAMESPACE) {

        // This token is the beginning of a namespace declaration.
        // Group together all subsquent tokens in the declaration.
        while ($tokens[0] !== ';') {
          $group[] = array_shift($tokens);
        }
        // Get the last token in the declaration, which we know is just a ";"
        $group[] = ';';

        yield new NamespaceTokenHandler($group, $this->options);

      } elseif (is_string($token)) {

        // This token is a string.
        // Group together all subsequent string tokens.
        while (is_string($tokens[0])) {
          $group[] = array_shift($tokens);
        }

        yield new TransparentTokenHandler($group, $this->options);

      }
    }
  }

  protected function is_groot_hook_comment($token) {
    return is_array($token)
      && in_array($token[0], [T_COMMENT, T_DOC_COMMENT])
      && strpos($token[1], GrootTokenHandler::GROOT_TAG) !== false;
  }
}

?>
