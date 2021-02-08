<?php
/**
 * Template part for displaying event content
 *
 * @since Soli 2.0
 * @version 2.0
 */
 global $post;
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
		<div class="single_event_content">
			<div class="map" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
		    url(<?php echo get_soli_post_image($post,'large'); ?>)">
		    <?php tribe_get_template_part( 'modules/meta/map' );?>
			</div>
			<div class="details">
				<?php
          if (tribe_event_in_category("uitgaanstip")&&'tribe_events' === get_post_type()) echo '<h2>Uitgaanstip!</h2>';
					the_content();
					if(  $selected = get_post_meta( $post->ID, 'event_is_bookable', true ) == "true"){
              echo '<a class="reserveren" href="./reserveren">Reserveren</a>';
          }
					tribe_get_template_part( 'modules/meta/details' );
					tribe_get_template_part( 'modules/meta/venue' );
				?>
        <br>
        <a href="<?php get_permalink(); ?>?ical=1&tribe_display=">+download ical</a>
			</div>
		</div>
    <?php if($post->post_content){
      get_template_part( 'template-parts/socialmedia', 'bar' );
    }
    ?>
    <div class="wrap-excerpt">
      <?php
        get_event_posts();
      ?>
    </div>
	</div>
</article>
