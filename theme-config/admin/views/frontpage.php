<?php
//must check that the user has the required capability
if (!current_user_can('manage_options'))
{
  wp_die( __('You do not have sufficient permissions to access this page.') );
}

// variables for the field and option names
$opt_default_name = 'soli_frontpage';
$hidden_default_field_name = 'soli_frontpage_submit_hidden';
$data_default_field_name = 'soli_frontpage';

// Read in existing option value from database
$opt_default_val = get_option( $opt_default_name );

// See if the user has posted us some information
// If they did, this hidden field will be set to 'Y'
if( isset($_POST[ $hidden_default_field_name ]) && $_POST[ $hidden_default_field_name ] == 'Y' ) {
    // Read their posted value
    $opt_default_val = $_POST[ $data_default_field_name ];

    // Save the posted value in the database
    update_option( $opt_default_name, $opt_default_val );

    // Put a "settings saved" message on the screen
    ?>
    <div class="updated"><p><strong><?php _e('settings saved.', 'menu-test' ); ?></strong></p></div>
    <?php
        }
        $fp_info = get_soli_fp_info();

        // Now display the settings editing screen
        echo '<div class="wrap admin-front-page">';
        // header
        echo "<h2>" . get_admin_page_title() . "</h2>";

        echo "<div class=\"updat-nag\">
        </div>";
        ?>

        <form method="post" action="options.php">
        <?php settings_fields( 'frontpage-settings-group' ); ?>
        <?php do_settings_sections( 'frontpage-settings-group' ); ?>
        <table class="form-table frontpage">
          <tr valign="top">
          <th scope="row">Afbeelding header:</th>
          <td><?php default_image_print_box('frontpage_header_image',$fp_info['frontpage_header_image'],null,true); ?></td>
          </tr>
          <tr valign="top">
          <th scope="row">Title voorpagina:</th>
          <td><input type="text" id="frontpage_title" value="<?php echo $fp_info['frontpage_title']; ?>" init-value="<?php echo $fp_info['frontpage_title'];?>"/></td>
          </tr>
          <tr valign="top" style="border-top:1px solid black">
          <th scope="row">Afbeelding voorpagina:</th>
          <td><?php default_image_print_box('frontpage_background', $fp_info['frontpage_background'],null,true); ?></td>
          <td rowspan="5" class="admin-event-options">
            <p><b>Choose event:</b></p><br>
            <input type="radio" name="post"
            id="default"
            class="select_event"
            data-url="<?php echo $fp_info['frontpage_button_link'];?>"
            data-subtitle="<?php echo $fp_info['frontpage_subtitle'];?>"
            data-image="<?php echo wp_get_attachment_image_src($fp_info['frontpage_background'],'full')[0];?>"
            data-imageid="<?php echo $fp_info['frontpage_background'];?>"
            data-date="<?php echo $fp_info['frontpage_subtext'];?>"
            value="0"/>
            <label for="default">default</label>
            <?php
            if(function_exists('tribe_get_events')){
              $events = tribe_get_events(array(
                'posts_per_page' => 6,
                'start_date' => date('Y-m-d H:i:s')
              ));
              if($events){
                foreach ($events as $post) {
                  echo frontpage_event_option($post);
                }
              }
            } else {
              echo "Tribe Event Calendar plugin is not installed.";
            }
            ?>
           </td>
          </tr>
          <tr valign="top">
          <th scope="row">Subtitle:</th>
          <td><input type="text" id="frontpage_subtitle" value="<?php echo $fp_info['frontpage_subtitle']; ?>" init-value="<?php echo $fp_info['frontpage_subtitle']; ?>"/></td>
          </tr>
          <tr valign="top">
          <th scope="row">Datum:</th>
          <td><input type="text" id="frontpage_subtext" value="<?php echo $fp_info['frontpage_subtext']; ?>" init-value="<?php echo $fp_info['frontpage_subtext']; ?>"/></td>
          </tr>
          <tr valign="top">
          <th scope="row">Button link:</th>
          <td>
            <input type="text" id="frontpage_button_link" value="<?php echo $fp_info['frontpage_button_link']; ?>" init-value="<?php echo $fp_info['frontpage_button_link']; ?>"/>
            <p><i>Begin url with 'http://' or 'https://'</p>
          </td>
          </tr>
        </table>

        <p class="submit">
          <input name="submit" id="submit_soli_images" class="button button-primary" value="Wijzigingen opslaan" type="button" data-nonce="<?php echo wp_create_nonce("save_default_images_nonce"); ?>">
        </p>
      </form>
    </div>
