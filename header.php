<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * Note: User context, $myrows, social meta tags, Google Analytics and the Google
 * Fonts stylesheet have been moved to hooks in functions.php so block templates
 * (parts/header.html) get the same context. Keep this file for PHP templates that
 * still call get_header().
 *
 * @since Soli 2.0
 * @version 2.0
 */

global $myrows, $post;

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php
  wp_head();
?>
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