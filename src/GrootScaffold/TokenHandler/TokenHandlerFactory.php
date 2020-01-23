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
    while ($tokens) {
      $handlerClass  = $this->get_handler_class($tokens[0]);
      $consumeTokens = $this->get_token_consumer($handlerClass);
      $group         = $consumeTokens($tokens);

      yield new $handlerClass($group, $options);
    }
  }

  protected function get_handler_class($token) {
    if ($this->is_groot_hook_comment($token)) {
      return GrootTokenHandler::class;
    } elseif ($this->is_namespace_token($token)) {
      return NamespaceTokenHandler::class;
    } else {
      return TransparentTokenHandler::class;
    }
  }

  /**
   * Get a function that builds up a collection of tokens to send to an
   * instance of $handlerClass
   * as long as they are tokens that $handlerClass handles.
   *
   * @return callable
   */
  protected function get_token_consumer(string $handlerClass) {
    $consumers = [
      GrootTokenHandler::class => function(array &$tokens) {
        // Groot hooks are always just single tokens
        return [array_shift($tokens)];
      },

      NamespaceTokenHandler::class => function(array &$tokens) {
        $group = [];
        do {
          $token   = array_shift($tokens);
          $group[] = $token;
        } while ($token !== ';');

        return $group;
      },

      TransparentTokenHandler::class => function(array &$tokens) {
        $group = [];
        while ($tokens && $this->is_transparent($tokens[0])) {
          $group[] = array_shift($tokens);
        }

        return $group;
      },
    ];

    return $consumers[$handlerClass];
  }

  protected function is_transparent($token) {
    return ! (
      $this->is_namespace_token($token)
      ||
      $this->is_groot_hook_comment($token)
    );
  }

  protected function is_namespace_token($token) {
    return is_array($token) && (
      $token[0] === T_NAMESPACE ||
      $token[0] === T_USE
    );
  }

  protected function is_use_token($token) {
    return is_array($token) && $token[0] === T_USE;
  }

  protected function is_groot_hook_comment($token) {
    return is_array($token)
      && in_array($token[0], [T_COMMENT, T_DOC_COMMENT])
      && strpos($token[1], GrootTokenHandler::GROOT_TAG) !== false;
  }
}
