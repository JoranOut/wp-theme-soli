<?php
/**
 * The template for displaying event pages, overrides plugin default template
 *
 * TEMPLATE NAME: Agenda page
 * @since Soli 2.0
 * @version 2.0
 */
  get_header();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post,'large'); ?>)">
    <h1>
    <?php
      wp_title();
    ?>
    </h1>
	</header>
  <div class="page-content">
    <div class="wrap-excerpt">
      <?php
			$page_day_date = new DateTime(tribe_get_month_view_date());
      $page_day_next_date = clone $page_day_date;
      $page_day_next_date->modify("next day");
      $events = tribe_get_events(array(
        'start_date' => $page_day_date->format("Y-m-d")." 00:00:00",
        'end_date' => $page_day_next_date->format("Y-m-d")." 00:00:00",
        'post__not_in' => $myrows
      ));
      if($events){
        global $post;
        foreach ($events as $post) {
          get_template_part( '/template-parts/post', 'excerpt' );
        }
      }else{
        get_template_part( 'template-parts/post', 'none');
      }
      ?>
    </div>
  </div>

</article>

<?php
  get_footer();
?>
