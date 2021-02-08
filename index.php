<?php
/**
 * The main template file
 *
 * @since Soli 2.0
 * @version 2.0
 */

get_header(); ?>

<!-- ADD CONTENT HERE -->
<main>
  <?php
    if ( have_posts() ) while ( have_posts() ) : the_post();
      the_title(
        '<h1 class="font-resizer">',
        '</h1>');
      the_content();
    endwhile;
   ?>
</main>
<?php
get_footer();
?>
