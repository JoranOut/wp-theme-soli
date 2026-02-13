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
    url(<?php echo get_soli_post_image(get_page_by_title('agenda'),'large'); ?>)">
  <h1>
    <?php
      wp_title();
      $main_title = wp_title(null,false);
    ?>
  </h1>
	</header>
  <div class="page-content">
    <div class="wrap-excerpt" style="position: relative">
      <h1 class="title"><?php echo $main_title; ?></h1>
      <a class="monthlist_toggle" href="<?php echo home_url("/agenda/");?>"><div></div><span>Kalender</span><span>Lijst</span></a>
      <?php
      global $wpdb;
			$page_month_date    = new DateTime(tribe_get_month_view_date());
			$page_month_baseurl = tribe_get_gridview_link( false );
      $page_month_baseurl = substr($page_month_baseurl,0,strlen($page_month_baseurl)-6);

      $begin_date = 0;
      $i=0;
      $end_date = 0;
      while ( tribe_events_have_month_days() ) : tribe_events_the_month_day();
			   $daydata = tribe_events_get_current_month_day();
         if($i == 0){
           $begin_date = $daydata['date'];
           $i = 1;
         }
         $end_date = $daydata['date'];
			endwhile;

      $events = tribe_get_events(array(
        'start_date' => $begin_date." 00:00:00",
        'end_date' => $end_date." 24:00:00",
        'post__not_in' => $myrows
      ));

      function soli_month_get_post($events,$date = null){
        $output = array();
        foreach ($events as $event) {
          if($date==null || substr($event->event_date,0,10)===$date){
            array_push($output,$event);
          }
        }
        return $output;
      }

      include_once "month/content.php";
      get_template_part('template-parts/ical', 'part');

      ?>
    </div>
  </div>
</article>

<?php
  get_footer();
?>
