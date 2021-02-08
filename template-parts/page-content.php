<?php
/**
 * Template part for displaying page content in page.php
 *
 * @since Soli 2.0
 * @version 2.0
 */

 if(get_post_type($post->ID) == "mededelingen"){
	 add_post_meta($post->ID, "mededelingen".wp_get_current_user()->ID,'true', true);
 }
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post,'large'); ?>)">
		<?php the_title(
      '<h1 class="font-resizer">',
			'</h1>'
		); ?>	</header>
	<div class="page-content">
		<h1 class="title font-resizer"><?php the_title(); ?></h1>
		<?php if(!is_page()):?>
      <h2 class="subtitle">
        <?php the_date(); ?>
				<span>
					<?php echo get_post_type_nicename($post->post_type); ?>
				</span>
      </h2>
		<?php
    endif;

		?>
		<p class="picture title_image">
			<a href="<?php echo get_soli_post_image($post,'large'); ?>">
				<img class="alignnone"
				src="<?php echo get_soli_post_image($post,'large'); ?>">
			</a>
		</p>
		<?php the_content();

    if($post->post_content){
      get_template_part( 'template-parts/socialmedia', 'bar' );
    }
    ?>
    </div>
    <?php if(!is_page()){?>
      <div class="wrap-excerpt suggestions">
        <?php
        if(!is_page()){
          get_other_posts();
        }
        ?>
      </div>
    <?php } ?>
</article>
