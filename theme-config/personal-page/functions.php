<?php

function admin_default_page() {
  $current_user = wp_get_current_user();
  if ( ! $current_user->exists() ) {
    return home_url('/mijn-pagina');
  }

  $roles = (array) $current_user->roles;
  if ( in_array( 'administrator', $roles, true ) ) {
    return admin_url();
  }
  
  return home_url('/mijn-pagina');
}
add_filter('login_redirect', 'admin_default_page');


add_action("wp_ajax_get_news_widget", "get_news_widget");
add_action("wp_ajax_nopriv_get_news_widget","get_news_widget");
function get_news_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_news_widget_nonce")) {
    exit("We are not for hack");
  }

  /*FILTER ON UAM GROUPS IF UAM IS ACTIVATED*/
  include_once( ABSPATH . 'wp-admin/includes/user-access-manager.php' );
  $plugin_active = is_plugin_active( 'user-access-manager/user-access-manager.php' );
  $user_id = wp_get_current_user()->ID;

  if($plugin_active){
    global $wpdb;
    $posts_avecGroupe = array();
    $posts_avecGroupe = $wpdb->get_col($wpdb->prepare(
                                      "SELECT T2.object_id FROM ".$wpdb->prefix."uam_accessgroup_to_object T1
                                      INNER JOIN ".$wpdb->prefix."uam_accessgroup_to_object T2 ON T1.group_id=T2.group_id
                                      INNER JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."posts.ID=T2.object_id
                                      WHERE T1.object_id = %d AND T2.object_type='post'
                                      ORDER BY post_date DESC LIMIT 16",
                                      intval($user_id)
    ));
    $posts = get_posts( array('post__in'=>$posts_avecGroupe, "numberposts"=>6));
  } else {
    $posts = get_posts(array("numberposts" => 6));
  }

  global $post;
  if($posts){
    foreach ($posts as $post){
      get_template_part( '/template-parts/post', 'excerpt-small' );
    }
  }
  die();
}

add_action("wp_ajax_get_agenda_widget", "get_agenda_widget");
add_action("wp_ajax_nopriv_get_agenda_widget","get_agenda_widget");
function get_agenda_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_agenda_widget_nonce")) {
    exit("We are not for hack");
  }

  $myrows = get_myrows();
  $events = tribe_get_events(array(
    'posts_per_page' => 6,
    'start_date' => date('Y-m-d H:i:s'),
    'post__not_in' => $myrows
  ));
  if($events){
    global $post;
    foreach ($events as $post) {
      get_template_part( 'template-parts/post', 'excerpt-small' );
    }
  }else{
    get_template_part( 'template-parts/post/content', 'none');
  }
  die();
}

function compareByName($a, $b) {
  return strcasecmp($a->getFilename(), $b->getFilename());
}

add_action("wp_ajax_get_music_widget", "get_music_widget");
add_action("wp_ajax_nopriv_get_music_widget","get_music_widget");
function get_music_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_music_widget_nonce")) {
    exit("We are not for hack");
  }

  echo '<li><a onclick="document.getElementById(\'musicmaindir\').style.display = \'block\'; document.getElementById(\'musicfolderdir\').style.display = \'none\';"><< terug</a></li>';
  if(isset($_REQUEST['foldername'])){
    // Sanitize folder name to prevent path traversal
    $foldername = sanitize_file_name($_REQUEST['foldername']);
    $upload_dir = wp_upload_dir();
    $music_base = $upload_dir['basedir'] . '/music/';
    $full_path = realpath($music_base . $foldername);

    // Verify the path is within the allowed directory
    if ($full_path === false || strpos($full_path, realpath($music_base)) !== 0) {
      echo '<li>Invalid directory</li>';
      die();
    }

    $di = new DirectoryIterator($full_path);
    $allFilesinfo = array();
    foreach ($di as $fileInfo) {
        if ($fileInfo->isFile()) {
            $allFilesinfo[] = clone $fileInfo;
        }
    }

    usort($allFilesinfo, 'compareByName');

    foreach ($allFilesinfo as $fileinf) {
      $safe_filename = esc_attr($fileinf->getFilename());
      $safe_id = 'player-' . sanitize_html_class($fileinf->getFilename());
      $safe_src = esc_url(str_replace($_SERVER["DOCUMENT_ROOT"], '', $fileinf->getPathname()));
      echo '<li class="music"><a onclick="if(this.classList.contains(\'pause\')){this.classList.remove(\'pause\');document.getElementById(\'' . $safe_id . '\').pause();}else{document.getElementById(\'' . $safe_id . '\').play();this.classList.add(\'pause\');}">
      <div class="audiobuttons">
        <div class="play"></div>
      </div>
      <p>' . esc_html($fileinf->getFilename()) . '</p>
      <audio id="' . $safe_id . '" src="' . $safe_src . '"></audio></a></li>';
    }
  }

  die();
}

