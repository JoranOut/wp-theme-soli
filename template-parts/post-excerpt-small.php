<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */
 setup_postdata($post);
?>


<section id="post-<?php the_ID(); ?>" <?php post_class('post-excerpt-small'); ?> onclick="window.location.href='<?php echo esc_url( get_permalink() )?>'">
	 <div class="date">
     <div>
       <?php
       if ( 'post' === get_post_type() ) :
          wrap_element(get_the_time('d/m'),"em");
       elseif ( 'tribe_events' === get_post_type() ) :
          $date = strtotime($post->event_date);
          wrap_element(date('d/m',$date),"em");
       endif;
       ?>
     </div>
   </div>
   <div class="content">
     <?php the_title( '<h2 class="entry-title">', '</h2>' );?>
  </div>
</section>
