<?php

/**
 *
 * @link              http://iseek.ie
 * @since             1.0.0
 * @package           iseek-login-logo
 *
 * @wordpress-plugin
 * Plugin Name:       iSeek Login Logo
 * Plugin URI:        https://github.com/amielucha/baSeek
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Slawek Amielucha @iseek.ie
 * Author URI:        https://github.com/amielucha
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iseek-login-logo
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
 * TODO: develop as a plugin, post to Github
 * - maybe look for logo.jpg or logo.png in the site's folder?
 */

/*
 * Let Jetpack manage site's logo.
 * Requires Jetpack plugin (http://jetpack.me).
 */
if ( !get_theme_support( 'site-logo' ) ) {
	$jetargs = array(
	    'header-text' => array(
	        'site-title',
	        'site-description',
	    ),
	    'size' => 'full',
	);
	add_theme_support( 'site-logo', $jetargs );
}

/*
 * Returns iSeek logo URL
 */
function baseek_get_iseek_logo() {
  return plugin_dir_url( __FILE__ )."images/iseek-logo.svg";
}

function baseek_get_logo_height() {
	/*
	 * Returns a CSS string value of the logo height if the site logo is specified.
	 */
  if ( function_exists('site_logo') ) {
  	$logo_h = null;

  	// Load medium size image if it exists
  	if ( isset( site_logo()->logo['sizes']['medium']['width'] , site_logo()->logo['sizes']['medium']['height'] ) )
  		$logo_h = intval( 320 / site_logo()->logo['sizes']['medium']['width'] * site_logo()->logo['sizes']['medium']['height'] );

  	// Load fullsize image. Probably the logo is small or is an SVG image
  	elseif ( isset( site_logo()->logo['sizes']['full']['width'] , site_logo()->logo['sizes']['full']['height'] ) )
  		$logo_h = intval( 320 / site_logo()->logo['sizes']['full']['width'] * site_logo()->logo['sizes']['full']['height'] );

  	// Set logo height
  	if ($logo_h > 1)
  		return $logo_h . "px";
  	else
  		return "120px";
  }
}

function baseek_get_login_logo() {
	/*
	 * Returns the site's logo. If no logo has been set returns iSeek logo.
	 */
	if ( function_exists( 'jetpack_has_site_logo' ) && function_exists('site_logo') ) {
		if ( jetpack_has_site_logo() ) {
			if ( isset( site_logo()->logo['sizes']['medium']['url'] ) )
				return site_logo()->logo['sizes']['medium']['url'];
			else
				return site_logo()->logo['url'];
		} else {
			return baseek_get_iseek_logo();
		}
	}	else {
		return baseek_get_iseek_logo();
	}
}

function baseek_login_logo_styles() { ?>
	<?php
		/*
		 * Prints CSS on wp-login.php
		 */

		// Uncomment for debug information:
			//var_dump(site_logo()->logo);

	?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo baseek_get_login_logo(); ?>);
            background-size: contain;
            width: 100%;
            height: <?php echo baseek_get_logo_height() ?>;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'baseek_login_logo_styles' );
