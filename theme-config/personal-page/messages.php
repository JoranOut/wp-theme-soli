<?php
/**
 * File to handle messages through custom posts
 **/

function create_memo_post_type() {
  $labels = array(
    'name'                => 'Mededelingen',
    'singular_name'       => 'Mededeling',
    'menu_name'           => 'Mededelingen',
    'all_items'           => 'Alle Mededelingen',
    'view_item'           => 'Bekijk Mededeling',
    'add_new_item'        => 'Nieuwe Mededeling',
    'add_new'             => 'Nieuwe Mededeling',
    'edit_item'           => 'Bewerk Mededeling',
    'update_item'         => 'Update Mededeling',
    'search_items'        => 'Zoek Mededeling',
    'not_found'           => 'Niet gevonden',
    'not_found_in_trash'  => 'Niet in de prullenbak gevonden',
  );
  $args = array(
    'label'               => 'mededelingen',
    'description'         => 'Mededelingen op de site',
    'labels'              => $labels,
    // Features this CPT supports in Post Editor
    'supports'            => array( 'title','editor'),
    /* A hierarchical CPT is like Pages and can have
    * Parent and child items. A non-hierarchical CPT
    * is like Posts.
    */
    'hierarchical'        => false,
    'public'              => true,
    'menu_position'       => 6,
    'menu_icon'           => 'dashicons-format-aside',
    'can_export'          => true,
    'has_archive'         => false,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
  );
  register_post_type( 'mededelingen', $args );
}
add_action( 'init', 'create_memo_post_type' );
?>
