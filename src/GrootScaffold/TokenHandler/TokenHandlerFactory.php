<?php

/**
 * TokenHandlerFactory class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\TokenHandler;

/**
 * Creates TokenHandler objects
 */
class TokenHandlerFactory {
  public function generate_token_handlers(array $tokens, array $options) {
    // Get a group of token chunks
    $group = [];
    while ($tokens) {
      $token = array_shift($tokens);
      $group[] = $token;

      if ($this->is_groot_hook_comment($token)) {

        // Groot hooks are always just single tokens
        yield new GrootTokenHandler([$token], $options);

      } elseif ($token[0] === T_NAMESPACE) {

        // This token is the beginning of a namespace declaration.
        // Group together all subsquent tokens in the declaration.
        while ($tokens[0] !== ';') {
          $group[] = array_shift($tokens);
        }
        // Get the last token in the declaration, which we know is just a ";"
        $group[] = ';';

        yield new NamespaceTokenHandler($group, $options);

      } elseif (is_string($token)) {

        // This token is a string.
        // Group together all subsequent string tokens.
        while (is_string($tokens[0])) {
          $group[] = array_shift($tokens);
        }

        yield new TransparentTokenHandler($group, $options);

      }
    }
  }

  protected function is_groot_hook_comment($token) {
    return is_array($token)
      && in_array($token[0], [T_COMMENT, T_DOC_COMMENT])
      && strpos($token[1], GrootTokenHandler::GROOT_TAG) !== false;
  }
}
