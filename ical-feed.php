<?php
/**
 * The template for feeding calendar information
 *
 * TEMPLATE NAME: Ical feed
 * @since Soli 2.0
 * @version 2.0
 */

if (ISSET($_GET["i"])&&$_GET["i"]!=''&&ISSET($_GET["h"])&&$_GET["h"]!=''&&$_GET['o']==1) {
  global $wpdb;
  $userid = ($id === null)? wp_get_current_user()->ID : $id;
  $myrowsquery = "SELECT distinct t.ID FROM wp_uam_accessgroup_to_object q
    INNER JOIN wp_uam_accessgroup_to_object w ON q.group_id = w.group_id AND q.object_id = 632
    LEFT JOIN wp_uam_accessgroup_to_object r ON w.object_id = r.object_id
    INNER JOIN wp_posts t ON r.object_id = t.ID
    INNER JOIN wp_postmeta m on t.ID = m.post_id
    WHERE t.post_parent = 0 AND NOT q.group_id = 3 AND t.post_type = 'tribe_events'
    AND m.meta_key = '_EventStartDate' and meta_value >= NOW() ORDER BY `r`.`object_id` DESC";
  $tmpoutput = $wpdb->get_results($wpdb->prepare($myrowsquery,"OBJECT"));
  $output = array_map(function ($entry) {
    return (int) $entry->ID;
  }, $tmpoutput);
  $events = tribe_get_events(array(
    'start_date' => date('Y-m-d 00:00:00'),
    'post__in' => $output
  ));
} elseif(ISSET($_GET["i"])&&$_GET["i"]!=''&&ISSET($_GET["h"])&&$_GET["h"]!=''&&$_GET["h"]==get_user_ical_hash($_GET["i"])){
  $role_name = get_user_by("ID",$_GET["i"])->roles[0];
  if($role_name==="lid"||$role_name==="author"){
    $myrows = get_myrows($_GET["i"]);
  }
  $events = tribe_get_events(array(
    'start_date' => date('Y-m-d 00:00:00'),
    'post__not_in' => $myrows
  ));
} else {
  $myrows = get_myrows(0);
  $events = tribe_get_events(array(
    'start_date' => date('Y-m-d 00:00:00'),
    'post__not_in' => $myrows
  ));
}

$ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//soli.nl//icalfeed v1.0//NL
";

foreach($events as $event) {
 $DTSTART = date("Ymd\THis",strtotime($event->event_date));
 $DTEND = (tribe_get_end_date($event->ID,false,$format = "Y-m-d H:i:s"))?date("Ymd\THis",strtotime(tribe_get_end_date($event->ID,false,$format = "Y-m-d H:i:s"))):date("Ymd\THis",strtotime($event->event_date));
 $DTSTAMP = date("Ymd\THis",strtotime($event->post_modified_gmt));
 $LOCATION = trim(preg_replace('/\v(?:[\v\h]+)/', ' ', limit_excerpt(wp_strip_all_tags(tribe_get_venue_details($event->ID)["address"]),70,false)));
 $URL = get_home_url()."?p=".$event->ID;
 $DESCRIPTION =  trim(preg_replace('/\v(?:[\v\h]+)/', ' ', limit_excerpt(wp_strip_all_tags($event->post_content),60,false)));
 $ical .= "BEGIN:VEVENT
SUMMARY:{$event->post_title}
UID:{$event->ID}@soli.nl
DTSTAMP:{$DTSTAMP}
DTSTART:{$DTSTART}
DTEND:{$DTEND}
LOCATION:{$LOCATION}
DESCRIPTION:{$DESCRIPTION}
URL: {$URL}
END:VEVENT
";
}
$ical .= "END:VCALENDAR";

 //set correct content-type-header
 header('Content-type: text/calendar; charset=utf-8');
 header('Content-Disposition: inline; filename=calendar.ics');
 echo $ical;
 exit;
 ?>
