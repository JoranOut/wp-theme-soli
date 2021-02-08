<?php
/**
 * Function file to customize login pagewhile stil using standard
 * login page for security and stability
 */

/**
 * Hook style.css to login page
 */
function add_login_stylesheet(){
  wp_enqueue_style('custom-login',get_stylesheet_directory_uri().'/style.css');
}
add_action('login_enqueue_scripts','add_login_stylesheet');

function set_login_header_image(){
  $fp_info = get_soli_fp_info();
  $image = wp_get_attachment_image_src($fp_info['frontpage_background'],'full');
  echo '<style type="text/css">
    body.login {
      background-image: linear-gradient(-135deg, rgba(170, 42, 42, .8) 0%, rgba(75, 33, 191, .8) 100%), url(\''.$image[0].'\');
      background-position:center;
    }
    a.login_background {
      position: absolute;
      bottom: 10px;
      right: 20px;
      color: rgba(255,255,255,0.666);
      text-decoration: none;
    }
    a.login_background:hover {
      color: rgba(255,255,255,1);
    }
    body.login h1 > a {
      background-image: url(' . get_template_directory_uri() . '/assets/img/logo_full.png) !important;
      margin: 0 auto;
      background-size: contain;
      width:100%;
    }
  </style>';
}
add_action('login_head', 'set_login_header_image');

function set_header_image_url() {
  return get_home_url();
}
add_filter('login_headerurl', 'set_header_image_url');

function set_header_image_text() {
  return 'Soli';
}
add_filter('login_headertitle','set_header_image_text');


function extra_text() {
  $fp_info = get_soli_fp_info();
  echo "<a class=\"login_background\" href=\"".$fp_info['frontpage_button_link']."\"><strong>".$fp_info['frontpage_subtitle'].", ".$fp_info['frontpage_subtext']."  →</strong></a>";
}
add_action( 'login_head', 'extra_text' );


if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
  ob_start();
}

add_action('login_form', function($args) {
  $login = ob_get_contents();
  ob_clean();
  $login = str_replace('id="user_pass"', 'id="user_pass" autocomplete="on"', $login);
  $login = str_replace('id="user_login"', 'id="user_login" autocomplete="name"', $login);
  echo $login;
}, 9999);

/*function wpse_159462_login_form() {
    echo "
<script type=\"text/javascript\">
    document.getElementById( \"user_login\" ).autocomplete = \"name\";
    document.getElementById( \"user_pass\" ).autocomplete = \"on\";
</script>";
}
add_action( 'login_form', 'wpse_159462_login_form' );*/

?>
