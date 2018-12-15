<?php

/**
 * GrootScaffoldTest\GrootScaffoldTest class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the `wp scaffold groot` command end-to-end
 */
class GrootScaffoldTest extends TestCase {
  protected $theme_dir;

  public function setUp() {
    $this->theme_dir = ABSPATH . 'wp-content/themes/wp-scaffold-groot-test/';
    `rm -rf {$this->theme_dir}`;
  }

  public function tearDown() {
    `rm -rf {$this->theme_dir}`;
  }

	public function test_wp_scaffold_groot_command() {
    if (`which wp`) {
      $wpCliPath = 'wp';
    } elseif(getenv('WP_CLI_PATH')) {
      $wpCliPath = getenv('WP_CLI_PATH');
    } else {
      $this->fail('No `wp` detected on your system!');
    }

    $command = "$wpCliPath scaffold groot wp-scaffold-groot-test"
      . ' --theme_name=Starfruit'
      . ' --theme_uri=https://grootthe.me'
      . ' --description="I AM GROOT"'
      . ' --author="Coby Tamayo <ctamayo@sitecrafting.com>"'
      . ' --author_uri=https://www.sitecrafting.com';

    echo `$command`;

    $this->assertDirectoryExists($this->theme_dir);
    $this->assertContains(
      'Description: I AM GROOT',
      $this->get_theme_file_contents('less/style.less')
    );
	}

  protected function get_theme_file_contents( string $file ) : string {
    return file_get_contents(
      ABSPATH . 'wp-content/themes/wp-scaffold-groot-test/' . $file
    );
  }
}
