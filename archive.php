<?php
/**
 * The template for displaying archive pages
 *
 * TEMPLATE NAME: Archive page
 * @since Soli 2.0
 * @version 2.0
 */

 get_header();
?>

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
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $args = array ('posts_per_page' => 16,'paged'=>$paged);
      query_posts($args);
      if ( have_posts() ) : while (have_posts()) : the_post();
        get_template_part( 'template-parts/post', 'excerpt' );
      endwhile;
        previous_posts_link();
        next_posts_link();
      else:
        get_template_part( 'template-parts/post', 'none');
      endif; ?>
    </div>
  </div>
</article>

<?php
get_footer();
?>