add_action("wp_ajax_get_message_widget", "get_message_widget");
add_action("wp_ajax_nopriv_get_message_widget","get_message_widget");
function get_message_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_message_widget_nonce")) {
    exit("We are not for hack");
  }

  if(isset($_REQUEST['group'])){
    echo '<li><a onclick="document.getElementById(\'messagegroups\').style.display = \'block\'; document.getElementById(\'messages\').style.display = \'none\';"><< terug</a></li>';
    global $wpdb;
    $group_id = intval($_REQUEST['group']);
    $current_user_id = intval(wp_get_current_user()->ID);

    // Verify user is a member of this group (IDOR protection)
    $is_member = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM ".$wpdb->prefix."uam_accessgroup_to_object
       WHERE group_id = %d AND object_id = %d",
      $group_id,
      $current_user_id
    ));

    if (!$is_member) {
      echo '<li>Access denied</li>';
      die();
    }

    $results = $wpdb->get_results($wpdb->prepare('
      SELECT '.$wpdb->prefix.'posts.post_title, '.$wpdb->prefix.'posts.ID, seen
      FROM '.$wpdb->prefix.'posts
      INNER JOIN
      (SELECT object_id FROM '.$wpdb->prefix.'uam_accessgroup_to_object WHERE '.$wpdb->prefix.'uam_accessgroup_to_object.group_id = %d) w
      ON '.$wpdb->prefix.'posts.id = w.object_id
      LEFT JOIN
      (SELECT '.$wpdb->prefix.'postmeta.post_id as seen FROM '.$wpdb->prefix.'postmeta WHERE '.$wpdb->prefix.'postmeta.meta_key LIKE "mededelingen"
      AND '.$wpdb->prefix.'postmeta.meta_value = %d) y
      ON '.$wpdb->prefix.'posts.ID = y.seen
      WHERE '.$wpdb->prefix.'posts.post_type LIKE "mededelingen"',
      $group_id,
      $current_user_id
    ));

    if($results!=null){
      foreach ($results as $res) {
        $cl = ($res->seen)? '':'nseen';
        $nwcl = ($res->seen)? '':'<span class="nwcl"></span>';
        echo '<li><a data-messageid="'.esc_attr($res->ID).'">'.esc_html($res->post_title).$nwcl.'<span class="'.esc_attr($cl).'">></span></a></li>';
      }
    }
  }

  die();
}

add_action("wp_ajax_get_solo_message_widget", "get_solo_message_widget");
add_action("wp_ajax_nopriv_get_solo_message_widget","get_solo_message_widget");
function get_solo_message_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_message_widget_nonce")) {
    exit("We are not for hack");
  }

  echo '<li><a onclick="document.getElementById(\'messages\').style.display = \'block\'; document.getElementById(\'solomessage\').style.display = \'none\';"><< terug</a></li>';

  if(isset($_REQUEST['messageid'])){
    global $wpdb;
    $messageid = intval($_REQUEST['messageid']);
    $current_user_id = intval(wp_get_current_user()->ID);

    // Verify user has access to this message (IDOR protection)
    $has_access = $wpdb->get_var($wpdb->prepare(
      "SELECT COUNT(*) FROM ".$wpdb->prefix."uam_accessgroup_to_object msg
       INNER JOIN ".$wpdb->prefix."uam_accessgroup_to_object usr ON msg.group_id = usr.group_id
       WHERE msg.object_id = %d AND usr.object_id = %d",
      $messageid,
      $current_user_id
    ));

    if (!$has_access) {
      echo '<li>Access denied</li>';
      die();
    }

    $results = $wpdb->get_results($wpdb->prepare('
      SELECT '.$wpdb->prefix.'posts.post_title, '.$wpdb->prefix.'posts.post_content, seen
      FROM '.$wpdb->prefix.'posts
      LEFT JOIN
      (SELECT '.$wpdb->prefix.'postmeta.post_id as seen FROM '.$wpdb->prefix.'postmeta WHERE '.$wpdb->prefix.'postmeta.meta_key LIKE "mededelingen"
      AND '.$wpdb->prefix.'postmeta.meta_value = %d) y
      ON '.$wpdb->prefix.'posts.ID = y.seen
      WHERE '.$wpdb->prefix.'posts.post_type LIKE "mededelingen" AND '.$wpdb->prefix.'posts.ID = %d',
      $current_user_id,
      $messageid
    ));

    if($results && isset($results[0])){
      $nwcl = ($results[0]->seen)? '':'<span class="nwcl"></span>';
      echo '<li style="padding:30px">
      <p>'.$nwcl.'</p>
      <h2>'.esc_html($results[0]->post_title).'</h2>
      <p>'.wp_kses_post($results[0]->post_content).'</p>
      </li>';

      add_post_meta($messageid, "mededelingen", $current_user_id, true);
    }
  }


  die();
}

