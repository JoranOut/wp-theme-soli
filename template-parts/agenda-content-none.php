<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in for when no agenda items are presented
 *
 * @since Soli 2.0
 * @version 2.0
 */
?>

<section id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="date" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image(null,'medium'); ?>)">
    <div>
      <?php
        wrap_element(current_time('D'),"strong");
        wrap_element(current_time('d.m.y'),"em");
      ?>
    </div>
  </div>
  <div class="content">
    <h2 class="entry-title">Geen Agenda items gevonden.</h2>
    <div class="entry-content">
      In dit coronatijdperk hebben we geen publieke meer activeiten gepland.
      <a href="<?php echo home_url("/wp-login.php"); ?>">Log in</a> om
      te zien of er ledenactiviteiten zijn.
    </div>
  <a href="<?php echo home_url("/wp-login.php"); ?>" >Log in</a>
  </div>
</section>
