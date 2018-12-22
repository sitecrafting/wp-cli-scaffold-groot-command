<?php

/**
 * AbstractTokenHandler class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\TokenHandler;

/**
 * Abstract class all TokenHandlers must extend
 */
abstract class AbstractTokenHandler {
  protected $tokens;
  protected $options;

  abstract public function handle() : string;

  public function __construct(array $tokens, array $options = []) {
    $this->tokens  = $tokens;
    $this->options = $options;
  }
}
