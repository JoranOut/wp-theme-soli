<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */

global $post;
$monthNames = [
	"Januari", "Februari", "Maart",
	"April", "Mei", "Juni", "Juli",
	"Augustus", "September", "Oktober",
	"November", "December"
];
?>

<section id="post-<?php the_ID(); ?>" <?php post_class('search-excerpt'); ?> onclick="window.location.href='<?php echo esc_url( get_permalink() )?>'">
	<div class="picture" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
	 url(<?php echo get_soli_post_image($post,'large'); ?>)">
	</div>
	<div>
		<?php
		the_title( '<h2 class="entry-title">', '</h2>' );
		echo '<span class="type">'.get_post_type_nicename($post->post_type).' </span>';
		if ( 'tribe_events' === get_post_type() ) :
			$date = strtotime(get_post_meta($post->ID, '_EventStartDate')[0]);
			wrap_element(date('d ',$date).$monthNames[intval(date('m',$date))-1].date(' Y ',$date),"em");
		elseif ('page' === get_post_type()) :
		else :
			wrap_element(get_the_time(d.' ').$monthNames[intval(get_the_time(m))-1].get_the_time(' Y '),"em");

		endif;
		echo '<br>'.get_uam_groups_by_ID($post->ID);
		//wp_strip_all_tags(the_excerpt());
		?>
	</div>
</section>
