<?php
/**
 * Template part for displaying page content in page.php
 *
 * @since Soli 2.0
 * @version 2.0
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post,'large'); ?>)">
		<?php the_title(
			'<h1 class="font-resizer">',
      '</h1>'
    ); ?>
	</header>
	<div class="page-content">
		<h1 class="title font-resizer"><?php the_title(); ?></h1>
		<p class="picture title_image">
			<a href="<?php echo get_soli_post_image($post,'large'); ?>">
				<img class="alignnone"
				src="<?php echo get_soli_post_image($post,'large'); ?>">
			</a>
		</p>
		<?php	the_content();?>
		<div class="related">
			<h3>Bekijk de onderliggende pagina's</h3>
			<?php get_child_pages(); ?>
		</div>
    <?php get_template_part( 'template-parts/socialmedia', 'bar' );?>

    </div>
</article>
