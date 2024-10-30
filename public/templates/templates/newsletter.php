<?php
use ChopChop\ContactFree\Contact_Free_Main as MainPlugin;

$main_plugin = MainPlugin::get_instance();
$form_grid = $this->get_template_option( 'input', 'grid' );


printf( "<div class=\"chch-contact-free chch-contact chch-theme__%s chch-template__newsletter chch-position__default %s \">\n", $template_type, $form_grid );
?>

<article class="chch-contact-free__article">
	<div class="chch-contact-free__wrapper">
		<div class="chch-contact-free__content--header chch-contact-free__content">
			<?php echo apply_filters('the_content', $this->get_template_option( 'content', 'header' )); ?>
		</div>
		<!-- /.chch-contact-free__header -->
		<div class="chch-contact-free__content--subheader chch-contact-free__content">
			<?php echo apply_filters('the_content', $this->get_template_option( 'content', 'subheader' )); ?>
		</div>
		<!-- /.chch-contact-free__subheader -->
		<div class="chch-contact-free__content--content chch-contact-free__content">
			<?php echo apply_filters('the_content', $this->get_template_option( 'content', 'content' )); ?>
		</div>
		<!-- /.chch-contact-free__content -->
		<?php $main_plugin->get_newsletter_form( $this->lp_post_id, $this ); ?>
	</div>
</article>
</div>

