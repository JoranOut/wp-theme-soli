<?php
/**
 * File to handle advertisement through custom posts
 **/

function create_post_type() {
  $labels = array(
    'name'                => 'Advertenties',
    'singular_name'       => 'Advertentie',
    'menu_name'           => 'Advertenties',
    'all_items'           => 'Alle Advertenties',
    'view_item'           => 'Bekijk Advertentie',
    'add_new_item'        => 'Nieuwe Advertentie',
    'add_new'             => 'Nieuwe Advertentie',
    'edit_item'           => 'Bewerk Advertentie',
    'update_item'         => 'Update Advertentie',
    'search_items'        => 'Zoek Advertentie',
    'not_found'           => 'Niet gevonden',
    'not_found_in_trash'  => 'Niet in de prullenbak gevonden',
  );
  $args = array(
    'label'               => 'advertenties',
    'description'         => 'Advertenties op de site',
    'labels'              => $labels,
    // Features this CPT supports in Post Editor
    'supports'            => array( 'title',),
    /* A hierarchical CPT is like Pages and can have
    * Parent and child items. A non-hierarchical CPT
    * is like Posts.
    */
    'hierarchical'        => false,
    'public'              => true,
    'menu_position'       => 5,
    'menu_icon'           => 'dashicons-tickets-alt',
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
  );
  register_post_type( 'advertenties', $args );
}
add_action( 'init', 'create_post_type' );

function add_advert_metaboxes(){
  add_meta_box(
    'advert_link',
    'Advertentie link',
    'meta_box_link',
    'advertenties',
    'normal',
    'default'
  );
  add_meta_box(
      'advert_pic',
      'Advertentie Afbeelding',
      'meta_box_pic',
      'advertenties',
      'normal',
      'high'
    );
}
add_action('add_meta_boxes', 'add_advert_metaboxes');

function meta_box_link(){
  global $post;
  // Nonce field to validate form request came from current site
  wp_nonce_field( basename( __FILE__ ), 'event_fields' );
  // Get the location data if it's already been entered
  $location = get_post_meta( $post->ID, 'ad_link', true );
  // Output the field
  echo '<h5>Begin altijd met "http://" of "https://"</h4>
  <input type="text" name="ad_link" value="' . esc_textarea( $location )  . '" class="widefat">';
}

function ad_default_image_uploader_field($name, $value = ''){
  $image = ' button">Upload image';
  $image_size = 'full';
  $display = 'none';

  if( $image_attributes = wp_get_attachment_image_src($value,$image_size)){
    $image = '"><img src="' . $image_attributes[0] . '" style="width:300px;display:block;" />';
		$display = 'inline-block';
  }

  return '
	<div>
		<a href="#" class="default_image_button' . $image . '</a>
		<input type="hidden" class="image" name="' . $name . '" id="' . $name . '" value="' . $value . '" init-value="' . $value . '" autocomplete="off" />
		<a href="#" class="default_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
	</div>';
}

function soli_theme_adv_admin_include_jq() {
  global $post;

  if(!(get_current_screen()->base === "post")) {
      return;
    }
  if ( ! did_action( 'wp_enqueue_media' ) ) {
 		wp_enqueue_media();
 	}
	wp_enqueue_script( 'myuploadscript', get_stylesheet_directory_uri() . '/assets/js/theme-admin.js', array('jquery'), null, false );
  wp_localize_script( 'myuploadscript', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
   wp_enqueue_script( 'myuploadscript' );
}
add_action( 'admin_enqueue_scripts', 'soli_theme_adv_admin_include_jq' );

function meta_box_pic($post){
  global $content_width, $_wp_additional_image_sizes;
  $image_id = get_post_meta($post->ID, 'ad_pic',true);

  echo ad_default_image_uploader_field( "_listing_cover_image", $image_id);
}

function get_advertisements(){
    global $post;
    $args = array(
      'post_type' => 'advertenties',
      'posts_per_page' => 5,
      'orderby' => 'rand',
    );
    $loop = new WP_Query( $args );
    if($loop){
  ?><article class="recla" data-time='5000'>
    <div class="container">
      <?php
        while ( $loop->have_posts() ) : $loop->the_post();
          ?>
          <a class="recl"
              title="<?php the_title() ?>"
              style="background-image:url('<?php echo wp_get_attachment_url(get_post_meta($post->ID, 'ad_pic',true),'large'); ?>')"
              href="<?php echo get_post_meta($post->ID, 'ad_link',true); ?>" target="_blank">
              <?php
              if(wp_get_attachment_url(get_post_meta($post->ID, 'ad_pic',true))==null){
                the_title();
              }
              ?>
          </a>
          <?php
        endwhile;
        $size = $loop->found_posts;
      ?>
      <div class="nav-dots" data-size="<?php echo $size; ?>">
        <?php
          for ($i=0;$i<$size;$i++){
            echo '<div data-ad="'.$i.'"></div>';
          }
        ?>
      </div>
    </div>
    <div class="nav left"></div>
    <div class="nav right"></div>
  </article><?php
  }
}

add_action( 'admin_enqueue_scripts', 'my_enqueue' );
function my_enqueue($hook) {
  if ( 'post-new.php' != $hook && 'post.php' != $hook) {
      return;
  }
  wp_enqueue_media();
  wp_enqueue_script( 'my_custom_script', get_template_directory_uri() . '/assets/js/advert-admin-edit.js' );
}

add_action( 'save_post', 'ad_pic_save', 10, 1 );
function ad_pic_save ( $post_id ) {
	if( isset( $_POST['_listing_cover_image'] ) ) {
 	  $image_id = (int) $_POST['_listing_cover_image'];
 		update_post_meta( $post_id, 'ad_pic', $image_id );
 	}
  if( isset( $_POST['ad_link'] ) ) {
 	  $ad_link = $_POST['ad_link'];
    if(!get_post_meta( $post->ID, 'ad_link', true )){
      update_post_meta( $post_id, 'ad_link', $ad_link);
    } else {
      add_post_meta( $post_id, 'ad_link', $ad_link);
    }
 	}
}

function register_advertention_settings(){
  register_setting('advertenties','time-lapse');
  register_setting('advertenties','pages');
}
add_action('admin-init','register_advertention_settings');


add_action('admin_menu', 'mt_add_pages');
function mt_add_pages() {
   add_submenu_page('edit.php?post_type=advertenties', 'Instellingen', 'Instellingen', 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

function mt_sublevel_page2() {
  include 'views/settings.php';
}
?>
