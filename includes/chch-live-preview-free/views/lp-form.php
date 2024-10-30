<?php 
	echo '<div class="chch-lpf-customize-form" id="chch-lpf-customize-form-'.$this->template.'"  >';
		 
		echo '<div class="chch-lpf-customize-controls">';
		
		//preview options header
		echo '
			<div class="chch-lpf-customize-header-actions">
				<input name="publish" id="publish-customize" class="button button-primary button-large" value="Save &amp; Close" accesskey="p" type="submit" />  
				<a class="chch-lpf-customize-close" href="#" data-template="'. $this->template.'">
					<span class="screen-reader-text">Close</span>
				</a> 
		</div>';
		
		//preview options overlay - start
		echo '<div class="chch-lpf-options-overlay">';
		
		//preview customize info
		echo '<div class="chch-lpf-customize-info">
				<span class="preview-notice">
					You are customizing <strong class="template-title">'.$this->template.' Template</strong>
				</span>
			</div><!--#customize-info-->';
	
		//preview options accordion wrapper - start
		echo '<div class="customize-theme-controls"  class="accordion-section">';
		
		// build options sections
		
		$this->build_options_section();
		
		echo '
				</div><!--.accordion-section-->
			</div><!--.chch-lpf-options-overlay-->
		</div><!--#chch-lpf-customize-controls-->';
	
		echo '<div id="chch-lpf-customize-preview-'.$this->template.'" class="chch-lpf-customize-preview" style="position:relative;">'; 
    $this->template_object->get_template();
		echo '</div>';
		echo '</div>';
?>