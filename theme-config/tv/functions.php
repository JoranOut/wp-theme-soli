<?php add_action( 'init', function() {
    add_theme_support('post-thumbnails');
    $labels = xcompile_post_type_labels('Tv bericht','Tv berichten');
    $type = 'tv';
    $supports = ['title', 'editor', 'revisions', 'thumbnail'];
    $arguments =  [
      'rest_base' => 'tv',
      'show_in_rest' => true,
      'hierarchical' => false,
      'supports' => $supports,
      'public' => true,
      'description' => 'Optredende artiesten',
      'menu_icon' => 'dashicons-desktop',
      'menu_position' => 4,
      'has_archive' => false,
      'taxonomies' => array(),
      'register_meta_box_cb' => 'wpt_add_tv_metaboxes',
      'labels'  => $labels ] ;
    register_post_type( $type, $arguments);
});

function xcompile_post_type_labels($singular = 'Post', $plural = 'Posts') {
    $p_lower = strtolower($plural);
    $s_lower = strtolower($singular);

    return [
        'name' => $plural,
        'singular_name' => $singular,
        'add_new' => "Nieuw $singular",
        'add_new_item' => "Nieuw $singular",
        'edit_item' => "Edit $singular",
        'view_item' => "Bekijk $singular",
        'view_items' => "Bekijk $plural",
        'search_items' => "Zoek $plural",
        'not_found' => "Geen $p_lower gevonden",
        'not_found_in_trash' => "Geen $p_lower gevonden in de prullenbak",
        'parent_item_colon' => "Parent $singular",
        'all_items' => "Alle $plural",
        'archives' => "$singular Archief",
        'attributes' => "$singular Attributes",
        'insert_into_item' => "Voeg toe aan $s_lower",
        'uploaded_to_this_item' => "Geüploaded naar $s_lower",
    ];
}

