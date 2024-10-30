<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Form' ) ) {
	class CHCH_LPF_Form {

		private $lp = null;

		private $template = '';

		/**
		 * LP_FORM::__construct()
		 *
		 * @param mixed $fields
		 * @param mixed $template
		 *
		 * @return
		 */
		public function __construct( $lp, $template, $id = '0' ) {
			$this->lp              = $lp;
			$this->template        = $template;
			$this->id              = $id;
			$this->template_object = new CHCH_LPF_Templates( $this->lp, $this->template, $this->id );
		}

		/**
		 * Returns hole lp view: form, templates list etc.
		 *
		 * @return void
		 */
		function get_form() {
			$lp_view_dir = CHCH_LIVE_PREVIEW_FREE_DIR . 'views/lp-form.php';
			$lp_view_dir = apply_filters( 'chch_lpf_tpl_view', $lp_view_dir );

			$lp_view = '';
			if ( file_exists( $lp_view_dir ) ) {
				$lp_view = ( include( $lp_view_dir ) );
			}

			return $lp_view;
		}

		/**
		 * LP_FORM::build_options_section()
		 *
		 * @return
		 */
		private function build_options_section() {

			printf( "<input type=\"hidden\" value=\"1\" name=\"%s-form-loaded\" />", $this->template );
			foreach ( $this->lp->get_all_field_sections() as $section ) {
				echo $this->build_form_tabs( $section );
			}
		}

		/**
		 * LP_FORM::build_form_tabs()
		 *
		 * @param mixed $section
		 *
		 * @return
		 */
		private function build_form_tabs( $section ) {

			if ( ! isset( $section[ 'fields_groups' ] ) || ! is_array( $section[ 'fields_groups' ] ) ) {
				return;
			}

			$lp_form_tab = '';

			$section_name = ! empty( $section[ 'name' ] ) ? $section[ 'name' ] : 'Section';
			$lp_form_tab .= sprintf( "<h3 class=\"accordion-section-title\">%s<span class=\"screen-reader-text\">Press return or enter to expand</span></h3>\n", $section_name );

			$lp_form_tab .= "\t<div class=\"accordion-section-content\">\n";

			foreach ( $section[ 'fields_groups' ] as $options ):
				$lp_form_tab .= $this->build_fields_groups( $options );
			endforeach;

			$lp_form_tab .= "\t</div>\n";

			return $lp_form_tab;
		}

		/**
		 * LP_FORM::build_fields_sections()
		 *
		 * @param mixed $options
		 *
		 * @return
		 */
		private function build_fields_groups( $options ) {

			if ( ! is_array( $options ) || ! is_array( $options[ 'fields' ] ) ) {
				return;
			}

			$disabled_section = ( isset( $options[ 'disabled' ] ) && $options[ 'disabled' ] === true ) ? true : false;

			$section = '<div class="chch-lpf-fields-wrapper">';

			if ( $disabled_section ) {
				$pro_message = $this->lp->disabled_section_content;
				$section .= '<div class="chch-lpf-overlay">' . $pro_message . '</div>';
			}

			$section .= '<h4>' . $options[ 'title' ] . '</h4>';
			foreach ( $options[ 'fields' ] as $field ):

				$lp_field = new CHCH_LPF_Fields( $field, $options[ 'id' ], $this->template, $this->lp, $this->id, $disabled_section );
				$section .= $lp_field->get_field();

			endforeach;

			$section .= ' </div>';

			return $section;
		}
	}
}