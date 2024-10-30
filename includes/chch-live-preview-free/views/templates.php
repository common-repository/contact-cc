<?php

$templates = $this->get_templates();

$default_tpl = reset( $templates );
$active_template = get_post_meta( get_the_ID(), '_chch_lpf_template', true ) ? get_post_meta( get_the_ID(), '_chch_lpf_template', true ) : $default_tpl['id'];
 
?> 
<div class="themes-php chch-lpf-tab chch-lpf-tab-active chch-lpf-tab-templates" id="chch-lp-templates-form">
	<div class="wrap"> 
		<h2>Templates:<span class="theme-count title-count"><?php

echo count( $templates );

?></span></h2>  
		<?php

if ( count( $templates ) ):

?>
			<div class="theme-browser rendered">
				<div class="themes">
					<?php

  if ( isset( $templates[$active_template] ) ):

?>
						<?php

    $template = $templates[$active_template];

?>
						<div class="theme active">
							<div class="theme-screenshot">
                <?php

    $this->get_template_thumbnail( $template['id'], true );

?>
								
							</div>
							<h3 class="theme-name"><span>Active:</span>	<?php

    echo $template['title'];

?></h3>
							<div class="theme-actions">
								<?php

    printf( '<a href="#" class="chch-lpf-template-acivate button button-primary" data-template="%s">Activate</a>', $template['id'] );
    printf( '<a href="#" class="chch-lpf-template-ajax-edit button button-primary" data-template="%s" data-postid="%s" data-lpf-id="%s" data-nounce="%s">Customize</a>', $template['id'], get_the_ID(), $this->lp->id, wp_create_nonce( 'chch-lpf-nounce-' . $template['id'] ) );

?>
							</div> 
						</div> 
					<?php

  endif;

?>
					
					<?php

  foreach ( $templates as $template ):
    if ( $template['id'] !== $active_template ):

?>
						<div class="theme" tabindex="0">
							<div class="theme-screenshot">
								<?php

      $this->get_template_thumbnail( $template['id'], true );

?>
							</div>
							<h3 class="theme-name"><?php

      echo $template['title'];

?></h3>
							<div class="theme-actions"> 
<?php

      printf( '<a href="#" class="chch-lpf-template-acivate button button-primary" data-template="%s">Activate</a>', $template['id'] );
      printf( '<a href="#" class="chch-lpf-template-ajax-edit button button-primary" data-template="%s" data-postid="%s" data-lpf-id="%s" data-nounce="%s">Customize</a>', $template['id'], get_the_ID(), $this->lp->id, wp_create_nonce( 'chch-lpf-nounce-' . $template['id'] ) );

?>
							</div> 
						  <?php

      //$this->get_lp_form( $template['id'] );


?>
						</div>
						
						 
						
					<?php

    endif;
  endforeach;

?> 
				</div>
			</div>
		<?php

endif;

?>
	</div>
  <div id="chch-lpf-ajax-form-container" style="display: none;"></div>
</div> 
<input type="hidden" name="_chch_lpf_template" id="_chch_lpf_template" value="<?php

echo $active_template;

?>"/> 
<div style="display: none;">
<?php 
	wp_editor('','chch-lpf-helper-editor'); 
?>
</div>
<?php

wp_nonce_field( 'chch_lpf_save_nonce_' . get_the_ID(), 'chch_lpf_save_nonce' );

?>
 
 