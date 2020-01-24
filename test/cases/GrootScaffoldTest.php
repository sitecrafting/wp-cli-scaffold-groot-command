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
    $wpCliPath = realpath( __DIR__ . '/../../vendor/bin/wp' );

    $command = "$wpCliPath scaffold groot wp-scaffold-groot-test"
      . ' --quiet'
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

    // assert functions.php uses the right files
    $this->assert_theme_file_contains(
      'functions.php',
      "use ClientSite\\Twig\\ThemeTwigHelper;"
    );

    foreach (['BlogPost.php', 'Page.php', 'FrontPage.php'] as $libFile) {
      $this->assertFileExists(
        $this->theme_dir . 'lib/ClientSite/Post/' . $libFile
      );
      $this->assert_theme_file_contains(
        'lib/ClientSite/Post/' . $libFile,
        'namespace ClientSite\\Post;'
      );
    }

    $this->assertFileExists(
      $this->theme_dir . 'lib/ClientSite/Twig/ThemeTwigHelper.php'
    );
    $this->assert_theme_file_contains(
      'lib/ClientSite/Twig/ThemeTwigHelper.php',
      'namespace ClientSite\\Twig;'
    );

    $this->assert_theme_file_contains(
      'views/front-page.twig',
      '{% extends \'layouts/main.twig\' %}'
    );
	}

	public function test_wp_scaffold_groot_command_config_callback_file() {
    $wpCliPath = realpath( __DIR__ . '/../../vendor/bin/wp' );

    $command = "$wpCliPath scaffold groot wp-scaffold-groot-test"
      . ' --quiet'
      . ' --theme_name=Starfruit'
      . ' --theme_uri=https://example.com/starfruit'
      . ' --description="I AM FROOT"'
      . ' --author="Coby Tamayo <ctamayo@sitecrafting.com>"'
      . ' --author_uri=https://www.example.com'
      . ' --company="EvilCorp, Inc."'
      . ' --namespace=ClientSite';

    echo `$command`;

    $this->assert_theme_file_contains(
      'functions.php',
      "  // actual contents of the test file config_callback.inc\n"
        . "  fake_function_call();"
    );
  }

  public function test_wp_scaffold_groot_post_class_command() {
    $this->generate_theme('wp-scaffold-groot-test');

    // test that it creates the directory for us if necessary
    unlink($this->theme_dir . 'lib/ClientSite/Post/BlogPost.php');
    unlink($this->theme_dir . 'lib/ClientSite/Post/FrontPage.php');
    unlink($this->theme_dir . 'lib/ClientSite/Post/Page.php');
    rmdir($this->theme_dir . 'lib/ClientSite/Post/');

    $this->wp('scaffold groot-post-class'
      . ' --quiet'
      . ' --namespace=ClientSite'
      . ' my_post');

    $content = <<<EOF
<?php

namespace ClientSite\Post;

use Conifer\Post\Post;

class MyPost extends Post {
  const POST_TYPE = 'my_post';

  public static function type_options() : array {
    return [
      'public' => true,
    ];
  }
}

?>
EOF;

    $this->assertEquals($content, $this->get_theme_file_contents(
      'lib/ClientSite/Post/MyPost.php'
    ));
  }

  protected function assert_theme_file_contains( string $file, string $needle ) {
    $this->assertContains($needle, $this->get_theme_file_contents($file));
  }

  protected function get_theme_file_contents( string $file ) : string {
    return file_get_contents(
      ABSPATH . 'wp-content/themes/wp-scaffold-groot-test/' . $file
    );
  }

  protected function generate_theme( string $theme ) {
    $this->wp("scaffold groot $theme"
      . ' --quiet'
      . ' --activate'
      . ' --theme_name=Starfruit'
      . ' --theme_uri=https://example.com/starfruit'
      . ' --description="I AM FROOT"'
      . ' --author="Coby Tamayo <ctamayo@sitecrafting.com>"'
      . ' --author_uri=https://www.example.com'
      . ' --company="EvilCorp, Inc."'
      . ' --namespace=ClientSite');
  }

  protected function wp( string $subCommand ) {
    $wpCliPath = realpath( __DIR__ . '/../../vendor/bin/wp' );

    `$wpCliPath $subCommand`;
  }
}
