<?php
/**
 * Recipe Remix
 *
 * @package     Recipe Remix
 * @author      Badabingbreda
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Recipe Remix
 * Plugin URI:  https://www.badabing.nl
 * Description: Recipe Remix for WordPress
 * Version:     1.0.0
 * Author:      Badabingbreda
 * Author URI:  https://www.badabing.nl
 * Text Domain: reciperemix
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

use RecipeRemix\Autoloader;
use RecipeRemix\Init;


if ( defined( 'ABSPATH' ) && ! defined( 'RECIPEREMIX_VERION' ) ) {
 register_activation_hook( __FILE__, 'RECIPEREMIX_check_php_version' );

 /**
  * Display notice for old PHP version.
  */
 function RECIPEREMIX_check_php_version() {
     if ( version_compare( phpversion(), '5.3', '<' ) ) {
        die( esc_html__( 'Recipe Remix plugin requires PHP version 5.3+. Please contact your host to upgrade.', 'reciperemix' ) );
    }
 }

  define( 'RECIPEREMIX_VERSION'   , '1.0.0' );
  define( 'RECIPEREMIX_DIR'     , plugin_dir_path( __FILE__ ) );
  define( 'RECIPEREMIX_FILE'    , __FILE__ );
  define( 'RECIPEREMIX_URL'     , plugins_url( '/', __FILE__ ) );

  define( 'CHECK_RECIPEREMIX_PLUGIN_FILE', __FILE__ );

}

if ( ! class_exists( 'RecipeRemix\Init' ) ) {

  	// Load the composer components that we use
	if ( file_exists( RECIPEREMIX_DIR . 'vendor/autoload.php' ) ) require( RECIPEREMIX_DIR . 'vendor/autoload.php' );

 /**
  * The file where the Autoloader class is defined.
  */

  require_once 'inc/Autoloader.php';
  spl_autoload_register( array( new Autoloader(), 'autoload' ) );

 $reciperemix = new Init();
 // looking for the init hooks? Find them in the Check_Plugin_Dependencies.php->run() callback

}
