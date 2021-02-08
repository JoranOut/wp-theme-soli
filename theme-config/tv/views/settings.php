
<div class="wrap">
  <h1 class="wp-heading-inline">Instellingen - Enkele berichten</h1>
  <form method="post" action="options.php">
     <table class="form-table stand-alone">
       <tr>
         <th scope="row">Kalender items:</th>
         <td><?php

         $userid = 0;
         $role_name = 0;
         $myrows = get_myrows(0);

          $depr_events = tribe_get_events(array(
            'posts_per_page' => 6,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d', strtotime("+21 days")),
            'post__not_in' => $myrows
          ));

          $events = tribe_get_events(array(
            'posts_per_page' => 6 + count($depr_events),
            'start_date' => date('Y-m-d H:i:s'),
            'post__not_in' => $myrows
          ));
          if($events){
            $i = 0;
            foreach ($events as $event) {
              $coming = (++$i > 6) ? '(binnenkort)':'';
              $coming_color = ($i > 6) ? 'style="color:red"':'';
              $checked = (get_post_meta($event->ID,"invisible_on_tv")[0])?'':'checked';
              echo '<div style="display:block">';
              echo '<input type="checkbox" name="'.$event->ID.'" value="" '.$checked.'/>';
              echo '<label '.$coming_color.' for="'.$event->ID.'">'.$event->post_title.' '.$coming.'</label>';
              echo '</div>';
            }
          }
         ?></td>
       </tr>
       <tr valign="top">
         <th scope="row">Tv items:</th>
         <td>
           <?php
           $tv_posts = get_tv_posts();
            if($tv_posts){
              foreach ($tv_posts as $tv_post) {
                $checked = (get_post_meta($tv_post->ID,"invisible_on_tv")[0])?'':'checked';
                echo '<div style="display:block">';
                echo '<input type="checkbox" name="'.$tv_post->ID.'" value="" '.$checked.'/>';
                echo '<label for="'.$tv_post->ID.'">'.$tv_post->post_title.'</label>';
                echo '</div>';
              }
            }
           ?>
         </td>
       </tr>
     </table>
     <p class="submit">
       <input name="submit" id="submit_soli_tv_settings_sa" class="button button-primary" value="Wijzigingen opslaan" type="button" data-nonce="<?php echo wp_create_nonce("save_tv_settings_nonce"); ?>">
     </p>
     <h1 class="wp-heading-inline">Instellingen - Gallery berichten</h1>
     <table class="form-table gallery">
       <tr>
         <th scope="row">Kalender items:</th>
         <td><?php
          $myrows = get_myrows(0);

          $depr_events = tribe_get_events(array(
            'posts_per_page' => 6,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d', strtotime("+21 days")),
            'post__not_in' => $myrows
          ));

          $events = tribe_get_events(array(
            'posts_per_page' => 6 + count($depr_events),
            'start_date' => date('Y-m-d H:i:s'),
            'post__not_in' => $myrows
          ));
          if($events){
            $i = 0;
            foreach ($events as $event) {
              $coming = (++$i > 6) ? '(binnenkort)':'';
              $coming_color = ($i > 6) ? 'style="color:red"':'';
              $checked = (get_post_meta($event->ID,"invisible_on_gallery")[0])?'':'checked';
              echo '<div style="display:block">';
              echo '<input type="checkbox" name="'.$event->ID.'" value="" '.$checked.'/>';
              echo '<label '.$coming_color.' for="'.$event->ID.'">'.$event->post_title.' '.$coming.'</label>';
              echo '</div>';
            }
          }
         ?></td>
       </tr>
     </table>
     <p class="submit">
       <input name="submit" id="submit_soli_tv_settings_gallery" class="button button-primary" value="Wijzigingen opslaan" type="button" data-nonce="<?php echo wp_create_nonce("save_tv_settings_nonce"); ?>">
     </p>
  </form>
</div>