add_filter( 'post_updated_messages', function($messages) {
    global $post, $post_ID;
    $link = esc_url( get_permalink($post_ID) );

    $messages['tv_berichten'] = array(
        0 => '',
        1 => sprintf( __('Tv-bericht geüpdated. <a href="%s">Bekijk tv-bericht</a>'), $link ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Tv-bericht geüpdated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Tv-berichtrestored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Tv-bericht gepubliceerd. <a href="%s">Bekijk Artiest</a>'), $link ),
        7 => __('Tv-bericht opgeslagen.'),
        8 => sprintf( __('Tv-bericht submitted. <a target="_blank" href="%s">Preview Artist</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Tv-bericht scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview artist</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), $link ),
        10 => sprintf( __('Tv-bericht draft updated. <a target="_blank" href="%s">Preview artist</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
});

add_filter('manage_posts_columns', 'posts_columns', 5);
add_action('manage_posts_custom_column', 'posts_custom_columns', 5, 1);
function posts_columns($defaults){
    $defaults['riv_post_thumbs'] = __('Thumbs');
    return $defaults;
}

function posts_custom_columns($column_name, $id = null){
  if ($id){
    if($column_name === 'riv_post_thumbs'){
        echo the_post_thumbnail( 'thumbnail');
    }
  }
}

function wpt_add_tv_metaboxes() {
	add_meta_box(
		'tv_post_type',
		'Type',
		'tv_post_type_html',
		'tv',
		'normal',
		'low'
	);
}

function tv_post_type_html() {
	global $post;
	// Nonce field to validate form request came from current site
	wp_nonce_field( basename( __FILE__ ), 'tv_post_type_fields' );
	// Get the location data if it's already been entered
	$selected = get_post_meta( $post->ID, 'tv_post_type', true );
	// Output the field
	$buttons = array(array("Tekst met vullende afbeelding", "pic_with_text"),
                   array("Tekst met volledige afbeelding", "pic_with_text_contain"),
                   array( "Alleen afbeelding", "pic_only"));

  for ($i=0; $i < sizeof($buttons); $i++) {
    $sel = ($buttons[$i][1]===$selected || ($selected==NULL && $i == 0))? "checked" : "";
    echo '<div class="tv_post_type_option">
      <input type="radio" id="'.$buttons[$i][1].'" name="tv_post_type" value="'.$buttons[$i][1].'"
             '.$sel.' style="margin:0.4rem">
      <div class="'.$buttons[$i][1].'"></div>
      <label for="'.$buttons[$i][1].'">'.$buttons[$i][0].'</label>
    </div>';
  }
}

function wpt_save_events_meta( $post_id, $post ) {
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['tv_post_type'] ) || ! wp_verify_nonce( $_POST['tv_post_type_fields'], basename(__FILE__) ) ) {
		return $post_id;
	}

	$events_meta['tv_post_type'] = esc_textarea( $_POST['tv_post_type'] );
	// Cycle through the $events_meta array.
	// Note, in this example we just have one item, but this is helpful if you have multiple.
	foreach ( $events_meta as $key => $value ) :
		// Don't store custom data twice
		if ( 'revision' === $post->post_type ) {
			return;
		}
		if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}
		if ( ! $value ) {
			// Delete the meta key if there's no value
			delete_post_meta( $post_id, $key );
		}
	endforeach;
}
add_action( 'save_post', 'wpt_save_events_meta', 1, 2 );

function wpse_cpt_enqueue( $hook_suffix ){
    $cpt = 'tv';

    if( in_array($hook_suffix, array('post.php', 'post-new.php') ) ){
        $screen = get_current_screen();
        if( is_object( $screen ) && $cpt == $screen->post_type ){
          if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
          }
            wp_enqueue_script( 'tv-admin', get_stylesheet_directory_uri() . '/assets/js/tv-admin.js', array('jquery'), null, false );
            wp_localize_script( 'tv-admin', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            wp_enqueue_script( 'tv-admin' );
        }
    }
    if( in_array($hook_suffix, array('tv_page_tv_settings') ) ){
        $screen = get_current_screen();
        if( is_object( $screen ) && $cpt == $screen->post_type ){
          if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
          }
            wp_enqueue_script( 'tv-admin-settings', get_stylesheet_directory_uri() . '/assets/js/tv-admin-settings.js', array('jquery'), null, false );
            wp_localize_script( 'tv-admin-settings', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
            wp_enqueue_script( 'tv-admin-settings' );
        }
    }
}
add_action( 'admin_enqueue_scripts', 'wpse_cpt_enqueue');

add_action('admin_menu', 'tv_add_pages');
function tv_add_pages() {
   add_submenu_page('edit.php?post_type=tv', 'Instellingen', 'Instellingen', 'manage_options', 'tv_settings', 'tv_sublevel_page2');
}

function tv_sublevel_page2() {
  include 'views/settings.php';
}

function get_tv_posts($amount = -1){
  $args = array( 'post_type' => 'tv', 'posts_per_page' => $amount );
  $loop = new WP_Query( $args );
  return $loop->posts;
}

add_action("wp_ajax_save_tv_settings", "save_tv_settings");
add_action("wp_ajax_nopriv_save_tv_settings","save_tv_settings");
function save_tv_settings(){
  if (!wp_verify_nonce($_REQUEST['nonce'],"save_tv_settings_nonce")) {
    exit("We are not for hack");
  }

  $infos = json_decode(stripslashes($_REQUEST["info"]));
  foreach($infos as $info){
    if ( ! add_post_meta( $info->name, $_REQUEST["type"], $info->value, true ) ) {
     update_post_meta( $info->name, $_REQUEST["type"], $info->value );
    }
  }
  die();
}

function get_tv_gallery($items, $background, $calendar = false, $post){
  if($calendar){
    return tv_calendar_gallery($items, $background, $post);
  } else {

  }
}

function tv_calendar_gallery($items, $background, $post){
  $tv_gallery = "<div class=\"stage gallery\" style=\"background-image:url('".$background."')\"><div class=\"items\">";
  $tv_gallery .= "<h1>Agenda</h1>";
  foreach ($items as $item) {
    if(get_post_meta($item->ID,"invisible_on_gallery")[0]!=1){
      $date = DateTime::createFromFormat('Y-m-d H:i:s',$item->event_date);
      $item_class = ($item->ID === $post->ID)?'item active':'item';
      $tv_gallery .= "<div class=\"".$item_class."\">";
        $tv_gallery .= "<div><h4>".$date->format('d-m')."</h4>";
        $tv_gallery .= "<h4>".$date->format('H:i')."</h4></div>";
        $tv_gallery .= "<div><h4>".$item->post_title."</h4>";
        $tv_gallery .= "<h4>".tribe_get_venue($item)."</h4></div>";
      $tv_gallery .= "</div>";
    }
  }
  $tv_gallery .= "<div class=\"link\">www.soli.nl/agenda</div></div>";
  $tv_gallery .= load_template_part( 'template-parts/tv', 'event' );
  $tv_gallery .= "</div>";
  return $tv_gallery;
}

function load_template_part($template_name, $part_name=null) {
    ob_start();
    get_template_part($template_name, $part_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}
?>
