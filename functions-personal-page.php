<?php
function redirect_users_by_role(){
  $current_user = wp_get_current_user();
  $role_name = $current_user->roles[0];
  if('administrator' != $role_name &&
      $_SERVER['REQUEST_URI'] != "/soli/wp-admin/profile.php" &&
      $_SERVER['REQUEST_URI'] != "/soli/wp-admin/admin-ajax.php" ){
    wp_redirect(get_site_url().'/wp-admin/profile.php');
  }
}
add_action('admin_init','redirect_users_by_role');


function admin_default_page() {
  $current_user = wp_get_current_user();
  $role_name = $current_user->roles[0];
  if('administrator' != $role_name){
    return home_url('/mijn-pagina');
  } else {
    return home_url('/wp-admin/');
  }
}
add_filter('login_redirect', 'admin_default_page');


add_action("wp_ajax_get_agenda_widget", "get_agenda_widget");
add_action("wp_ajax_nopriv_get_agenda_widget","get_agenda_widget");
function get_agenda_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_agenda_widget_nonce")) {
    exit("We are not for hack");
  }

  /*FILTER ON UAM GROUPS IF UAM IS ACTIVATED*/
  include_once( ABSPATH . 'wp-admin/includes/user-access-manager.php' );
  $plugin_active = is_plugin_active( 'user-access-manager/user-access-manager.php' );
  $user_id = wp_get_current_user()->ID;

  $posts = tribe_get_events(array(
      'posts_per_page' => 4,
      'start_date' => date('Y-m-d H:i:s')
  ));

  global $post;
  if($posts){
    foreach ($posts as $post){
      get_template_part( '/template-parts/post', 'excerpt' );
    }
  }
  die();
}
?>
