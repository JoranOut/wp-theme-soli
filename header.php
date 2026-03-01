<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @since Soli 2.0
 * @version 2.0
 */

$current_user = wp_get_current_user();

if (null === $current_user) {
    error_log('wp_get_current_user returned NULL');
    error_log('Defined in: ' . (new ReflectionFunction('wp_get_current_user'))->getFileName());
}

$userid    = 0;
$role_name = null;

if ( $current_user && $current_user->exists() ) {
    $userid = (int) $current_user->ID;

    if ( ! empty( $current_user->roles ) ) {
        $role_name = $current_user->roles[0];
    }
}

global $myrows, $post;

if ( ! is_user_logged_in() || in_array( $role_name, array( 'lid', 'author' ), true ) ) {
    $myrows = get_myrows();
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">

<?php /* Social Media tags */
  $social_post["id"] = get_queried_object_id();
  if($social_post["id"]==0){
    $social_post["post_title"] = ($_SERVER["REQUEST_URI"]=="/") ? "SOLI.nl" : "SOLI.nl";
    $social_post["guid"] = home_url($_SERVER["REQUEST_URI"]);
    $social_post["post_content"] = "Klik op de link of afbeelding om meer te zien!";
    $social_post["post_image"] = get_soli_post_image(null,'large');
  } else {
    $social_post_obj = get_post($social_post["id"]);
    if ($social_post_obj) {
      $social_post["guid"] = get_permalink($social_post_obj);
      $social_post["post_title"] = $social_post_obj->post_title;
      $social_post["post_image"] = get_soli_post_image($social_post_obj,'large');
      $social_content = substr(wp_strip_all_tags($social_post_obj->post_content), 0, 65);
      $social_post["post_content"] = substr($social_content, 0, strrpos($social_content, ' '))."...";
    } else {
      $social_post["post_title"] = "SOLI.nl";
      $social_post["guid"] = home_url($_SERVER["REQUEST_URI"]);
      $social_post["post_content"] = "Klik op de link of afbeelding om meer te zien!";
      $social_post["post_image"] = get_soli_post_image(null,'large');
    }
  }
  $social_post["id"]='';
?>

<meta property="og:title" content="<?php echo esc_attr($social_post["post_title"]); ?>" />
<meta property="og:url" content="<?php echo esc_url($social_post["guid"]); ?>" />
<meta property="og:description" content="<?php echo esc_attr($social_post["post_content"]); ?>" />
<meta property="og:image" content="<?php echo esc_url($social_post["post_image"]); ?>"/>
<meta property="og:type" content="article" />

<meta itemprop="name" content="<?php echo esc_attr($social_post["post_title"]); ?>">
<meta itemscope itemtype="<?php echo esc_url($social_post["guid"]); ?>">
<meta itemprop="description" content="<?php echo esc_attr($social_post["post_content"]); ?>">
<meta itemprop="image" content="<?php echo esc_url($social_post["post_image"]); ?>">

<meta name="twitter:image:alt" content="<?php echo esc_attr($social_post["post_title"]); ?>">
<meta name="twitter:title" content="<?php echo esc_attr($social_post["post_title"]); ?>">
<meta name="twitter:description" content="<?php echo esc_attr($social_post["post_content"]); ?>">
<meta name="twitter:image" content="<?php echo esc_url($social_post["post_image"]); ?>">
<meta name="twitter:site" content="@MzvSoli">
<meta name="twitter:creator" content="@MzvSoli">

<?php /* end Social Media tags */ ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-177325852-1"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-177325852-1');
</script>

<link rel="profile" href="http://gmpg.org/xfn/11">
<?php
  wp_head();
?>
<link href="https://fonts.googleapis.com/css?family=Lato|Raleway&display=swap" rel="stylesheet">
</head>

<body <?php body_class(); ?>>
  <div class="menu-wrapper">
    <?php wp_nav_menu(array('container' => '', 'fallback_cb' => ''));
    ?>

  </div>
  <div class="top_header">
    <a onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('Muzieklessen') ) )?>'">Opleidingen</a>
    <a onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('Mijn Pagina') ) )?>'">Mijn Soli</a>
    <a onclick="window.location.href='<?php echo esc_url( get_permalink( get_page_by_title('contact') ) )?>'">Contact</a>
    <a onclick="window.location.href='<?php echo get_home_url(); ?>/?s'">Zoeken</a>
  </div>
  <header id="masthead" class="site-header" role="banner">
    <div>
      <a class="logo" href="<?php echo get_home_url(); ?>">
        <img class="white" src="<?php
           bloginfo('template_url');
           echo "/assets/img/logo_white.svg";
        ?>" height="45px" alt="logo">
        <img class="black" src="<?php
           bloginfo('template_url');
           echo "/assets/img/logo.svg";
        ?>" height="45px" alt="logo">
      </a>
      <div class="first_menu">
        <?php get_template_part("template-parts/main","menu");
        if(is_user_logged_in()){?>
          <div class="menu_item ">
            <h3 onclick="window.location.href='  <?php
                if(is_user_logged_in()){
                  echo home_url("/mijn-pagina");
                } else {
                  echo home_url("/wp-login.php");
                }
                ?>'">Mijn Soli</h3>
          </div><?php
        }?>
      </div>
      <a class="login" href="
      <?php
        if(is_user_logged_in()){
          echo home_url("/mijn-pagina");
        } else {
          echo home_url("/wp-login.php");
        }
        ?>">
        <?php if(is_user_logged_in()){
          echo '<span>'. esc_html(wp_get_current_user()->user_firstname) . '</span>';
        } else {
          echo '<span>Mijn Soli</span>';
        }?>
        <img class="white" src="<?php bloginfo('template_url'); ?>/assets/img/user_white.svg" />
        <img class="black" src="<?php bloginfo('template_url'); ?>/assets/img/user.svg" />
        <div id="notification" data-nonce="<?php echo wp_create_nonce("any_message_nonce")?>">!</div>
      </a>
      <a class="hamburger">
        <span>Menu</span>
        <input id="main-nav" type="checkbox">
        <label for="main-nav">
          <img class="black" src="<?php bloginfo('template_url'); ?>/assets/img/hamburger.svg" alt="hamburger">
          <img class="white" src="<?php bloginfo('template_url'); ?>/assets/img/hamburger_white.svg" alt="hamburger">
        </label>
      </a>
    </div>
	</header>

  <main style="margin-top:<?php if(is_admin_bar_showing()){
    echo '-32px';
  } ?>">