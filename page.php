<?php
/**
 * The main template file for displaying pages
 *
 * @since Soli 2.0
 * @version 2.0
 */

get_header();
while ( have_posts() ) : the_post();
  get_template_part( 'template-parts/page', 'content' );
endwhile;
get_footer();
?>
