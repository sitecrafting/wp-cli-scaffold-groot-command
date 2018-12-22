<?php

/**
 * GrootScaffoldTest\TokenHandlerFactoryTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace TokenHandlerFactoryTest;

use PHPUnit\Framework\TestCase;

use GrootScaffold\TokenHandler\TokenHandlerFactory;
use GrootScaffold\TokenHandler\GrootTokenHandler;
use GrootScaffold\TokenHandler\NamespaceTokenHandler;
use GrootScaffold\TokenHandler\TransparentTokenHandler;

/**
 * Test the TokenHandlerFactory class
 *
 * @group unit
 */
class TokenHandlerFactoryTest extends TestCase {
  public function test_generate_token_handlers() {
    $php = <<<EOF
<?php

/**
 * File comment blah blah
 */

namespace Hello\World;

class Thingy {
  /*
   * @groot example_groot_tag
   */

  public function hi() {}
}
EOF;

    $tokens           = token_get_all($php);
    $factory          = new TokenHandlerFactory();
    $handlers         = $factory->generate_token_handlers($tokens, []);
    $expectedHandlers = [
      TransparentTokenHandler::class,
      NamespaceTokenHandler::class,
      TransparentTokenHandler::class,
      GrootTokenHandler::class,
      TransparentTokenHandler::class,
    ];

    foreach ($handlers as $i => $handler) {
      $this->assertInstanceOf($expectedHandlers[$i], $handler);
    }
  }
}
