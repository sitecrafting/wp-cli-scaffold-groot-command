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
 *
 * @group e2e
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
      . ' --theme_uri=https://example.com/starfruit'
      . ' --description="I AM FROOT"'
      . ' --author="Coby Tamayo <ctamayo@sitecrafting.com>"'
      . ' --author_uri=https://www.example.com'
      . ' --company="EvilCorp, Inc."'
      . ' --namespace=ClientSite'
      . ' --config_callback="// some config code"';

    echo `$command`;

    $this->assertDirectoryExists($this->theme_dir);

    foreach (['less/style.less', 'style.css'] as $file) {
      $this->assert_theme_file_contains(
        $file,
        'Theme Name: Starfruit'
      );
      $this->assert_theme_file_contains(
        $file,
        'Theme URI: https://example.com/starfruit'
      );
      $this->assert_theme_file_contains(
        $file,
        'Description: I AM FROOT'
      );
      $this->assert_theme_file_contains(
        $file,
        'Author: Coby Tamayo <ctamayo@sitecrafting.com>'
      );
      $this->assert_theme_file_contains(
        $file,
        'Author URI: https://www.example.com'
      );
      $this->assert_theme_file_contains(
        $file,
        'Copyright ' . date('Y') . ' EvilCorp, Inc.'
      );
    }

    $this->assertDirectoryExists( $this->theme_dir . 'lib/ClientSite' );

    // assert functions.php declares config callback
    $this->assert_theme_file_contains(
      'functions.php',
      "// some config code"
    );

    foreach (['BlogPost.php', 'Page.php', 'FrontPage.php'] as $libFile) {
      $this->assertFileExists(
        $this->theme_dir . 'lib/ClientSite/Post/' . $libFile
      );
      // TODO get namespaces working
      $this->assert_theme_file_contains(
        'lib/ClientSite/Post/' . $libFile,
        'namespace ClientSite\\Post;'
      );
    }
	}

  protected function assert_theme_file_contains( string $file, string $needle ) {
    $this->assertContains($needle, $this->get_theme_file_contents($file));
  }

  protected function get_theme_file_contents( string $file ) : string {
    return file_get_contents(
      ABSPATH . 'wp-content/themes/wp-scaffold-groot-test/' . $file
    );
  }
}
