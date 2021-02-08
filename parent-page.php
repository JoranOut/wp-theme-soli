<?php
/**
 * The main template file for displaying pages with single childs
 *
 * TEMPLATE NAME: parent page
 *
 * @since Soli 2.0
 * @version 2.0
 */

global $post;
get_header();
while ( have_posts() ) : the_post();
  setup_postdata($post);
  get_template_part( 'template-parts/page', 'content-parent' );
endwhile;
wp_reset_postdata();
get_footer();
?>
