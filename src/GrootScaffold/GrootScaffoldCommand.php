<?php

/**
 * GrootScaffoldCommand class
 *
 * @copyright 2018 SiteCrafting, Inc.
 * @author    Coby Tamayo <ctamayo@sitecrafting.com>
 */

namespace GrootScaffold;

use WP_CLI;
use Scaffold_Command;

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
	 *     $ wp scaffold _s sample-theme --theme_name="Sample Theme" --author="John Doe"
	 *     Success: Created theme 'Sample Theme'.
   *
   * @subcommand scaffold groot
   *
   * @when before_wp_load
	 */
	public function groot( $args, $options ) {
    $slug = $args[0];

    $stylesheetGenerator = new StylesheetGenerator(
      'style.css',
      $options
    );
    $stylesheetGenerator->generate();

    WP_CLI::success("TODO generate theme in ABSPATH/wp-content/themes/$slug");
    return;
	}
}
