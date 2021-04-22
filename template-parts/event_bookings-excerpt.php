<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */
setup_postdata($post);

$classes = (tribe_event_in_category("uitgaanstip")&&'tribe_events' === get_post_type()) ? "post-excerpt-template tip" : "post-excerpt-template";
$range = get_post_meta( $post->ID, 'event_bookable_range', true ) ? get_post_meta( $post->ID, 'event_bookable_range', true ) : 30;
$participants_json = get_post_meta( $post->ID, 'event_bookable_participants', true );
$booking_closed = get_post_meta( $post->ID, 'event_booking_is_closed', true );
$participants = json_decode($participants_json,true);
$current_user = wp_get_current_user()->ID;
$index = false;
for ($i = 0; $i < sizeof($participants); $i++){
  if($participants[$i]["id"]===$current_user){
    $index = $i;
  }
}

$full_event = sizeof($participants) >= $range;
$selected = ($index || $index === 0) ? "checked" : "";
$instrument = ($index || $index === 0) ? $participants[$index]["instrument"] : "";
$disabled = ($index || $index === 0 || ($booking_closed === "true") || $full_event) ? "disabled" : "";
$booking_closed_str = ($booking_closed === "true" || $full_event) ? "disabled" : "";
$date = strtotime($post->event_date);

?>

<input type="checkbox" id="booking-<?php the_ID(); ?>" class="booking_inputs" name="booking-<?php the_ID(); ?>" onclick="setChecked(<?php the_ID(); ?>)" <?php echo $selected." ".$booking_closed_str ?> >
<section id="post-<?php the_ID(); ?>" <?php post_class($classes); ?> data-datum="<?php echo date('d.m.y',$date) ?>" data-titel="<?php the_title() ?>" data-url="<?php echo esc_url( get_permalink() )?>">
  <div class="date" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .2) 0%, rgba(75, 33, 191, .2) 100%),
    url(<?php echo get_soli_post_image($post,'medium'); ?>)">
    <div>
      <?php
      if ( 'post' === get_post_type() ) :
        wrap_element(get_the_time(D),"strong");
        wrap_element(get_the_time('d.m.y'),"em");
      elseif ( 'tribe_events' === get_post_type() ) :
        $date = strtotime($post->event_date);
        wrap_element(date('D',$date),"strong");
        wrap_element(date('d.m.y',$date),"em");
      endif;
      ?>
    </div>
  </div>
  <div class="content">
    <?php
    if (tribe_event_in_category("uitgaanstip")&&'tribe_events' === get_post_type()) echo '<h3>Uitgaanstip!</h3>';
    ?> <h2 class="entry-title"> <?php the_title()?> </h2>
       <div class="participants-hover">
         <span>deelnemers:</span>
         <span id="booking-participants-<?php the_ID(); ?>"><?php echo sizeof($participants) ?></span>
         <span>/</span>
         <span id="booking-max-participants-<?php the_ID(); ?>"><?php echo $range ?></span>
       </div>
       <div class="entry-content">
       <p><?php
        if ( 'post' === get_post_type() || 'page' === get_post_type()) :
          wp_strip_all_tags(the_excerpt());
        elseif ( 'tribe_events' === get_post_type() ) :
          limit_excerpt(wp_strip_all_tags(get_post()->post_content),100);
        endif;
        ?></p>
      <?php
        if( get_post_meta( $post->ID, 'event_is_bookable', true ) == "true"){
          echo '<span class="booking-error"><input id="booking-instrument-'.$post->ID.'" class="booking-instrument" placeholder="Instrument..." type="text" value="'.$instrument.'" '.$disabled.'/></span>';
          echo '<label for="booking-'.$post->ID.'" class="reserveren unreserve">Annuleer reservering</label>';
          echo '<label for="booking-'.$post->ID.'" class="reserveren reserve">Reserveren</label>';
        }
      ?>
    </div>
    <a href="<?php echo esc_url( get_permalink() )?>" >lees meer</a>
  </div>
</section>
<div id="booking-<?php the_ID(); ?>-participants" class="participants">
  <?php
    if( wp_get_current_user()->roles[0] !== "lid"){
      $usernames = getUserNames($participants);
      if($participants) {
        echo '<div class="fparticipant">Deelnemers:</div>';
        for ($i = 0; $i < sizeof($usernames); $i++) {
          echo '<div id="participant-'.$post->ID.'-'.$usernames[$i]["id"].'" class="participant">' . $usernames[$i]["name"] . '<div onclick="bookingsInput('.$post->ID.','.$usernames[$i]["id"].',\'participant-'.$post->ID.'-'.$usernames[$i]["id"].'\')"></div></div>';
        }
      }
    }
  ?>
</div>
