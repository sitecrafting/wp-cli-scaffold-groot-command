<?php

/**
 * GrootScaffoldTest\TransparentTokenHandlerTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use GrootScaffold\TokenHandler\TransparentTokenHandler;
use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the TransparentTokenHandler class
 *
 * @group unit
 */
class TransparentTokenHandlerTest extends TestCase {
	public function test_handle() {
    $handler = new TransparentTokenHandler([
      [T_COMMENT, "// this is a comment\n", 123],
      [T_STRING, 'fun', 123],
      '(',
      ')',
      ';',
    ], []);

    $this->assertEquals(
      "// this is a comment\nfun();",
      $handler->handle()
    );
  }
}
