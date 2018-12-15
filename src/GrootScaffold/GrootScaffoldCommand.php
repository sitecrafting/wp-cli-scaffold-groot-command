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
   * Where to go to look for the list of releases
   *
   * @var string
   */
  const GITHUB_RELEASES_ENDPOINT = 'https://api.github.com/repos/sitecrafting/groot/releases';

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
   * [--company=<company_name>]
   * : What to put in the copyright language. If left blank, copyright is stripped.
   *
   * [--namespace=<project_namespace>]
   * : The PHP namespace for your theme's lib classes
   *
   * [--force]
   * : Overwrite files that already exist.
   *
   * [--version=<version>]
   * : The version of Groot to install. Defaults to the latest, currently v0.1.2
   * TODO don't hard code this
   * ---
   * default: latest
   * ---
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

    $themeDir = $this->download_groot_starter( $slug, $options['version'] );
    if (empty($themeDir)) {
      WP_CLI::error('Something went wrong downloading Groot!');
      return false;
    }

    $lessEntrypointGenerator = new StylesheetGenerator(
      $themeDir . 'less/style.less',
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

    $libDir = "{$themeDir}lib/{$options['namespace']}/";
    rename("{$themeDir}/lib/Project", $libDir);
    $this->log_generated_file($libDir);

    if (!empty($options['activate'])) {
      WP_CLI::runcommand('theme activate ' . basename($themeDir));
    }

    WP_CLI::success(sprintf(
      'Your new theme, %s, is installed at %s',
      $options['theme_name'],
      $themeDir
    ));

    return;
  }

  /**
   * Download the theme and place it in a new theme directory called `$slug`
   *
   * @param string $slug the new theme slug
   * @param string $version the version of Groot to download
   * @return string the absolute directory of the generated theme, or the empty
   * string on failure.
   */
  protected function download_groot_starter(
    string $slug,
    string $version
  ) : string {
    $zipUrl = $this->get_github_release_url( $version );
    if (empty($zipUrl)) {
      return '';
    }

    $zipFile = Utils\get_temp_dir() . 'groot-' . basename($zipUrl) . '.zip';

    $downloadResponse = Utils\http_request( 'GET', $zipUrl, [], [
      'timeout'  => 120,
    ]);
    if ($downloadResponse->status_code === 200) {
      file_put_contents($zipFile, $downloadResponse->body);
    } else {
      WP_CLI::error("Download failed with status code {$downloadResponse->status_code}");
      return '';
    }

    $themeDir = ABSPATH . "wp-content/themes/{$slug}/";
    mkdir($themeDir);
    Extractor::extract($zipFile, $themeDir);

    return $themeDir;
  }

  protected function log_generated_file( string $absolutePath ) {
    $relativePath = str_replace(ABSPATH, '', $absolutePath);
    WP_CLI::log("Generated {$relativePath}");
  }

  protected function get_github_release_url( string $version ) : string {
    $releasesResponse = Utils\http_request(
      'GET',
      static::GITHUB_RELEASES_ENDPOINT,
      ['Accept: application/json'],
      ['timeout' => 120]
    );
    if ($releasesResponse->status_code !== 200) {
      WP_CLI::error("Failed to get Groot releaseversions with status code {$releasesResponse->status_code}");
      return '';
    }

    $releases = json_decode($releasesResponse->body, true);

    return $releases[0]['zipball_url'] ?? '';
  }

}
