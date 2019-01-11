<?php

if ( !defined('ABSPATH') && is_dir( __DIR__ . '/../wp/' ) ) {
  // if ./wp doesn't exist, that means we haven't install wp core yet
  define( 'ABSPATH', realpath(__DIR__ . '/../wp/') . '/' );
}

require __DIR__ . '/../vendor/autoload.php';
