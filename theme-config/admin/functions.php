<?php
/**
 * File to handle standard imaging
 */

/**
 * Create admin page to handle default images
 **/
add_action( 'admin_menu', 'my_admin_menu' );
function my_admin_menu() {
	add_menu_page( 'Thema instellingen', 'Thema instellingen', 'manage_options', 'theme/soli.php', 'soli_theme_admin_page', 'dashicons-admin-customizer',90 );
  add_submenu_page('theme/soli.php','Voorpagina','Voorpagina','manage_options', 'theme/soli/voorpagina.php','soli_theme_admin_frontpage_page');
  add_submenu_page('theme/soli.php','Afbeeldingen','Afbeeldingen','manage_options', 'theme/soli/afbeeldingen.php','soli_theme_admin_image_page');
}

add_action('admin_head', 'my_custom_admin_css');
function my_custom_admin_css() {
  echo '<style>';
  include 'theme.css';
  echo '</style>';
}

function soli_theme_admin_frontpage_page(){
    include 'views/frontpage.php';
}

function soli_table_exists(){
  global $wpdb;
  $table_name = $wpdb->prefix . "soli_imaging";
  if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    return false;
  }
  return true;
}

function create_soli_table(){
  define("ABSPATH");

  global $wpdb;
  $table_name = $wpdb->prefix . "soli_imaging";
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    type tinytext NOT NULL,
    object tinytext NOT NULL,
    info tinytext DEFAULT '' NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

function get_soli_fp_info(){
  global $wpdb;
  $table_name = $wpdb->prefix . "soli_imaging";
  $result = $wpdb->get_results("
  SELECT * FROM $table_name
  WHERE type=\"frontpage\";","ARRAY_N");
  $array = array();
  for ($i=0; $i < count($result); $i++) {
    $array[$result[$i][2]] = $result[$i][3];
  }
  return $array;
}

function get_soli_groups() {
  global $wpdb;
  $table_name = $wpdb->prefix . "uam_accessgroups";
  $table_name_second = $wpdb->prefix . "soli_imaging";
  $out_array = $wpdb->get_results("
  SELECT U.ID, U.groupname, I.info image
  FROM $table_name U
  LEFT JOIN $table_name_second I
  ON U.ID = I.object AND I.type = \"image\"
  ORDER BY U.ID ASC;","ARRAY_N");
  for ($i=0; $i < count($out_array); $i++) {
    $out_array[$i][2] = json_decode($out_array[$i][2]);
  }
  return $out_array;
}

function get_soli_post_image($post, $size = 'large'){
 $image = get_the_post_thumbnail_url($post->ID, $size);
 if($image){
   return $image;
 } else {
   return standard_imaging($post, $size);
 }
}

function frontpage_event_option($post){
    return "<input type=\"radio\" name=\"post\"
        id=\"".$post->ID."\"
        class=\"select_event\"
        data-url=\"".$post->guid."\"
        data-subtitle=\"".get_group($post)."\"
        data-image=\"".get_soli_post_image($post)."\"
        data-imageid=\"".get_image_id(get_soli_post_image($post))."\"
        data-date=\"".$post->event_date."\"
        value=\"".$post->ID."\">
        <label for=\"".$post->ID."\">
        <span>".date("d M",strtotime($post->event_date))."</span>
        ".$post->post_title."
        </label>";
}

function soli_theme_admin_image_page(){
  include 'views/imagepage.php';
 }

function soli_theme_admin_include_jq() {
  global $post;
  if(!(get_current_screen()->base === "thema-instellingen_page_theme/soli/voorpagina" ||
    get_current_screen()->base === "thema-instellingen_page_theme/soli/afbeeldingen")) {
      return;
    }
  if ( ! did_action( 'wp_enqueue_media' ) ) {
 		wp_enqueue_media();
 	}
  	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/assets/js/theme-admin.js', array('jquery'), null, false );
    wp_localize_script( 'myuploadscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
    wp_enqueue_script( 'myuploadscript' );
 }
 add_action( 'admin_enqueue_scripts', 'soli_theme_admin_include_jq' );

function get_image_id($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%s';", $image_url ));
  return $attachment[0];
}

function get_group($post){
  global $prep;
  if(!$prep){
    $prep = get_soli_groups();
  }
  for ($i=0; $i < count($prep); $i++) {
    if(check_preg($prep[$i][1],$post->post_title)||check_preg($prep[$i][1],$post->post_content))
      return $prep[$i][1];
  }
  return $post->post_title;
}

function standard_imaging($post, $size = 'large'){
  global $prep;
  if(!$prep){
    $prep = get_soli_groups();
  }
  if(!$post){
    $fp_info = get_soli_fp_info();
    return wp_get_attachment_image_src($fp_info['frontpage_background'],$size)[0];
  }
  for ($i=0; $i < count($prep); $i++) {
    if(check_preg($prep[$i][1],$post->post_title)||check_preg($prep[$i][1],$post->post_content))
      return take_image($i,$post->ID, $size);
  }
  return take_image(null,$post->ID, $size);
}

function check_preg($preg_str, $content){
  $preg_str = "/".$preg_str."/i";
  if(preg_match($preg_str,$content,$group))
    return true;
  return false;
}

function take_image($group, $postid, $size = 'large'){
  global $prep;
  if(!$group)$group=0;
  $ii = 0;
  if(count($prep[$group][2])){
    $ii = $postid % count($prep[$group][2]);
  }
  $image = wp_get_attachment_image_src($prep[$group][2][$ii],$size);
  if($image){
    return $image[0];
  } else {
    $image = wp_get_attachment_image_src($prep[0][2][0],$size);
    if($image){
      return $image[0];
    } else {
      return get_template_directory_uri().'/assets/img/screenshot.png';
    }
  }
}

function default_image_uploader_field($name, $value = ''){
  $image = ' button">Upload image';
  $image_size = 'full';
  $display = 'none';

  if( $image_attributes = wp_get_attachment_image_src($value,$image_size)){
    $image = '"><img src="' . $image_attributes[0] . '" style="width:300px;display:block;" />';
		$display = 'inline-block';
  }

  return '
	<div>
		<a href="#" class="default_image_button' . $image . '</a>
		<input type="hidden" class="image" name="' . $name . '" id="' . $name . '" value="' . $value . '" init-value="' . $value . '" autocomplete="off" />
		<a href="#" class="default_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
	</div>';
}

function default_image_print_box($object_id, $info) {
  echo default_image_uploader_field( $object_id, $info);
}

require_once "ajax.php";
?>
