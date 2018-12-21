<?php

/**
 * GrootScaffoldTest\GrootTokenHandlerTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use GrootScaffold\TokenHandler\GrootTokenHandler;
use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the GrootTokenHandler class
 *
 * @group unit
 */
class GrootTokenHandlerTest extends TestCase {
	public function test_handle_config_callback() {
    $handler = new GrootTokenHandler([
      // token
      T_COMMENT,
      "/*\n   * @groot config_callback\n   */",
      35,
    ], [
      // options
      'config_callback' => "fake_code();\ndo_stuff();"
    ]);

    $this->assertEquals(
      "fake_code();\n  do_stuff();",
      $handler->handle()
    );
  }

  public function test_get_indentation_level() {
    $comment = <<<_PHP_
  /*
   * @groot three leading spaces here
   */
_PHP_;

    $handler = new GrootTokenHandler([
      // token
      T_COMMENT,
      $comment,
      123,
    ], []);

    $this->assertEquals(2, $handler->get_indentation_level());
  }

  public function test_get_groot_hook_name() {
    $handler = new GrootTokenHandler([
      T_COMMENT,
      '/* @groot the_hook */',
      123,
    ], []);

    $this->assertEquals('the_hook', $handler->get_groot_hook_name());
  }

  public function test_get_groot_hook_name_no_groot_tag() {
    $handler = new GrootTokenHandler([
      T_COMMENT,
      "// blah blah blah", // whoops
      123,
    ], []);

    $this->assertEquals('', $handler->get_groot_hook_name());
  }

  public function test_get_groot_hook_name_empty() {
    $handler = new GrootTokenHandler([
      T_COMMENT,
      "// @groot   ", // whoops
      123,
    ], []);

    $this->assertEquals('', $handler->get_groot_hook_name());
  }
}
