<?php
/**
 * The template part for displaying results in search pages
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title(); ?>
	</header>

	<?php if ( 'post' === get_post_type() ) {
    echo 'post';
  } elseif ( 'events' === get_post_type()) {
    echo 'events';
  } else {
    echo 'others';
  }
  ?>
</article><!-- #post-## -->
