<?php
require_once(__DIR__ . '/vendor/autoload.php');
$timber = new \Timber\Timber();
if ( ! class_exists( 'Timber' ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php') ) . '</a></p></div>';
	});
	
	add_filter('template_include', function($template) {
		return get_stylesheet_directory() . '/static/no-timber.html';
	});
	
	return;
}

Timber::$dirname = array('templates', 'views');

class StarterSite extends TimberSite {

	function __construct() {
		add_theme_support( 'post-formats' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'menus' );
		add_filter( 'timber_context', array( $this, 'add_to_context' ) );
		add_filter( 'get_twig', array( $this, 'add_to_twig' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts' ) );
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
		add_action( 'init', array( $this, 'register_my_menus') );
		add_action( 'init', array( $this, 'remove_extras') );
		parent::__construct();
	}

	function register_my_menus() {
  register_nav_menus(
    array(
      'primary' => __( 'Primary Menu' ),
      'sidebar' => __( 'Sidebar Menu' ),
      'footer' => __( 'Footer Menu' ),
      'mobile' => __( 'Mobile Menu' )
    )
  );
}

	function register_post_types() {
		//this is where you can register custom post types
	}

	function register_taxonomies() {
		//this is where you can register custom taxonomies
	}

 function loadScripts() {
 		wp_deregister_script( 'jquery' );

    wp_enqueue_script( 'jquery', get_template_directory_uri() . '/app/libs/jquery/dist/jquery.min.js', array(), '', true );
    wp_enqueue_script( 'scripts', get_template_directory_uri() . '/app/js/scripts.min.js', array('jquery'), '', true );
    wp_enqueue_script( 'common-js', get_template_directory_uri() . '/app/js/common.min.js', array('scripts'), '', true );
  } 

	function add_to_context( $context ) {

		$context['primary_menu'] = new TimberMenu('primary');
		$context['sidebar_menu'] = new TimberMenu('sidebar');
		$context['footer_menu'] = new TimberMenu('footer');
		$context['mobile_menu'] = new TimberMenu('mobile');
		// $context['site_menus'] = [
		// 	$context['primary_menu'], $context['sidebar_menu']
		// ];
		$context['site'] = $this;
		return $context;
	}

	function myfoo( $text ) {
		$text .= ' bar!';
		return $text;
	}

	function add_to_twig( $twig ) {
		/* this is where you can add your own functions to twig */
		$twig->addExtension( new Twig_Extension_StringLoader() );
		$twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
		return $twig;
	}




	// REMOVE EXTRAS
function remove_extras() {
  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );

  remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'start_post_rel_link');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
	remove_action('wp_head', 'feed_links', 2);
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
	remove_action( 'template_redirect', 'wp_shortlink_header', 11, 0 );
	remove_action( 'wp_head', 'feed_links_extra', 3 );

	function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
	}

	function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

	// add_filter( 'wpcf7_load_css', '__return_false' );
}

}

new StarterSite();
