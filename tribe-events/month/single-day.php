<?php
/**
 * Month View Single Day
 * This file contains one day in the month grid
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/month/single-day.php
 *
 * @package TribeEventsCalendar
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

$day = tribe_events_get_current_month_day();
$currentday_events = soli_month_get_post($events,$day['date']);
$events_label = ( 1 === $day['total_events'] ) ? tribe_get_event_label_singular() : tribe_get_event_label_plural();
?>

<!-- Day Header -->
<div id="tribe-events-daynum-<?php echo $day['daynum-id'] ?>">
		<?php echo $day['daynum']?>
</div>

<!-- Events List -->
<?php
//if(sizeof($currentday_events)==1){
  foreach ($currentday_events as $post) {
    ?>
    <a class="has_event" href="<?php echo esc_attr($page_month_baseurl.$day['date']); ?>"></a>
    <div id="tribe-events-event-<?php echo esc_attr( $post->ID ); ?>" class="tribe_events" data-tribejson='<?php echo esc_attr( tribe_events_template_data( $post ) ); ?>'>
    	<h3 class="tribe-events-month-event-title"><a href="<?php echo esc_url( $post->guid ) ?>" class="url"><?php echo $post->post_title ?></a></h3>
    </div>
    <?php
  }
//} else if (sizeof($currentday_events)>0){
/*?>
<!-- View More -->
  <a class="has_event" href="<?php echo esc_attr($page_month_baseurl.$day['date']); ?>"></a>
  <div id="tribe-events-event-<?php echo esc_attr( $post->ID ); ?>" class="tribe_events">
		<a href="<?php echo esc_attr($page_month_baseurl.$day['date']); ?>"><?php echo "Meerdere evenementen..." ?> &raquo;</a>
	</div>
	<div id="tribe-events-tooltip-8009-2019-09-20" class="tribe-events-tooltip" style="bottom: 62px; display: none; opacity: 1;">
			<h3 class="entry-title summary">Bezoek Fanfare Wilhelmina Groesbeek</h3>
			<div class="tribe-events-event-body">
				<div class="tribe-event-duration">
					<abbr class="tribe-events-abbr tribe-event-date-start">20 september  19:00-22:00 </abbr>
				</div>
				<div class="tribe-event-description"><p>Ontvangst Fanfare Wilhelmina, kennismaking en eerste repetitie&nbsp;in de grote zaal.</p></div>
				<span class="tribe-events-arrow"></span>
			</div>
		</div>
<?php
}*/
