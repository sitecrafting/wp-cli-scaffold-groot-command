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
      [T_NAMESPACE, 'namespace', 123],
      [T_WHITESPACE, ' ', 123],
      [T_STRING, 'Project', 123],
      [T_NS_SEPARATOR, '\\', 123],
      [T_STRING, 'Subspace', 123],
      ';',
    ], [
      'namespace' => 'SpecialProject',
    ]);

    $this->assertEquals(
      'namespace SpecialProject\\Subspace;',
      $handler->handle()
    );
  }

  public function test_handle_without_namespace_option() {
    $handler = new NamespaceTokenHandler([
      [T_NAMESPACE, 'namespace', 123],
      [T_WHITESPACE, ' ', 123],
      [T_STRING, 'Project', 123],
      [T_NS_SEPARATOR, '\\', 123],
      [T_STRING, 'Subspace', 123],
      ';',
    ], [
      'name' => 'special project',
    ]);

    $this->assertEquals(
      'namespace SpecialProject\\Subspace;',
      $handler->handle()
    );
  }
}
