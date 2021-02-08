<?php
add_action("wp_ajax_save_default_images", "save_default_images");
add_action("wp_ajax_nopriv_save_default_images","save_default_images");
function save_default_images(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"save_default_images_nonce")) {
    exit("We are not for hack");
  }

  global $wpdb;
  $table_name = $wpdb->prefix . "soli_imaging";
  $fp = json_decode(stripslashes($_REQUEST["frontpage_settings"]));

  for($ai = 0; $ai<count($fp); $ai++){
    $input = json_decode($fp[$ai]);
    $sql = $wpdb->prepare(
       "
         DELETE FROM $table_name
         WHERE type = %s AND object = %s
       ",
              array(
                  "frontpage",
                  $input[0],
              )
      );
    $wpdb->get_results($sql);

    if($input[1]!=""){
      $sql = $wpdb->prepare(
  	     "
  		     INSERT INTO $table_name
  		     ( type, object, info )
    	     VALUES ( %s, %s, %s )
         ",
                array(
  		              "frontpage",
  		              $input[0],
  		              $input[1]
  	            )
        );
      $wpdb->get_results($sql);
    }
  }
  $info = json_decode(stripslashes($_REQUEST["info"]));
  for($ai = 0; $ai<count($info); $ai++){
    $sql = $wpdb->prepare(
       "
         DELETE FROM $table_name
         WHERE type = %s AND object = %d
       ",
              array(
                  "image",
                  $info[$ai][0],
              )
      );
    $wpdb->get_results($sql);

    $imgarr = array_slice($info[$ai],1,count($info[$ai]));
    if(!empty($imgarr)){
      $sql = $wpdb->prepare(
  	     "
  		     INSERT INTO $table_name
  		     ( type, object, info )
    	     VALUES ( %s, %s, %s )
         ",
                array(
  		              "image",
  		              $info[$ai][0],
                    json_encode($imgarr)
  	            )
        );
        $wpdb->get_results($sql);
      }
  }

  die();
}
?>
