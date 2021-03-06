<?php

if ( ! defined( 'WP_CLI' ) && ! WP_CLI ) {
	return;
}

use GrootScaffold\GrootScaffoldCommand;

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
  require_once $autoload;
}

WP_CLI::add_command( 'scaffold groot', [GrootScaffoldCommand::class, 'groot'] );
WP_CLI::add_command( 'scaffold groot-post-class', [GrootScaffoldCommand::class, 'groot_post_class'] );
