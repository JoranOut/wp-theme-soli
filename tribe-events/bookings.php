<?php

function wpt_add_booking_metaboxes()
{
  add_meta_box(
    'event_is_bookable',
    'Reserveerbaar',
    'event_is_bookable_html',
    'tribe_events',
    'side',
    'default'
  );
}

add_action('add_meta_boxes', 'wpt_add_booking_metaboxes');


function event_is_bookable_html()
{
  global $post;
  // Nonce field to validate form request came from current site
  wp_nonce_field(basename(__FILE__), 'tribe_events_post_type_fields');
  // Get the location data if it's already been entered
  $selected = get_post_meta($post->ID, 'event_is_bookable', true) == "true" ? "checked" : "";
  $closed = get_post_meta($post->ID, 'event_booking_is_closed', true) == "true" ? "" : "checked";
  $range = get_post_meta($post->ID, 'event_bookable_range', true) ? get_post_meta($post->ID, 'event_bookable_range', true) : 30;

  echo '<ul>
        <li>
            <input type="hidden" id="event_is_not_bookable" name="event_is_bookable" value="false"/>
            <input type="checkbox" id="event_is_bookable" name="event_is_bookable" value="true" ' . $selected . '/>
            <label for="event_is_bookable"> Kunnen leden een plek reserveren voor dit evenement?</label></li>
        <li>
            <label for="event_bookable_range"> Aantal deelnemers:</label>
            <input type="numbers" id="event_bookable_range" name="event_bookable_range" value="' . $range . '"/></li>
        <li>
            <input type="hidden" id="event_booking_is_not_closed" name="event_booking_is_closed" value="true"/>
            <input type="checkbox" id="event_booking_is_closed" name="event_booking_is_closed" value="false" ' . $closed . '/>
            <label for="event_booking_is_closed"> Inschrijving open</label></li>
        <li>
        </ul>
        ';
}

function wpt_save_booking_meta($post_id, $post)
{
  if (!current_user_can('edit_post', $post_id)) {
    return $post_id;
  }

  if (!isset($_POST['event_booking_is_closed'])||!isset($_POST['event_is_bookable']) || !isset($_POST['event_bookable_range']) || !wp_verify_nonce($_POST['tribe_events_post_type_fields'], basename(__FILE__))) {
    return $post_id;
  }

  $events_meta['event_booking_is_closed'] = esc_textarea($_POST['event_booking_is_closed']);
  $events_meta['event_is_bookable'] = esc_textarea($_POST['event_is_bookable']);
  $events_meta['event_bookable_range'] = esc_textarea($_POST['event_bookable_range']);
  // Cycle through the $events_meta array.
  // Note, in this example we just have one item, but this is helpful if you have multiple.
  foreach ($events_meta as $key => $value) :
    // Don't store custom data twice
    if ('revision' === $post->post_type) {
      return;
    }
    if (get_post_meta($post_id, $key, false)) {
      // If the custom field already has a value, update it.
      update_post_meta($post_id, $key, $value);
    } else {
      // If the custom field doesn't have a value, add it.
      add_post_meta($post_id, $key, $value);
    }
    if (!$value) {
      // Delete the meta key if there's no value
      delete_post_meta($post_id, $key);
    }
  endforeach;
}

add_action('save_post', 'wpt_save_booking_meta', 1, 2);

function db_get_booking_data()
{
  global $wpdb;
  $cnt = $wpdb->get_results('
      SELECT p.post_id, p.meta_value as bookable, m.max_participants, o.participants, n.closed
      FROM ' . $wpdb->prefix . 'postmeta p
      LEFT JOIN (
        SELECT post_id, meta_value as max_participants
        FROM ' . $wpdb->prefix . 'postmeta
        WHERE meta_key = \'event_bookable_range\'
      ) m ON p.post_id = m.post_id
      LEFT JOIN (
        SELECT post_id, meta_value as participants
        FROM ' . $wpdb->prefix . 'postmeta
        WHERE meta_key = \'event_bookable_participants\'
      ) o ON p.post_id = o.post_id
      LEFT JOIN (
        SELECT post_id, meta_value as closed
        FROM ' . $wpdb->prefix . 'postmeta
        WHERE meta_key = \'event_booking_is_closed\'
      ) n ON p.post_id = n.post_id
      WHERE p.meta_key = \'event_is_bookable\' AND p.meta_value = \'true\'');
  return $cnt;
}

