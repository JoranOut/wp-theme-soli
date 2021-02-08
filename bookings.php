<?php
/**
 * The template for displaying all bookable pages
 *
 * TEMPLATE NAME: Bookable event page
 * @since Soli 2.0
 * @version 2.0
 */

 if ( !is_user_logged_in() ) {
    auth_redirect();
    die();
 }

 $current_user = wp_get_current_user();
 $user_id = $current_user->ID;
 $user_role = $current_user->roles[0];

 global $myrows;

 function enqueue_bookings_scripts() {
   wp_enqueue_script( 'bookings_script', get_template_directory_uri() . '/assets/js/bookings.js', array('jquery') );
   wp_localize_script( 'bookings_script', 'myAjax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
 }
 add_action( 'wp_enqueue_scripts', 'enqueue_bookings_scripts' );

 get_header();
?>
<div id="user_id" data-id="<?php echo $user_id?>"></div>
<div id="user_role" data-role="<?php echo $user_role?>"></div>
<div id="booking" data-nonce="<?php echo wp_create_nonce("booking")?>"></div>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post); ?>)">
    <?php the_title(
      '<h1 class="font-resizer">',
      '</h1>'
    ); ?>
  </header>
  <div class="page-content">
    <h1 class="title font-resizer"><?php the_title(); ?></h1>
      <div class="wrap-excerpt">
        <?php

        $events = tribe_get_events(array(
          'meta_key' => 'event_is_bookable',
          'meta_value' => 'true',
          'start_date' => date('Y-m-d 00:00:00'),
          'post__not_in' => $myrows
        ));

        if($events){
          global $post;
          foreach ($events as $post) {
            get_template_part( 'template-parts/event_bookings', 'excerpt' );
          }
        }else{
          get_template_part( 'template-parts/post', 'none');
        }
        ?>
      </div>
  </div>
</article>

<?php
get_footer();
?>
