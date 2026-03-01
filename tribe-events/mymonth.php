<div class="wrap-excerpt" style="position: relative">
  <?php
  global $wpdb, $myrows;
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
    'end_date' => $end_date." 00:00:00",
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
  ?>
</div>
