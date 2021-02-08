<?php
/**
 * The template for displaying search results pages
 *
 * @since Soli 2.0
 * @version 2.0
 */

get_header();

if($_GET['s']=='' && $_POST['s']==''){
  get_template_part( 'template-parts/search', 'none' );
} else {
  get_template_part( 'template-parts/search', 'results' );
}

get_footer();?>