add_action('wp_ajax_nopriv_db_get_participants', 'db_get_participants');
add_action('wp_ajax_db_get_participants', 'db_get_participants');
function db_get_participants()
{
  if(!isset($_REQUEST['post_id'])){
    return;
  }

  global $wpdb;
  $cnt = $wpdb->get_results('
        SELECT *
        FROM ' . $wpdb->prefix . 'postmeta
        WHERE meta_key = \'event_bookable_participants\' and post_id = \''.$_REQUEST['post_id'].'\'');

  echo json_encode($cnt);
  die();
}



function getUserNames($users)
{
  if (!$users) {
    return;
  }

  $participants = [];

  if (is_array($users)) {
    foreach ($users as $user) {
      if(is_array($user)) {
        $participants[] = array(
          'name' => get_user_by('id', $user["id"])->display_name,
          'email' => get_user_by('id', $user["id"])->user_email,
          'instrument' => $user["instrument"]
        );
      } else {
        $participants[] = array(
          'name' => get_user_by('id', $user->id)->display_name,
          'email' => get_user_by('id', $user->id)->user_email,
          'instrument' => $user->instrument
        );
      }

    }
  }
  return $participants;
}

add_action('wp_ajax_nopriv_get_booking_users', 'get_booking_users');
add_action('wp_ajax_get_booking_users', 'get_booking_users');
function get_booking_users()
{
  if (!wp_verify_nonce($_REQUEST['nonce'], "booking")) {
    exit("We are not for hack");
  }

  $participants = $_REQUEST['participants'];

  $usernames = str_replace("\\", "",$participants);

  $json_de = json_decode($usernames);
  $us = getUserNames($json_de);
  echo json_encode($us);
  die();
}

add_action('wp_ajax_nopriv_get_booking_data', 'get_booking_data');
add_action('wp_ajax_get_booking_data', 'get_booking_data');
function get_booking_data()
{
  if (!wp_verify_nonce($_REQUEST['nonce'], "booking")) {
    exit("We are not for hack");
  }

  echo json_encode(db_get_booking_data());
  die();
}


add_action('wp_ajax_nopriv_set_booking_participant', 'set_booking_participant');
add_action('wp_ajax_set_booking_participant', 'set_booking_participant');
function set_booking_participant()
{
  if (!wp_verify_nonce($_REQUEST['nonce'], "booking")) {
    exit("We are not for hack");
  }

  $post_id = $_REQUEST['post_id'];
  $instrument = $_REQUEST['instrument'];

  $range = get_post_meta($post_id, 'event_bookable_range', true) ? get_post_meta($post_id, 'event_bookable_range', true) : 30;
  $current_meta = get_post_meta($post_id, "event_bookable_participants", true);
  $current_user = wp_get_current_user()->ID;

  if ($current_meta) {
    $new_meta = json_decode($current_meta);
    $index = array_search($current_user, $new_meta);
    if ($index || $index === 0) {
      die();
    }
    if (sizeof($new_meta) < $range) {
      array_push($new_meta, array("id"=>$current_user,"instrument"=>$instrument));
      $new_meta = json_encode($new_meta);
      update_post_meta($post_id, "event_bookable_participants", $new_meta);
      echo json_encode(db_get_booking_data());
    } else {
      echo "false";
    }
  } else {
    $new_meta = json_encode(array(array("id"=>$current_user,"instrument"=>$instrument)));
    update_post_meta($post_id, "event_bookable_participants", $new_meta);
    echo json_encode(db_get_booking_data());
  }

  die();
}

add_action('wp_ajax_nopriv_delete_booking_participant', 'delete_booking_participant');
add_action('wp_ajax_delete_booking_participant', 'delete_booking_participant');
function delete_booking_participant()
{
  if (!wp_verify_nonce($_REQUEST['nonce'], "booking")) {
    exit("We are not for hack");
  }

  $post_id = $_REQUEST['post_id'];
  $user_id = $_REQUEST['user_id'];
  if (!isset($user_id)) {
    $user_id = wp_get_current_user()->ID;
  }

  $current_meta = get_post_meta($post_id, "event_bookable_participants", true);

  if ($current_meta) {
    $new_meta = json_decode($current_meta);
    $index = false;
    for ($i = 0; $i < sizeof($new_meta); $i++){
      if($new_meta[$i]->id==$user_id){
        $index = $i;
      }
    }
    if ($index || $index === 0) {
      array_splice($new_meta, $index, 1);
      $new_meta = json_encode($new_meta);
      update_post_meta($post_id, "event_bookable_participants", $new_meta);
    }
  }

  echo json_encode(db_get_booking_data());

  die();
}


?>
