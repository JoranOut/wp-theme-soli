<?php
/**
 * The template for displaying personal page
 *
 * TEMPLATE NAME: Personal page
 * @since Soli 2.0
 * @version 2.0
 */

 if ( !is_user_logged_in() ) {
    auth_redirect();
    die();
 }

 $current_user = wp_get_current_user();
 $user_id = $current_user->ID;
 get_header();
?>

<div class="personal" style="padding-top:<?php if(is_admin_bar_showing()){
  echo '180px';
} ?>">
  <div class="header">
    <ul>
      <li><a href="<?php echo get_home_url(); ?>">Website</a></li>
      <li><a href="<?php echo get_home_url(); ?>/agenda/month/">Mijn Agenda</a></li>
      <li style="margin-left: auto"><a href="<?php echo wp_logout_url( home_url() ); ?>">Uitloggen</a></li>
    </ul>
  </div>
  <div class="header personal">
    <div id="profileoption" class="profileoption">?</div>
    <div id="profilehelp" class="profilehelp">Iets mis met deze gegevens? Neem contact op met de ledenadministratie: ledenadministratie@soli.nl</div>
    <div class="profilename">
      <h1 class="font-resizer"> <?php echo $current_user->display_name; ?></h1>
      <span><?php echo $current_user->user_email; ?></span>
    </div>
    <div class="profilepicture">
      <img class="white" src="<?php bloginfo('template_url'); ?>/assets/img/user_white.svg" />
    </div>
    <div class="profilegroups"><?php
    global $wpdb;
    $results = $wpdb->get_results('SELECT DISTINCT '.$wpdb->prefix.'uam_accessgroups.groupname FROM '.$wpdb->prefix.'uam_accessgroup_to_object INNER JOIN '.$wpdb->prefix.'uam_accessgroups ON '.$wpdb->prefix.'uam_accessgroups.ID = '.$wpdb->prefix.'uam_accessgroup_to_object.group_id WHERE '.$wpdb->prefix.'uam_accessgroup_to_object.object_id ='.$current_user->ID.' AND NOT '.$wpdb->prefix.'uam_accessgroup_to_object.group_id=3');
    if($results!=null){
      foreach ($results as $res) {
        echo '<span>'.($res->groupname).'</span>';
      }
    } ?>
    </div>
  </div>
  <div class="header files">
    <ul>
      <li><a href="<?php echo wp_get_attachment_url(9344); ?>">Statuten</a></li>
      <li><a href="<?php echo wp_get_attachment_url(9343); ?>">Huishoudelijk reglement</a></li>
    </ul>
  </div>
  <h1 class="container_title">Mijn Mededelingen</h1>
  <div class="item_container">
    <div class="move">
      <?php
        $posts = get_posts(array(
          'posts_per_page' => 16,
          'post_type' => 'mededelingen',
          'order' => 'DESC',
          'exclude' => $myrows));
        if($posts){
          foreach ($posts as $post) {
            setup_postdata($post);
            get_template_part( '/template-parts/gallery', 'item' );
          }
          wp_reset_postdata();
          get_template_part( '/template-parts/gallery', 'more' );
        } else {
          get_template_part( '/template-parts/gallery', 'none' );
        }
      ?>
    </div>
    <div class="nav left"></div>
    <div class="nav right"></div>
  </div>
  <h1 class="container_title">Mijn Nieuws</h1>
  <div class="item_container">
    <div class="move">
    <?php
      global $wpdb;
      $posts_avecGroupe = array();
      $posts_avecGroupe = $wpdb->get_col("SELECT T2.object_id FROM wp_uam_accessgroup_to_object T1
                                        INNER JOIN wp_uam_accessgroup_to_object T2 ON T1.group_id=T2.group_id
                                        INNER JOIN wp_posts ON wp_posts.ID=T2.object_id
                                        WHERE T1.object_id = '".$user_id."' AND T2.object_type='post'
                                        ORDER BY post_date DESC LIMIT 16");
      $posts = get_posts( array('post__in'=>$posts_avecGroupe, "numberposts"=>16));
      if($posts){
        foreach ($posts as $post){
          setup_postdata($post);
          get_template_part( '/template-parts/gallery', 'item' );
        }
        wp_reset_postdata();
        get_template_part( '/template-parts/gallery', 'more' );
      } else {
        get_template_part( '/template-parts/gallery', 'none' );
      }
    ?>
    </div>
    <div class="nav left"></div>
    <div class="nav right"></div>
  </div>
  <?php
  get_template_part( 'template-parts/sponsorkliks', 'frontpage' );
  ?>
  <h1 class="container_title">Mijn Agenda</h1>
  <div class="item_container">
    <div class="move">
      <?php include_once( ABSPATH . 'wp-admin/includes/user-access-manager.php' );
      $plugin_active = is_plugin_active( 'user-access-manager/user-access-manager.php' );
      $user_id = wp_get_current_user()->ID;

      $posts = tribe_get_events(array(
          'posts_per_page' => 8,
          'start_date' => date('Y-m-d H:i:s')
      ));

      global $post;
      if($posts){
        foreach ($posts as $post){
          setup_postdata($post);
          get_template_part( '/template-parts/gallery', 'item' );
        }
        wp_reset_postdata();
        get_template_part( '/template-parts/gallery', 'more' );
      } else {
        get_template_part( '/template-parts/gallery', 'none' );
      }
      ?>
    </div>
    <div class="nav left"></div>
    <div class="nav right"></div>
  </div>
  <h1 class="container_title">Muziek</h1>
  <div class="item_container">
      <div class="move">
        <?php get_template_part( '/template-parts/gallery', 'none' );?>
      </div>
      <div class="nav left"></div>
      <div class="nav right"></div>
    </div>
</div>

<?php
  get_footer();
 ?>
