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
  public function handle() : string {
    return "namespace {$this->options['namespace']};";
  }
}
