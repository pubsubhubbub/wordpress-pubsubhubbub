<?php
/**
 * Plugin Name: WebSub (FKA. PubSubHubbub)
 * Plugin URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub/
 * Description: A better way to tell the world when your blog is updated.
 * Version: 3.2.1
 * Author: PubSubHubbub Team
 * Author URI: https://github.com/pubsubhubbub/wordpress-pubsubhubbub
 * License: MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: pubsubhubbub
 * Requires PHP: 5.6
 */

namespace Pubsubhubbub;

\defined( 'ABSPATH' ) || exit;

\define( 'PUBSUBHUBBUB_VERSION', '3.2.1' );
\define( 'PUBSUBHUBBUB_PLUGIN_DIR', \plugin_dir_path( __FILE__ ) );
\define( 'PUBSUBHUBBUB_PLUGIN_BASENAME', \plugin_basename( __FILE__ ) );
\define( 'PUBSUBHUBBUB_PLUGIN_FILE', __FILE__ );
\define( 'PUBSUBHUBBUB_PLUGIN_URL', \plugin_dir_url( __FILE__ ) );

// Load the autoloader.
require_once PUBSUBHUBBUB_PLUGIN_DIR . 'includes/class-autoloader.php';

// Load helper functions.
require_once PUBSUBHUBBUB_PLUGIN_DIR . 'includes/functions.php';

// Load deprecated functions for backward compatibility.
require_once PUBSUBHUBBUB_PLUGIN_DIR . 'includes/deprecated.php';

// Register the autoloader.
Autoloader::register_path( __NAMESPACE__, PUBSUBHUBBUB_PLUGIN_DIR . 'includes' );

// Initialize the plugin.
$pubsubhubbub = Pubsubhubbub::get_instance();
$pubsubhubbub->init();

