<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */
global $post;
?>

<section id="post-<?php the_ID(); ?>" <?php post_class('flat-excerpt'); ?> onclick="window.location.href='<?php echo esc_url( get_permalink() )?>'">
	 <div class="picture" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
     url(<?php echo get_soli_post_image($post,'large'); ?>)">
   </div>
   <div>
     <?php
     the_title( '<h2 class="entry-title">', '</h2>' );
     ?>
   </div>
</section>
