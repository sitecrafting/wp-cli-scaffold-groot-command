<?php

/**
 * GrootScaffoldTest\NamespaceTokenHandlerTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use GrootScaffold\TokenHandler\NamespaceTokenHandler;
use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the NamespaceTokenHandler class
 *
 * @group unit
 */
class NamespaceTokenHandlerTest extends TestCase {
	public function test_handle() {
    $handler = new NamespaceTokenHandler([
      'namespace',
      'SiteCrafting',
      '\\',
      'Project',
      '\\',
      ';',
    ], [
      'namespace' => 'SpecialProject',
    ]);

    $this->assertEquals(
      'namespace SpecialProject;',
      $handler->handle()
    );
  }
}
