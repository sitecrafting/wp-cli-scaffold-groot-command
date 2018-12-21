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
  public function create($token, array $options) : AbstractTokenHandler {
    if (is_string($token)) {
      return new TransparentTokenHandler($token);

    } elseif ($this->is_groot_comment_token($token)) {
      return new GrootTokenHandler($token[1], $options);

    } elseif ($token[0] === T_NAMESPACE) {
      return new NamespaceTokenHandler($token[1], $options);

    } else {
      // any other array
      return new TransparentTokenHandler($token[1]);
    }
  }

  protected function is_groot_comment_token($token) {
    return is_array($token)
      && in_array($token[0], [T_COMMENT, T_DOC_COMMENT])
      && strpos($token[1], GrootTokenHandler::GROOT_TAG) !== false;
  }
}
