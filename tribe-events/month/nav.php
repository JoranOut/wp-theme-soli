<?php
/**
 * Month View Nav Template
 * This file loads the month view navigation.
 *
 * Override this template in your own theme by creating a file at [your-theme]/tribe-events/month/nav.php
 *
 * @package TribeEventsCalendar
 * @version 4.2
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
} ?>

<?php

  do_action( 'tribe_events_before_nav' );
  $previousMonth = clone $page_month_date;
  $previousMonth->modify("first day of previous month");
  $nextMonth = clone $page_month_date;
  $nextMonth->modify("first day of next month");
?>

<h3 class="screen-reader-text" tabindex="0"><?php esc_html_e( 'Calendar Month Navigation', 'the-events-calendar' ) ?></h3>

<ul class="soli_month_nav">
	<li>
    <?php echo '<a href="' . $page_month_baseurl . $previousMonth->format("Y-m"). '"><span>&laquo;</span> ' . $previousMonth->format("F") . ' </a>';?>
	</li>
	<!-- .tribe-events-nav-previous -->
	<li>
    <?php echo '<a href="' .  $page_month_baseurl . $nextMonth->format("Y-m"). '">' . $nextMonth->format("F") . ' <span>&raquo;</span></a>';?>
	</li>
	<!-- .tribe-events-nav-next -->
</ul><!-- .tribe-events-sub-nav -->

<?php
do_action( 'tribe_events_after_nav' );
