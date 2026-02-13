<?php
/**
 * File adding middle name field for every user
 **/


add_action( 'show_user_profile', 'soli_show_extra_profile_fields_middle_name' );
add_action( 'edit_user_profile', 'soli_show_extra_profile_fields_middle_name' );

function soli_show_extra_profile_fields_middle_name( $user ) {
	$middlename = get_the_author_meta( 'middle_name', $user->ID );
	?>
	<h3><?php esc_html_e( 'Extra personal info', 'crf' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="middle_name"><?php esc_html_e( 'Tussenvoegsel', 'crf' ); ?></label></th>
			<td>
				<input type="text"
			       id="middle_name"
			       name="middle_name"
			       value="<?php echo esc_attr( $middlename ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'personal_options_update', 'soli_update_profile_fields_middle_name' );
add_action( 'edit_user_profile_update', 'soli_update_profile_fields_middle_name' );

function soli_update_profile_fields_middle_name( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	if ( ! empty( $_POST['middle_name'] ) ) {
		update_user_meta( $user_id, 'middle_name', $_POST['middle_name'] );
	}
}
