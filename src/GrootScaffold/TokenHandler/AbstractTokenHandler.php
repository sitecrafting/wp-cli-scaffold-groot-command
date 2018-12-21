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
  protected $token;
  protected $options;
  protected $code;

  public function __construct($token, array $options = []) {
    $this->token   = $token;
    $this->options = $options;

    if (is_array($token)) {
      $this->type        = $token[0];
      $this->value       = $token[1];
      $this->line_number = $token[2];
    } else {
      // token is already a string
      $this->value       = $token;
    }
  }

  abstract public function handle() : string;

  public function get_type() : int {
    return $this->type ?? -1;
  }

  public function get_value() : string {
    return $this->value;
  }

  public function get_line_number() : int {
    return $this->line_number ?? -1;
  }
}
