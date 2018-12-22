<?php

/**
 * TransparentTokenHandler class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\TokenHandler;

/**
 * Handles simple string tokens by simply returning their value
 */
class TransparentTokenHandler extends AbstractTokenHandler {
  public function handle() : string {
    // get just the value for each token
    $tokenValues = array_map(function($token) {
      // token is either:
      // - an array (whose index 1 holds the code string), or
      // - a string
      return is_array($token) ? $token[1] : $token;
    }, $this->tokens);

    return implode('', $tokenValues);
  }
}

