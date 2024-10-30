<?php
use ChopChop\ContactFree;
$templates = $this->get_templates();


$main_plugin_admin = ContactFree\Contact_Free_Admin::get_instance();
$filtered_templates = $main_plugin_admin->sort_templates_by_set( $templates );

?>
<?php
$active_template = get_post_meta( get_the_ID(), '_chch_lp_template', true );
$active_template = $active_template ? $active_template : 'newsletter-white';

if ( isset( $templates[$active_template][ 'set' ] ) ) {
	$default_set = $templates[$active_template][ 'set' ];
} else {
	$default_set = 'newsletter';
}


$active_set = get_post_meta( get_the_ID(), '_chch_lp_template_set', true );
$active_set = $active_set ? $active_set : $default_set;
?>
<div class="themes-php chch-lpf-tab chch-lpf-tab-active chch-lpf-tab-templates" id="chch-lp-templates-form">
	<div class="wrap">
		<h2>Templates:<span class="theme-count title-count"><?php

				echo count( $templates );

				?></span></h2>
		<?php

		if ( count( $filtered_templates ) ):

			?>
			<div class="theme-browser rendered">
				<div class="themes">
					<?php

					if ( isset( $filtered_templates[ $active_set ][ 'templates' ] ) ):
						?>
						<?php

						$template_set = $filtered_templates[ $active_set ];
						$template = $filtered_templates[ $active_set ][ 'templates' ][ $active_template ];
						?>
						<div class="theme active">
							<div class="theme-screenshot">
								<?php

								$this->get_template_thumbnail( $template[ 'id' ], true );

								?>

							</div>

							<div class="theme-actions">
								<?php

								printf( '<a href="#" class="chch-lp-template-acivate button button-primary" data-set="%s" data-template="%s">Activate</a>', $template_set[ 'id' ], $template[ 'id' ] );
								printf( '<a href="#" class="chch-lp-template-ajax-edit button button-primary" data-set="%s" data-template="%s" data-postid="%s" data-lp-id="%s" data-nounce="%s">Customize</a>', $template_set[ 'id' ], $template[ 'id' ], get_the_ID(), $this->lp->get_param( 'id' ), wp_create_nonce( 'chch-lp-nonce-' . $template[ 'id' ] ) );

								?>
							</div>
						</div>
						<div class="theme active">
							<div class="inner-theme">
								<h1 class="theme-name"><span>Active:</span> <?php

									echo $template_set[ 'name' ];

									?></h1>
									<?php
										do_action('chch_contact_free_set_desc');
										do_action('chch_contact_free_set_desc_'.$template_set[ 'id' ]);
									?>

								<div class="theme-templates"><span>Select Color Preset:</span>
									<?php
									if ( isset( $template_set[ 'templates' ] ) && is_array( $template_set[ 'templates' ] ) ) {
										foreach ( $template_set[ 'templates' ] as $template ) {
											printf( "<a href=\"#\" class=\"template-thumb thumb-%s\" data-set=\"%s\" data-thumbnail=\"%s\" data-thumbnail-id=\"%s\">%s</a> ", strtolower($template[ 'title' ]), $template_set[ 'id' ], $this->get_template_thumbnail_url( $template[ 'id' ], true ), $template[ 'id' ], $template[ 'title' ] );
										}
									}
									?>

								</div>
							</div>
						</div>
						<?php

					endif;

					?>
				</div>

				<?php
				foreach ( $filtered_templates as $set ):
					if ( $set[ 'id' ] !== $active_set ):

						?>
						<div class="themes">
							<div class="theme">
								<div class="theme-screenshot">
									<?php
									$first_tpl = reset( $set[ 'templates' ] );

									$this->get_template_thumbnail( $first_tpl[ 'id' ], true );
									?>

								</div>

								<div class="theme-actions">
									<?php
									printf( '<a href="#" class="chch-lp-template-acivate button button-primary" data-set="%s" data-template="%s">Activate</a>', $set[ 'id' ], $first_tpl[ 'id' ] );
									printf( '<a href="#" class="chch-lp-template-ajax-edit button button-primary" data-set="%s" data-template="%s" data-postid="%s" data-lp-id="%s" data-nounce="%s">Customize</a>', $set[ 'id' ], $first_tpl[ 'id' ], get_the_ID(), $this->lp->get_param( 'id' ), wp_create_nonce( 'chch-lp-nonce-' . $first_tpl[ 'id' ] ) );
									?>
								</div>
							</div>
							<div class="theme">
								<div class="inner-theme">
									<h1 class="theme-name"><?php

										echo $set[ 'name' ];

										?></h1>

									<?php
									do_action('chch_contact_free_set_desc');
									do_action('chch_contact_free_set_desc_'.$set[ 'id' ]);
									?>

									<div class="theme-templates"><span>Select Color Preset:</span>
										<?php
										if ( isset( $set[ 'templates' ] ) && is_array( $set[ 'templates' ] ) ) {
											foreach ( $set[ 'templates' ] as $template ) {
												printf( "<a href=\"#\" class=\"template-thumb thumb-%s\" data-set=\"%s\" data-thumbnail=\"%s\" data-thumbnail-id=\"%s\">%s</a> ", strtolower($template[ 'title' ]), $set[ 'id' ], $this->get_template_thumbnail_url( $template[ 'id' ], true ), $template[ 'id' ], $template[ 'title' ] );
											}
										}
										?>

									</div>
								</div>
							</div>

						</div>

						<?php

					endif;
				endforeach;

				?>
			</div>
			<?php

		endif;

		?>
	</div>
	<div id="chch-lp-ajax-form-container" style="display: none;"></div>
</div>
<input type="hidden" name="_chch_lp_template" id="_chch_lp_template" value="<?php

echo $active_template;

?>"/>
<input type="hidden" name="_chch_lp_template_set" id="_chch_lp_template_set" value="<?php

echo $active_set;

?>"/>
<div style="display: none;">
	<?php
	wp_editor( '', 'chch-lp-helper-editor' );
	?>
</div>
<?php

wp_nonce_field( 'chch_lp_save_nonce_' . get_the_ID(), 'chch_lp_save_nonce' );
wp_nonce_field( 'chch_contact_free_save_nonce', 'chch_contact_free_save_nonce' );
?>


