<?php

/**
 * GrootScaffoldCommand class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold;

use Scaffold_Command;
use WP_CLI;
use WP_CLI\Extractor;
use WP_CLI\Utils;

use GrootScaffold\Generator\StylesheetGenerator;

/**
 * Generate starter code for a theme based on Groot
 */
class GrootScaffoldCommand extends Scaffold_Command {
  /**
   * See the [Groot website](https://grootthe.me/) for more details.
   *
   * ## OPTIONS
   *
   * <slug>
   * : The slug for the new theme, used for prefixing functions.
   *
   * [--activate]
   * : Activate the newly downloaded theme.
   *
   * [--enable-network]
   * : Enable the newly downloaded theme for the entire network.
   *
   * [--theme_name=<title>]
   * : What to put in the 'Theme Name:' header in 'style.css'.
   *
   * [--theme_uri=<theme_uri>]
   * : What to put in the 'Theme URI:' header in 'style.css'.
   *
   * [--description=<description>]
   * : What to put in the 'Description:' header in 'style.css'.
   *
   * [--author=<full-name>]
   * : What to put in the 'Author:' header in 'style.css'.
   *
   * [--author_uri=<uri>]
   * : What to put in the 'Author URI:' header in 'style.css'.
   *
   * [--force]
   * : Overwrite files that already exist.
   *
   * ## EXAMPLES
   *
   *     # Generate a theme with name "Sample Theme" and author "John Doe"
   *     $ wp scaffold groot example-theme --theme_name="Example Theme" --author="John Doe"
   *     Success: Created theme 'Sample Theme'.
   *
   * @subcommand scaffold groot
   *
   * @when before_wp_load
   */
  public function groot( array $args, array $options ) {
    $slug = $args[0];

    // TODO somehow don't hard-code the version number
    $zipUrl  = 'https://github.com/sitecrafting/groot/archive/v0.1.1.zip';
    $zipFile = Utils\get_temp_dir() . 'groot-' . basename($zipUrl);

    $downloadResponse = Utils\http_request( 'GET', $zipUrl, [], [
      'timeout'  => 600, // 10 minutes ought to be enough??
    ]);
    if ($downloadResponse->status_code === 200) {
      file_put_contents($zipFile, $downloadResponse->body);
    } else {
      WP_CLI::error("Download failed with status code {$downloadResponse->status_code}");
      return false;
    }

    $themeDir = ABSPATH . "wp-content/themes/{$slug}/";
    Extractor::extract($zipFile, $themeDir);

    $lessEntrypointGenerator = new StylesheetGenerator(
      $themeDir . 'style.less',
      $options
    );
    $lessFile = $lessEntrypointGenerator->generate();
    $this->log_generated_file($lessFile);

    $stylesheetGenerator = new StylesheetGenerator(
      $themeDir . 'style.css',
      $options
    );
    $cssFile = $stylesheetGenerator->generate();
    $this->log_generated_file($cssFile);

    WP_CLI::success("TODO generate theme in $themeDir");
    return;
  }

  protected function log_generated_file( string $absolutePath ) {
    $relativePath = str_replace(ABSPATH, '', $absolutePath);
    WP_CLI::log("Generated {$relativePath}");
  }

}
