<?php
/**
 * Template part for displaying single posts on a page
 *
 * @since Soli 2.0
 * @version 2.0
 */

get_header();
while ( have_posts() ) : the_post();
  get_template_part( 'template-parts/page', 'content' );
endwhile;
get_footer();?>
