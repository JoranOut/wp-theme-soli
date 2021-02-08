<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
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
        wrap_element("Geen","strong");
        wrap_element("Data","em");
      ?>
    </div>
  </div>
  <div class="content">
    <h2 class="entry-title">Geen data gevonden.</h2>
    <div class="entry-content">
    </div>
  </div>
</section>
