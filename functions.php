<?php
/**
 * Soli v2.0 functions and definitions
 *
 * @since Soli 2.0
 * @version 2.0
 */

 function itsme_disable_feed() {
  wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
 }

 add_action('do_feed', 'itsme_disable_feed', 1);
 add_action('do_feed_rdf', 'itsme_disable_feed', 1);
 add_action('do_feed_rss', 'itsme_disable_feed', 1);
 add_action('do_feed_rss2', 'itsme_disable_feed', 1);
 add_action('do_feed_atom', 'itsme_disable_feed', 1);
 add_action('do_feed_rss2_comments', 'itsme_disable_feed', 1);
 add_action('do_feed_atom_comments', 'itsme_disable_feed', 1);

 remove_action( 'wp_head', 'feed_links_extra', 3 );
 remove_action( 'wp_head', 'feed_links', 2 );

 $current_user = wp_get_current_user();
 global $role_name;
 $role_name = null;
 if ( $current_user && $current_user->exists() && ! empty( $current_user->roles ) ) {
    $role_name = $current_user->roles[0];
 }
 if ( in_array( $role_name, array( 'lid', 'subscriber' ), true ) ) {
    add_filter( 'show_admin_bar', '__return_false' );
 }

 add_action('wp_logout','auto_redirect_after_logout');
 function auto_redirect_after_logout(){
   wp_redirect( home_url() );
   exit();
 }

 add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
 function load_dashicons_front_end() {
   wp_enqueue_style( 'dashicons' );
 }

 function register_my_menu() {
   register_nav_menu('header-menu',__( 'Header Menu' ));
 }
 add_action( 'init', 'register_my_menu' );


  function debug_to_console( $data ) {
    echo "<script>console.log( 'Debug Objects: " . json_encode($data) . "' );</script>";
  }

/**
 * import css
 */
function load_styles() {
  wp_enqueue_style('meyer-reset', get_template_directory_uri().'/meyer-reset.css', false, '5.2.0', 'all');
  wp_enqueue_style('style', get_template_directory_uri().'/style.css', false, '5.2.3', 'all');
}

/**
 * JS
 */
