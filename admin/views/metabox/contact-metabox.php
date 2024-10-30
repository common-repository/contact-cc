<?php
$post_id = $post->ID;
$options_slug = '_chch_contact_free_'
?>

<div class="cmb2-wrap form-table">
	<div class="cmb2-metabox cmb-field-list">
		<!--
		@SECTION: General
		@Field: Contact Type
		-->
		<div class="cmb-row cmb-type-select cmb_id__<?php echo $options_slug . 'contact_type' ?>">
			<div class="cmb-th">
				<label for="<?php echo $options_slug . 'contact_type' ?>">Contact Type:</label>
			</div>
			<div class="cmb-td">
				<?php
				$saved_type   = get_post_meta( $post->ID, $options_slug . 'contact_type', true );
				$contact_type = $saved_type ? $saved_type : 'email';
				?>

				<select id="<?php echo $options_slug . 'contact_type' ?>" size="1" name="<?php echo $options_slug . 'contact_type' ?>" data-select-target=".contact-type-wrapper">
					<?php
					$contact_types = array(
						'email'     => 'Email',
						'mailchimp' => 'MailChimp - Available in Pro',
					);

					foreach ( $contact_types as $type_code => $type_name ) {
						$selected = ( $type_code == $contact_type ) ? 'selected' : '';
						if ($type_code == 'mailchimp') {
							$selected = 'disabled';
						}
						printf( '<option value="%s" %s>%s</option>', $type_code, $selected, $type_name );
					}
					?>
				</select><a href="http://ch-ch.org/contactpro" class="getpro" target="_blank">Get Pro</a>

			</div>
		</div>

		<!--
		@SECTION: Email
		@Field: Email Address
		-->
		<?php
		// get saved email data from db.
		$email_options = get_post_meta( $post_id, 'email_data', true );

		$email = ( isset( $email_options[ 'email_address' ] ) && ! empty( $email_options[ 'email_address' ] ) ) ? $email_options[ 'email_address' ] : get_option( 'admin_email' );

		$hide_section = ( $contact_type !== 'email' ) ? 'hide-section' : '';

		printf( '<div class="cmb-row contact-type-wrapper %s email-wrapper">', $hide_section );
		?>
		<div class="cmb-th">
			<label for="<?php echo $options_slug . 'email_address' ?>">E-mail Address:</label>
		</div>
		<div class="cmb-td">
			<input class="cmb_text_medium" name="<?php echo $options_slug . 'email_address' ?>" id="<?php echo $options_slug . 'email_address' ?>" value="<?php echo $email; ?>" type="text"/>
			<br/>
			<span class="cmb_metabox_description">Subscription notifications will be sent to this email. If there is no email provided, admin email will be used.</span>
		</div>
	</div>

	<!--
		@SECTION: Email
		@Field: Email Address
		-->
	<?php

	$subject = ( isset( $email_options[ 'subject' ] ) && ! empty( $email_options[ 'subject' ] ) ) ? $email_options[ 'subject' ] : 'New contact form from: ' . esc_url( get_bloginfo( 'url' ) );

	printf( '<div class="cmb-row contact-type-wrapper %s email-wrapper">', $hide_section );
	?>
	<div class="cmb-th">
		<label for="<?php echo $options_slug . 'subject' ?>">E-mail Subject:</label>
	</div>
	<div class="cmb-td">
		<input class="cmb_text_medium" name="<?php echo $options_slug . 'subject' ?>" id="<?php echo $options_slug . 'subject' ?>" value="<?php echo esc_attr( $subject ); ?>" type="text"/>
		<br/>
		<span class="cmb_metabox_description">This will be the subject of the notifications from your contact form.</span>
	</div>
</div>

<!--
@SECTION: Email
@Field: Message Body
-->
<?php printf( '<div class="cmb-row contact-type-wrapper %s email-wrapper">', $hide_section ); ?>
<div class="cmb-th">
	<label for="<?php echo $options_slug . 'message_body' ?>">E-mail Notification:</label>
</div>
<?php

$default_message = __( "This is just a sample message. Edit it and put the Field Id's in the curly brackets to send them in the e-mail message.\n\n--
This message was sent from a contact form on ".esc_url( get_bloginfo( 'url' )), $this->plugin_slug );

$message = ( isset( $email_options[ 'message_body' ] ) && ! empty( $email_options[ 'message_body' ] ) ) ? $email_options[ 'message_body' ] : $default_message;

?>
<div class="cmb-td">
	<textarea class="cmb-type-textarea-code" name="<?php echo $options_slug . 'message_body' ?>"><?php echo $message; ?></textarea>
	<br/>
			<span class="cmb_metabox_description">You will receive this message when a contact form is sent from your website.<br/>Use Field Id's (in curly brackets) defined in "E-Mail Fields" section below.<br/> For example, if a Field Id is <strong>email</strong>, put <strong>{email}</strong> in the box above.<br/>
			If you need more information, see the <a href="http://ch-ch.org/customfields" target="_blank">Documentation</a>.</span>
</div></div>


<!--
@SECTION: Email
@Field: Email Fields Repeater
-->
<?php printf( '<div class="cmb-row contact-type-wrapper %s email-wrapper">', $hide_section ); ?>
<div class="cmb-th"></div>
<div class="cmb-td">
<span class="cmb_metabox_description">Edit the sample field and/or add new ones.<br>Assign a unique Field Id to each field. If there is no Field Id, the field <strong>will not</strong> be visible in the Customize mode.<br>
Refer to the <a href="http://ch-ch.org/customfields" target="_blank">Documentation</a> if you have any doubts.
</span>
</div>
<div class="cmb-th">
	<label for="<?php echo $options_slug . 'email_fields' ?>">E-mail Fields:</label>
</div>
<div class="cmb-td">
	<?php
	$email_fields = isset( $email_options[ 'fields' ] ) ? $email_options[ 'fields' ] : false;

	$this->get_fields_repeater( 'email', 'chch_contact_free_email_fields', $email_fields ); ?>
</div></div>
