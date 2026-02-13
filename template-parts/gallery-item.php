<?php
/**
 * Template part for displaying posts with excerpts
 *
 * Used in Search Results and for Recent Posts in Front Page panels.
 *
 * @since Soli 2.0
 * @version 2.0
 */
global $post;
?>
<div id="post-<?php the_ID(); ?>" <?php post_class("personal_gallery_item"); ?> onclick="window.location.href='<?php echo esc_url( get_permalink() )?>'">
  <div class="personal_gallery_img" style="background-image: linear-gradient(-135deg, rgba(170, 42, 42, .1) 0%, rgba(75, 33, 191, .1) 100%),
    url(<?php echo get_soli_post_image($post,'medium'); ?>)">
    <?php
    if(get_post_type($post->ID)=="mededelingen"){
      if(!metadata_exists('post', $post->ID, "mededelingen".wp_get_current_user()->ID)){
        if(isset($results[0])){
          echo '<div class="new">nieuw</div>';
        }
      }
    }

    ?>
  </div>
  <div class="personal_gallery_sub">
    <p class="hovergroup"><?php echo get_uam_groups_by_ID($post->ID) ?></p>
    <p>
      <?php
      if ( 'post' === get_post_type() ) :
      echo get_the_time('d.F.Y');
      elseif ( 'tribe_events' === get_post_type() ) :
      $date = strtotime($post->event_date);
      echo date('d F, y',$date);
      endif;
      ?>
    </p>
    <?php the_title( '<h2 class="entry-title">', '</h2>' );?>
    <p class="hoversub"><?php echo wp_trim_words(wp_strip_all_tags(get_the_content()),8);?></p>
  </div>
</div>