function load_scripts() {
  wp_enqueue_script("jquery");
  if(is_home())wp_enqueue_script('recl_js',get_template_directory_uri().'/assets/js/recl-nav.js', false, '5.2.0', 'all');
  if(is_page_template("personal-page.php")){
    wp_enqueue_script('recl_js',get_template_directory_uri().'/assets/js/recl-nav.js', false, '5.2.0', 'all');
    wp_enqueue_script('pers_js',get_template_directory_uri().'/assets/js/personal-page.js', false, '5.2.0', 'all');
  }
  if(!is_admin()){
    wp_enqueue_script('page', get_template_directory_uri().'/assets/js/page.js', false, '1.4.0', 'all');
    wp_enqueue_script('javascript', get_template_directory_uri().'/assets/js/header.js', false, '1.4.0', 'all');
    wp_localize_script( 'javascript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    wp_enqueue_script( 'javascript' );
  }
  wp_enqueue_script('modernizr', get_template_directory_uri().'/assets/js/modernizr.js', false, '5.2.0', 'all');
}

function my_admin_enqueue($hook_suffix) {
  if($hook_suffix=='post-new.php'||$hook_suffix=='post.php')
    wp_enqueue_script('javascript',get_template_directory_uri().'/assets/js/post_requirements.js', false, '5.2.0', 'all');
    wp_enqueue_script('bookings-admin',get_template_directory_uri().'/assets/js/bookings-admin.js', false, '5.2.0', 'all');
}

/**
 * import assets
 */
add_action('admin_enqueue_scripts', 'my_admin_enqueue');
add_action( 'wp_enqueue_scripts', 'load_styles' );
add_action( 'wp_enqueue_scripts', 'load_scripts', 99 );

function limit_excerpt($source,$limit,$echo = true){
  if(strlen($source)>$limit){
    $excerpt = substr($source, 0, $limit);
    $excerpt = $excerpt.'...';
  } else {
    $excerpt = $source;
  }
  if ($echo){
    echo $excerpt;
  } else {
    return $excerpt;
  }
}

/**
 * warp element in parent element
 */
function wrap_element($info,$wrap){
  echo "<".$wrap.">".$info."</".$wrap.">";
}

add_filter( 'auth_cookie_expiration', 'wploop_never_log_out' );
function wploop_never_log_out( $expirein ) {
    return 10518975; // 40+ years shown in seconds
}

/**
 * Filter the except length
 */
function custom_excerpt_length( $length ) {
  return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

/**
 * removes ugly dots
 */
function wpdocs_excerpt_more( $more ) {
  return '...';
}
add_filter( 'excerpt_more', 'wpdocs_excerpt_more' );

/**
 * echo's child pages if excists
 */
function get_child_pages($pageid = null){
  global $post;
  if ($pageid) {
    $children = get_pages(array(
      'parent' => $pageid
    ));
  } else {
    $children = get_pages(array(
      'parent' => $post->ID
    ));
  }
  foreach ($children as $post) {
    setup_postdata($post);
    get_template_part( 'template-parts/item', 'flat-excerpt' );
  }
  wp_reset_postdata();
}

function get_other_posts(){
  global $post, $myrows;
  $args = array('numberposts' => 6, 'exclude' => $myrows);
  $posts = get_posts($args);
  foreach ($posts as $post) {
    get_template_part('template-parts/post','excerpt');
  }
}

function get_event_posts(){
  global $wpdb;
  global $post;
  global $myrows;
  global $role_name;
  $userid = wp_get_current_user()->ID;
  if($role_name==="lid"||$role_name==="author"||!is_user_logged_in()){
    $myrows = get_myrows();
  }
  if(function_exists('tribe_get_events')){
    $events = tribe_get_events(array(
      'posts_per_page' => 6,
      'start_date' => date('Y-m-d H:i:s'),
      'post__not_in' => $myrows
    ));
    if($events){
      foreach ($events as $post) {
        get_template_part( 'template-parts/post', 'excerpt' );
      }
    } else{
      get_template_part( 'template-parts/post/content', 'none');
    }
  } else {
    echo "Tribe Event Calendar plugin is not installed.";
  }
}

add_post_type_support( 'page', 'excerpt' );


/**
 * Check if User Access Manager plugin is active
 */
function soli_is_uam_active() {
  if ( ! function_exists( 'is_plugin_active' ) ) {
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
  }
  return is_plugin_active( 'user-access-manager/user-access-manager.php' );
}

/**
 * load function file for search element
 */
require_once get_parent_theme_file_path('/theme-config/search/functions.php');

/**
 * load function file for advertisement
 */
require_once get_parent_theme_file_path('/theme-config/advertisement/functions.php');

/**
 * load function file for admin page
 */
require_once get_parent_theme_file_path('/theme-config/admin/functions.php');

/**
 * load function file for login page
 */
require_once get_parent_theme_file_path('/theme-config/login/functions.php');

/**
 * load function file for personal page
 */
require_once get_parent_theme_file_path('/theme-config/personal-page/functions.php');

/**
 * load function file for messages
 */
require_once get_parent_theme_file_path('/theme-config/personal-page/messages.php');

/**
 * load function file for tv messages
 */
require_once get_parent_theme_file_path('/theme-config/tv/functions.php');

/**
 * load function file for ical feed
 */
require_once get_parent_theme_file_path('/theme-config/ical/functions.php');

/**
 * load function file for reserveer plugin tribe_events
 */
require_once get_parent_theme_file_path('/tribe-events/bookings.php');

/**
 * load function file for reserveer plugin tribe_events
 */
require_once get_parent_theme_file_path('/theme-config/user/middle_name.php');

/**
 * Initialize GitHub theme updater.
 */
function soli_theme_github_updater() {
	include_once get_template_directory() . '/updater.php';

	if ( class_exists( 'Soli\\ThemeSoli\\WP_GitHub_Theme_Updater' ) ) {
		$config = array(
			'slug'         => 'wp-theme-soli',
			'api_url'      => 'https://api.github.com/repos/JoranOut/wp-theme-soli',
			'raw_url'      => 'https://raw.githubusercontent.com/JoranOut/wp-theme-soli/master',
			'github_url'   => 'https://github.com/JoranOut/wp-theme-soli',
			'zip_url'      => 'https://github.com/JoranOut/wp-theme-soli/releases/latest/download/wp-theme-soli.zip',
			'requires'     => '6.0.0',
			'tested'       => '6.7.0',
			'requires_php' => '8.4',
			'readme'       => 'README.md',
		);

		new \Soli\ThemeSoli\WP_GitHub_Theme_Updater( $config );
	}
}
add_action( 'init', 'soli_theme_github_updater' );