add_action("wp_ajax_refresh_message_widget", "refresh_message_widget");
add_action("wp_ajax_nopriv_refresh_message_widget","refresh_message_widget");
function refresh_message_widget(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"get_message_widget_nonce")) {
    exit("We are not for hack");
  }

  $current_user = wp_get_current_user();
  $current_user_id = intval($current_user->ID);
  global $wpdb;
  $results = $wpdb->get_results($wpdb->prepare('
  SELECT groupname, group_id FROM '.$wpdb->prefix.'uam_accessgroup_to_object
  INNER JOIN '.$wpdb->prefix.'uam_accessgroups ON '.$wpdb->prefix.'uam_accessgroups.ID = '.$wpdb->prefix.'uam_accessgroup_to_object.group_id
  WHERE '.$wpdb->prefix.'uam_accessgroup_to_object.object_id = %d AND NOT '.$wpdb->prefix.'uam_accessgroup_to_object.group_id=3',
  $current_user_id
  ));
  if($results!=null){
    foreach ($results as $res) {
      $group_id = intval($res->group_id);
      $cnt = $wpdb->get_results($wpdb->prepare('
        SELECT COUNT(ID) as n, COUNT(seen) as seen
        FROM '.$wpdb->prefix.'posts
        INNER JOIN
        (SELECT object_id FROM '.$wpdb->prefix.'uam_accessgroup_to_object WHERE '.$wpdb->prefix.'uam_accessgroup_to_object.group_id = %d) w
        ON '.$wpdb->prefix.'posts.id = w.object_id
        LEFT JOIN
        (SELECT '.$wpdb->prefix.'postmeta.post_id as seen FROM '.$wpdb->prefix.'postmeta WHERE '.$wpdb->prefix.'postmeta.meta_key LIKE "mededelingen"
        AND '.$wpdb->prefix.'postmeta.meta_value = %d) y
        ON '.$wpdb->prefix.'posts.id = y.seen
        WHERE '.$wpdb->prefix.'posts.post_type LIKE "mededelingen"',
        $group_id,
        $current_user_id
      ));
      $cn = $cnt[0]->n - $cnt[0]->seen;
      $cl = ($cn <= 0)? '': 'nseen';
      $nwcl = ($cn <= 0)? '': '<span class="nwcl"></span>';
      echo '<li><a data-group="'.esc_attr($group_id).'">'.esc_html($res->groupname).$nwcl.'<span class="'.esc_attr($cl).'">'.intval($cnt[0]->n).'</span></a></li>';
    }
  }

  die();
}

add_action("wp_ajax_any_message", "any_message");
add_action("wp_ajax_nopriv_any_message","any_message");
function any_message(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"any_message_nonce")) {
    exit("We are not for hack");
  }

  $current_user = wp_get_current_user();
  $current_user_id = intval($current_user->ID);
  global $wpdb;
  $meta_key = 'mededelingen' . $current_user_id;
  $cnt = $wpdb->get_results($wpdb->prepare('
      SELECT COUNT( ID ) AS count, COUNT( meta_value ) AS seen
      FROM '.$wpdb->prefix.'posts
      LEFT JOIN (
      SELECT post_id, meta_value
      FROM '.$wpdb->prefix.'postmeta
      WHERE meta_key LIKE %s
      ) w ON w.post_id = '.$wpdb->prefix.'posts.id
      LEFT JOIN '.$wpdb->prefix.'uam_accessgroup_to_object ON '.$wpdb->prefix.'uam_accessgroup_to_object.object_id = '.$wpdb->prefix.'posts.id
      INNER JOIN (
        SELECT groupname, group_id
        FROM '.$wpdb->prefix.'uam_accessgroup_to_object
        INNER JOIN '.$wpdb->prefix.'uam_accessgroups ON '.$wpdb->prefix.'uam_accessgroups.ID = '.$wpdb->prefix.'uam_accessgroup_to_object.group_id
        WHERE '.$wpdb->prefix.'uam_accessgroup_to_object.object_id = %d
      ) y ON y.group_id = '.$wpdb->prefix.'uam_accessgroup_to_object.group_id
      WHERE '.$wpdb->prefix.'posts.post_type LIKE "mededelingen"',
      $meta_key,
      $current_user_id
  ));
  echo intval($cnt[0]->count - $cnt[0]->seen);
  die();
}

?>
