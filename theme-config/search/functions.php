<?php
function get_myrows($id = null){
  global $wpdb;
  $userid = intval(($id === null) ? wp_get_current_user()->ID : $id);
  $myrowsquery = $wpdb->prepare("SELECT r.object_id
    FROM ".$wpdb->prefix."uam_accessgroup_to_object q
    INNER JOIN ".$wpdb->prefix."uam_accessgroup_to_object w
    ON q.group_id = w.group_id AND q.object_id = %d OR w.group_id = 0
    RIGHT JOIN ".$wpdb->prefix."uam_accessgroup_to_object r
    ON w.object_id = r.object_id
    INNER JOIN ".$wpdb->prefix."posts t
    ON r.object_id = t.ID
    WHERE w.object_id IS NULL and t.post_parent = 0",
    $userid
  );
  $tmpoutput = $wpdb->get_results($myrowsquery);
  $output = array_map(function ($entry) {
    return (int) $entry->object_id;
  }, $tmpoutput);
  return $output;
}

function get_uam_groups_by_ID($id){
  global $wpdb;
  $groups = array();
  $results = $wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'uam_accessgroup_to_object A INNER JOIN '.$wpdb->prefix.'uam_accessgroups B on A.group_id = B.ID WHERE object_id = %d', intval($id)));
  if($results!=null){
    foreach ($results as $res) {
      if($res->groupname!="Alle Soli-Leden" && $res->groupname!="Soli muziekcentrum"){
        array_push($groups, "@".$res->groupname);
      }
    }
    return implode(" ",$groups);
  } else {
    return "";
  }
}

function get_post_type_nicename($name){
  switch ($name) {
    case "post":
      return "Nieuws";
    case "tribe_events":
      return "Evenement";
    case "page":
      return "Pagina";
    case "music":
      return "Muziekalbum";
    default:
      return "Mededelingen";
  }
}
?>
