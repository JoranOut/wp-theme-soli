<?php
  //must check that the user has the required capability
  if (!current_user_can('manage_options'))
  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  // variables for the field and option names
  $opt_default_name = 'soli_default_image';
  $hidden_default_field_name = 'soli_default_submit_hidden';
  $data_default_field_name = 'soli_default_image';

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
          // Now display the settings editing screen
          echo '<div class="wrap soli_imaging">';
          // header
          echo "<h2>" . get_admin_page_title() . "</h2>";

          echo "<div class=\"updat-nag\">In onderstaande opties kunt u voor elke group (opgehaald vanuit de
          UAM-plugin) een afbeelding kiezen. Deze afbeeldingen worden alleen geladen als er geen afbeelding
          aan een post of pagina toegevoegd is (uitgelichte afbeelding). Op het moment dat er geen van de
          zoektermen (groepnamen) gevonden wordt, zal de afbeelding uit de bovenste categorie (Alle Soli-leden)
          gekozen worden. Als er meerdere foto's in een categorie gekozen zijn, wordt een willekeurige foto
          toegewezen aan de post.
          </div>";

          if(!soli_table_exists()){
            create_soli_table();
          }

          $groups = get_soli_groups();
          ?>

          <form method="post" action="options.php">
          <?php settings_fields( 'default-imaging-settings-group' ); ?>
          <?php do_settings_sections( 'default-imaging-settings-group' ); ?>
          <table class="form-table">
            <?php
              foreach ($groups as $group) {
                ?>
                <tr valign="top">
                <th scope="row"><?php echo $group[1];?></th>
                <td><?php
                for ($i=0; $i < 5; $i++) {?>
                  <td><?php default_image_print_box($group[0],$group[2][$i]); ?></td>
                  <?php
                }
                ?></td>
                </tr>
                <?php
              }
            ?>
          </table>
      <p class="submit">
        <input name="submit" id="submit_soli_images" class="button button-primary" value="Wijzigingen opslaan" type="button" data-nonce="<?php echo wp_create_nonce("save_default_images_nonce"); ?>">
      </p>
      </form>
      </div>
