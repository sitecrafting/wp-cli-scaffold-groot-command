<?php

/**
 * GrootScaffoldCommand class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold;

use WP_CLI;
use WP_CLI_Command;
use WP_CLI\Extractor;
use WP_CLI\Utils;

use GrootScaffold\Generator\StylesheetGenerator;
use GrootScaffold\Generator\LibraryFileGenerator;
use GrootScaffold\Generator\PostScaffoldFileGenerator;

/**
 * Generate starter code for a theme based on Groot
 */
class GrootScaffoldCommand extends WP_CLI_Command {
  const THEME_FILE_GENERATOR_MAP = [
    'style.less'          => StylesheetGenerator::class,
    'style.css'           => StylesheetGenerator::class,
    'functions.php'       => LibraryFileGenerator::class,
    'BlogPost.php'        => LibraryFileGenerator::class,
    'Page.php'            => LibraryFileGenerator::class,
    'FrontPage.php'       => LibraryFileGenerator::class,
    'ThemeTwigHelper.php' => LibraryFileGenerator::class,
  ];

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
   * : The version of Groot to install. Defaults to the latest.
   * TODO don't hard code this
   * ---
   * default: latest
   * ---
   *
   * [--config_callback=<config_callback>]
   * : The code to put in the Site config callback
   * @see https://coniferplug.in/GLOSSARY.html#config-callback
   * ---
   * default: ''
   * ---
   *
   * ## EXAMPLES
   *
   *     # Generate a theme with name "Example Theme" and author "John Doe"
   *     $ wp scaffold groot example-theme \
   *         --theme_name="Example Theme" \
   *         --author="John Doe"
   *         --theme_name=Starfruit'
   *         --theme_uri=https://example.com/starfruit'
   *         --description="I AM FROOT"'
   *         --author="Coby Tamayo <ctamayo@sitecrafting.com>"'
   *         --author_uri=https://www.example.com'
   *         --company="EvilCorp, Inc."'
   *         --namespace=ClientSite'
   *         --config_callback="// some config code"';
   *     Success: Created theme 'Example Theme'.
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

    /*
     * Unless the --config_callback option is set explicitly,
     * look for a special file to include in the Conifer
     * config callback in functions.php
     * @see https://coniferplug.in/GLOSSARY.html#config-callback
     */
    if (
      empty($options['config_callback'])
      && file_exists('config_callback.inc')
    ) {
      $options['config_callback'] = file_get_contents('./config_callback.inc');
    }

    $this->generate_theme_file($themeDir . 'less/style.less', $options);
    $this->generate_theme_file($themeDir . 'style.css', $options);
    $this->generate_theme_file($themeDir . 'functions.php', $options);

    // rename Project namespace directory
    $libDir = $this->generate_project_namespace_dir($themeDir, $options);

    // generate library files
    $libFiles = $this->rglob($libDir . '**/*.php');
    foreach ($libFiles as $file) {
      $this->generate_theme_file($file, $options);
    }

    if (!empty($options['activate'])) {
      WP_CLI::runcommand('theme activate ' . basename($themeDir));
    }

    WP_CLI::success(sprintf(
      '%s is now installed at %s',
      $options['theme_name'],
      $themeDir
    ));

    return;
  }


  /**
   * See the [Groot website](https://grootthe.me/) for more details.
   *
   * ## ARGUMENTS
   *
   * <post_type>
   * : The post_type to register.
   *
   * ## OPTIONS
   *
   * --namespace=<project_namespace>
   * : The PHP namespace for your theme's lib classes. Required.
   *
   * [--post_class=<post_class>]
   * : Class name to declare for representing your post_type. Defaults to the value of post_type, CamelCased.
   *
   * [--force]
   * : Overwrite files that already exist.
   *
   * ## EXAMPLES
   *
   *     # Generate a class with name "MyPost" declaring the post_type "my_post"
   *     $ wp scaffold groot-post-class my_post --namespace=ClientSite
   *     Success: Created class 'ClientSite\Post\MyPost'.
   *
   *     # Generate a class with name "MySpecialPost" declaring the post_type "my_post"
   *     $ wp scaffold groot-post-class my_post --post_class=MySpecialPost --namespace=ClientSite
   *     Success: Created class 'ClientSite\Post\MySpecialPost'.
   *
   * @subcommand scaffold groot-post-class
   *
   * @when after_wp_load
   */
  public function groot_post_class(array $args, array $options) {
    $generator = new PostScaffoldFileGenerator(array_merge($options, [
      'post_type' => $args[0],
      'theme_dir' => get_template_directory(),
    ]));

    $path = $generator->generate();

    return;
  }

  /**
   * Generate a theme file at the given path
   */
  protected function generate_theme_file(
    string $path,
    array $options
  ) {
    $class         = static::THEME_FILE_GENERATOR_MAP[basename($path)];
    $generator     = new $class($path, $options);
    $generatedFile = $generator->generate();

    $this->log_generated_file($generatedFile);
  }

  /**
   * Generate a lib subdir for the given namespace and return its path
   *
   * @param string $themeDir
   * @param array $options
   * @return string
   */
  protected function generate_project_namespace_dir(
    string $themeDir,
    array $options
  ) : string {
    $libDir = "{$themeDir}lib/{$options['namespace']}/";

    rename("{$themeDir}/lib/Project", $libDir);

    $this->log_generated_file($libDir);

    return $libDir;
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
      WP_CLI::error("Failed to get Groot release versions with status code {$releasesResponse->status_code}");
      return '';
    }

    $releases = json_decode($releasesResponse->body, true);

    return $releases[0]['zipball_url'] ?? '';
  }

  /**
   * Recursively look for files, honoring the "/**(slash)" wildcard for
   * arbitrary directory depth
   */
  protected function rglob ($pattern, $flags = 0) {
    // Keep away the hassles of the rest if we don't use the wildcard anyway
    if (strpos($pattern, '/**/') === false) {
        return glob($pattern, $flags);
    }

    $patternParts = explode('/**/', $pattern);

    // Get sub dirs
    $upperDir = array_shift($patternParts);
    $dirs = glob($upperDir . '/*', GLOB_ONLYDIR | GLOB_NOSORT);

    // Get files for current dir
    $files = glob($pattern, $flags);

    foreach ($dirs as $dir) {
      // Conjoin subdirectory wildcards back together and recurse within them
      $subDirContent = $this->rglob(
        $dir . '/**/' . implode('/**/', $patternParts),
        $flags
      );

      $files = array_merge($files, $subDirContent);
    }

    return $files;
	}

}
