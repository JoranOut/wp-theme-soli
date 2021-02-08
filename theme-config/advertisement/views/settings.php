
<div class="wrap">
  <h1 class="wp-heading-inline">Instellingen</h1>
  <form method="post" action="options.php">
    <?php
     settings_fields('advertenties');
     do_settings_sections('advertenties');?>
     <table class="form-table">
       <tr valign="top">
         <th scope="row">Time-lapse (ms) --not functioning</th>
         <td><input type="text" name="time-lapse" value="<?php echo esc_attr( get_option('time-lapse') ); ?>" /></td>
       </tr>
     </table>
     <?php submit_button();
    ?>
  </form>
</div>
