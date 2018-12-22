<?php

/**
 * NamespaceTokenHandler class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\TokenHandler;

/**
 * Handles simple string tokens by simply returning their value
 */
class NamespaceTokenHandler extends AbstractTokenHandler {
  const PLACEHOLDER_NAMESPACE = 'Project';

  public function handle() : string {
    return array_reduce($this->tokens, function(string $code, $token) {
      // get the value for the current token, whether the token is a a string
      // or an array
      $value = is_string($token)
        ? $token
        : $token[1];

      $value = ($value === static::PLACEHOLDER_NAMESPACE)
        ? $this->options['namespace']
        : $value;

      return $code . $value;
    }, '');
  }
}
