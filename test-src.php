<?php

use Groot\PluginManager;
use Conifer\Site;

/* @groot use_project_classes */

// autoload...

// Require that certain classes be loaded (presumably by plugins)
$pluginManager = new PluginManager();
if (!$pluginManager->require_classes([
  '\Timber\Site',
  '\Conifer\Site',
  /*
   * @groot required_classes
   */
])) {
  return;
}

// Build out the site.
// Put WordPress configurations, such as filter and action hooks,
// inside the config function passed to Site::configure().
$site = new Site();
$site->configure(function() {

  /*
   * @groot config_callback
   */

});
