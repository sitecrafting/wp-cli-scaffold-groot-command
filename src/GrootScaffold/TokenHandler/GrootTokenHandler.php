<?php

/**
 * GrootTokenHandler class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\TokenHandler;

/**
 * Creates TokenHandler objects
 */
class GrootTokenHandler extends AbstractTokenHandler {
  /**
   * The tag to look for to consider this a "Groot token"
   *
   * @var string
   */
  const GROOT_TAG = '@groot';

  public function handle() : string {
    $indentation = str_repeat(' ', $this->get_indentation_level());

    // which Groot hook are we dealing with?
    // e.g. "config_callback"
    $name = $this->get_groot_hook_name();

    $hookCode = $this->options[$name] ?? '';

    // return the generated code, properly indented
    return implode("\n$indentation", explode("\n", $hookCode));
  }

  /**
   * Get the Groot hook name for this token, if any.
   * For example, "// @groot the_hook_name" -> "the_hook_name"
   *
   * @return string
   */
  public function get_groot_hook_name() : string {
    // e.g. ["/*", "", "", "*", "@groot", "the_hook", "", ...]
    $trimmedWords       = array_map('trim', explode(' ', $this->get_value()));
    // e.g. ["/*", "*", "@groot", "the_hook", "", ...]
    $nonWhitespaceWords = array_values(array_filter($trimmedWords));

    // Find and return the word after the @groot tag.
    foreach ($nonWhitespaceWords as $i => $word) {
      if ($word === '@groot') {
        return $nonWhitespaceWords[$i + 1] ?? '';
      }
    }

    // We didn't find the @groot tag.
    return '';
  }

  public function get_indentation_level() : int {
    $lines = explode("\n", $this->get_value());

    // It's not possible to determine indentation level from a single line of
    // code. This is because the preceding whitespace is actually part of the
    // preceding token. Fortunately, this also means that it doesn't really
    // matter for single lines.
    if (count($lines) < 2) {
      return 0;
    }

    // how many characters are removed when we left-trim spaces?
    $spaces = strlen($lines[1]) - strlen(ltrim($lines[1], ' '));

    // round down to the nearest even number of spaces
    return floor($spaces / 2) * 2;
  }

  protected function get_value() {
    return $this->tokens[0][1];
  }
}
