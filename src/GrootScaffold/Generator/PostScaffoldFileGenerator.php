<?php

/**
 * PostScaffoldFileGenerator class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold\Generator;

use GrootScaffold\TokenHandler\TokenHandlerFactory;
use GrootScaffold\TokenHandler\GrootTokenHandler;
use GrootScaffold\TokenHandler\NamespaceTokenHandler;
use GrootScaffold\TokenHandler\TransparentTokenHandler;

/**
 * Generate a custom theme stylesheet
 */
class PostScaffoldFileGenerator extends FileGenerator {
  const TEMPLATE = <<<EOF
<?php

namespace PROJECT_NAMESPACE\Post;

use Conifer\Post\Post;

class POST_CLASS extends Post {
  const POST_TYPE = 'POST_TYPE_CONST';

  public static function type_options() : array {
    return [
      'public' => true,
    ];
  }
}

?>
EOF;

  public function __construct(array $options) {
    parent::__construct('', $options);
  }

  public function replace_contents($contents) {
    foreach ($this->get_replacements() as $k => $v) {
      $contents = str_replace($k, $v, $contents);
    }

    return $contents;
  }

  public function get_replacements() : array {
    return [
      'POST_CLASS'        => $this->get_post_class(),
      'POST_TYPE_CONST'   => $this->options['post_type'],
      'PROJECT_NAMESPACE' => $this->options['namespace'],
    ];
  }

  public function get_path() : string {
    return sprintf(
      '%s/lib/%s/Post/%s.php',
      $this->options['theme_dir'],
      $this->options['namespace'],
      $this->get_post_class()
    );
  }

  protected function get_post_class() : string {
    if (!empty($this->options['post_class'])) {
      return trim($this->options['post_class']);
    }

    $words = explode('_', $this->options['post_type']);
    return array_reduce($words, function(string $class, string $word) {
      return $class . ucfirst(strtolower(trim($word)));
    }, '');
  }

  protected function get_contents() : string {
    return static::TEMPLATE;
  }
}

?>
