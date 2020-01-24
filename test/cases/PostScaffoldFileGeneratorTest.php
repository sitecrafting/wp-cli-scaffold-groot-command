<?php

/**
 * GrootScaffoldTest\PostScaffoldFileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffoldTest;

use GrootScaffold\Generator\PostScaffoldFileGenerator;
use PHPUnit\Framework\TestCase;
use WP_CLI;

/**
 * Test the PostScaffoldFileGeneratorTest class
 *
 * @group unit
 */
class PostScaffoldFileGeneratorTest extends TestCase {
  public function test_replace_contents() {
    $gen = new PostScaffoldFileGenerator([
      'post_type' => 'my_post',
      'namespace' => 'SpecialProject',
    ]);

    $this->assertContains(
      'namespace SpecialProject\Post;',
      $gen->replace_contents(PostScaffoldFileGenerator::TEMPLATE)
    );
  }

  public function test_get_replacements() {
    $gen = new PostScaffoldFileGenerator([
      'post_type' => 'my_post',
      'namespace' => 'SpecialProject',
    ]);

    $this->assertEquals([
      'POST_CLASS'        => 'MyPost',
      'POST_TYPE_CONST'   => 'my_post',
      'PROJECT_NAMESPACE' => 'SpecialProject',
    ], $gen->get_replacements());
  }

  public function test_get_replacements_post_class_default() {
    $gen = new PostScaffoldFileGenerator([
      'post_type'  => 'my_post',
      'namespace'  => 'SpecialProject',
    ]);

    $this->assertEquals('MyPost', $gen->get_replacements()['POST_CLASS']);
  }

  public function test_get_replacements_post_class() {
    $gen = new PostScaffoldFileGenerator([
      'post_type'  => 'my_post',
      'post_class' => 'MySpecialPost',
      'namespace'  => 'SpecialProject',
    ]);

    $this->assertEquals('MySpecialPost', $gen->get_replacements()['POST_CLASS']);
  }

  public function test_get_replacements_post_class_whitespace() {
    $gen = new PostScaffoldFileGenerator([
      'post_type'  => 'asdf',
      'post_class' => '   MySpecialPost   ',
      'namespace'  => 'SpecialProject',
    ]);

    $this->assertEquals('MySpecialPost', $gen->get_replacements()['POST_CLASS']);
  }

  public function test_get_replacements_post_class_default_whitespace() {
    $gen = new PostScaffoldFileGenerator([
      'post_type'  => '  my_post  ',
      'namespace'  => 'SpecialProject',
    ]);

    $this->assertEquals('MyPost', $gen->get_replacements()['POST_CLASS']);
  }

  public function test_get_path() {
    $gen = new PostScaffoldFileGenerator([
      'post_type'  => 'my_post',
      'namespace'  => 'SpecialProject',
      'theme_dir'  => ABSPATH . 'wp-content/themes/my-theme',
    ]);

    $this->assertEquals(
      ABSPATH . 'wp-content/themes/my-theme/lib/SpecialProject/Post/MyPost.php',
      $gen->get_path()
    );
  }
}
