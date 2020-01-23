<?php

/**
 * GrootScaffoldTest\LibraryFileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use GrootScaffold\Generator\LibraryFileGenerator;
use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the LibraryFileGeneratorTest class
 *
 * @group unit
 */
class LibraryFileGeneratorTest extends TestCase {
  public function test_replace_contents_config_callback_tag() {
    $gen = new LibraryFileGenerator('file.php', [
      'config_callback' => 'fake_code("stuff");',
    ]);

    $contents = <<<EOF
<?php

/**
 * @groot config_callback
 */
EOF;

    $this->assertContains(
      'fake_code("stuff");',
      $gen->replace_contents($contents)
    );
  }

  public function test_replace_contents_namespace() {
    $gen = new LibraryFileGenerator('file.php', [
      'namespace' => 'ClientSite',
    ]);

    $contents = <<<EOF
<?php

namespace Project\Stuff;
EOF;

    $this->assertContains(
      'namespace ClientSite\\Stuff;',
      $gen->replace_contents($contents)
    );
  }

  public function test_replace_contents_use() {
    $gen = new LibraryFileGenerator('file.php', [
      'namespace' => 'ClientSite',
    ]);

    $contents = <<<EOF
<?php

namespace Project\Stuff;

use Project\Thingy;
EOF;

    $this->assertContains(
      'use ClientSite\\Thingy;',
      $gen->replace_contents($contents)
    );
  }
}
