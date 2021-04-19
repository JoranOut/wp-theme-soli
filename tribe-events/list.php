<?php
/**
 * The template for displaying event pages, overrides plugin default template
 *
 * TEMPLATE NAME: Agenda page
 * @since Soli 2.0
 * @version 2.0
 */
  get_header();
  global $myrows;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="page-header" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image(0,'large'); ?>)">
    <h1>
    <?php
      wp_title();
    ?>
    </h1>
	</header>
	<div class="page-content">
    <div class="wrap-excerpt" style="position: relative">
      <h1 class="title">Agenda</h1>
      <a class="monthlist_toggle list" href="<?php echo home_url("/agenda/month");?>"><div></div><span>Kalender</span><span>Lijst</span></a>
      <?php
      $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
      $offset = $paged*16-16;

      $events = tribe_get_events(array(
        'posts_per_page' => 16,
        'start_date' => date('Y-m-d 00:00:00'),
        'offset' => $offset,
        'post__not_in' => $myrows
      ));

      if($events){
        global $post;
        foreach ($events as $post) {
          get_template_part( 'template-parts/post', 'excerpt' );
        }
      }else{
        get_template_part( 'template-parts/agenda-content', 'none');
      }

      previous_posts_link();
      next_posts_link();

      get_template_part('template-parts/ical', 'part'); ?>
    </div>
	</div>
</article>

<?php
  get_footer();
?>
