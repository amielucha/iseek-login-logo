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
 * Version:           1.1.0
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
 * TODO:
 * - maybe look for logo.jpg or logo.png in the site's folder?
 * - verify that it works with WP version 4.3
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

	      <?php if (get_theme_mod( 'login_bg' )): ?>
	        body.login {
	        	background-color: <?php echo get_theme_mod( 'login_bg' ) ?>;
	        }
        <?php endif ?>

				<?php if (get_theme_mod( 'login_bg_text' )): ?>
	        body.login #backtoblog a, body.login #nav a {
	        	color: <?php echo get_theme_mod( 'login_bg_text' ) ?>;
	        }
        <?php endif ?>

        <?php if (get_theme_mod( 'login_bg_text_hover' )): ?>
	        body.login #backtoblog a:hover, body.login #nav a:hover,
	        body.login #backtoblog a:active, body.login #nav a:active {
	        	color: <?php echo get_theme_mod( 'login_bg_text_hover' ) ?>;
	        }
        <?php endif ?>

        <?php if (get_theme_mod( 'login_primary' )): ?>
	        body.login .button-primary {
	        	background-color: <?php echo get_theme_mod( 'login_primary' ) ?>;
	        }

	        body.login .button-primary,
	        body.login .button-primary:hover,
	        body.login .button-primary:active,
	        body.login input[type=text]:focus,
	        body.login input[type=password]:focus,
	        body.login input[type=checkbox]:focus {
	        	border-color: <?php echo get_theme_mod( 'login_primary' ) ?>;
	        }

	        body.login input[type=text]:focus,
	        body.login input[type=password]:focus,
	        body.login input[type=checkbox]:focus {
	        	box-shadow: <?php echo get_theme_mod( 'login_primary' ) ?>  0px 0px 2px 0px;
	        }

	        input[type=checkbox]:checked:before {
	        	color: <?php echo get_theme_mod( 'login_primary' ) ?>;
	        }
        <?php endif ?>

        <?php if (get_theme_mod( 'login_primary_hover' )): ?>
	        body.login .button-primary:hover,
	        body.login .button-primary:active {
	        	background-color: <?php echo get_theme_mod( 'login_primary_hover' ) ?>;
	        }
        <?php endif ?>

        }
    </style>
    <?php echo get_theme_mod( 'login_bg' ); ?>
<?php }
add_action( 'login_enqueue_scripts', 'baseek_login_logo_styles' );

function baseek_login_logo_change_url() { ?>
	<script type="text/javascript">
		window.onload = function() {
			// Change logo URL
			var logo = document.querySelector('#login > h1 > a');
			logo.setAttribute('href', '<?php echo home_url("/") ?>');
			logo.setAttribute('title', 'Back to homepage');
		};
	</script>
<?php
} add_action( 'login_enqueue_scripts', 'baseek_login_logo_change_url' );

function login_logo_customize($wp_customize) {
	/*
	 * Adds login background image to the Customizer
	 */

	// Add Customizer Section
	$wp_customize->add_section( 'login_logo_section', array(
	    'title'          => 'Login Screen',
	    'priority'       => 900,
	) );

	/*
	 * Background Color
	 */
	// Add the setting
	$wp_customize->add_setting( 'login_bg', array(
	    'default'        => '#F1F1F1',
	) );

	// Add the color picker
	$wp_customize->add_control( 'login_bg', array(
	    'label'   => 'Background',
	    'section' => 'login_logo_section',
	    'type'    => 'color',
	) );

	/*
	 * Color of the text set on the background
	 */
	// Add the setting
	$wp_customize->add_setting( 'login_bg_text', array(
	    'default'        => '#999',
	) );

	// Add the color picker
	$wp_customize->add_control( 'login_bg_text', array(
	    'label'   => 'Background Text',
	    'section' => 'login_logo_section',
	    'type'    => 'color',
	) );

	// Add the setting
	$wp_customize->add_setting( 'login_bg_text_hover', array(
	    'default'        => '#999',
	) );

	// Add the color picker
	$wp_customize->add_control( 'login_bg_text_hover', array(
	    'label'   => 'Background Text Hover',
	    'section' => 'login_logo_section',
	    'type'    => 'color',
	) );

	/*
	 * Primary color for buttons and outlines
	 */
	// Add the setting
	$wp_customize->add_setting( 'login_primary', array(
	    'default'        => '#0091cd',
	) );

	// Add the color picker
	$wp_customize->add_control( 'login_primary', array(
	    'label'   => 'Primary Colour',
	    'section' => 'login_logo_section',
	    'type'    => 'color',
	) );

	// Add the setting
	$wp_customize->add_setting( 'login_primary_hover', array(
	    'default'        => '#0073aa',
	) );

	// Add the color picker
	$wp_customize->add_control( 'login_primary_hover', array(
	    'label'   => 'Primary Colour Hover',
	    'section' => 'login_logo_section',
	    'type'    => 'color',
	) );

}

add_action('customize_register', 'login_logo_customize');
