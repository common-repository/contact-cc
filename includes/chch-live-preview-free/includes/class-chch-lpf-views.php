<?php
namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Views' ) ) {
	if ( ! class_exists( 'PluginMetaData' ) ) {
		require_once( CHCH_LIVE_PREVIEW_FREE_DIR . 'third-party/PluginMetaData.php' );
	}

	/**
	 * LPF_Views builds all views in admin panel.
	 *
	 * @package   LivePreviewFree
	 * @author
	 * @copyright 2015
	 * @version   1.0.0
	 * @access    public
	 */
	class CHCH_LPF_Views {

		/**
		 *
		 * @param   string
		 *
		 * @since 1.0.0
		 */
		private $lp = '';

		/**
		 * CHCH_LPF_Views constructor.
		 *
		 * @param        $lp
		 * @param string $id
		 */
		public function __construct( $lp, $id = '0' ) {
			$this->lp = $lp;
			$this->id = $id;
		}

		/**
		 * Returns hole lp view: form, templates list etc.
		 *
		 * @return void
		 */
		public function get_lp_view( $echo = 'true' ) {
			$lp_view_dir = CHCH_LIVE_PREVIEW_FREE_DIR . 'views/templates.php';
			$lp_view_dir = apply_filters( 'chch_lp_tpl_view', $lp_view_dir );

			$lp_view = '';
			if ( file_exists( $lp_view_dir ) ) {
				$lp_view = ( include( $lp_view_dir ) );
			}

			return $lp_view;
		}

		/**
		 * Returns hole lp view: form, templates list etc.
		 *
		 * @return void
		 */
		public function get_lp_form( $template_id ) {
			$lp_form = new CHCH_LPF_Form( $this->lp, $template_id, $this->id );
			$lp_form->get_form();
		}

		/**
		 * Return list of templates
		 *
		 * @since     1.0.0
		 *
		 * @return    array - template list
		 */
		public function get_templates() {

			$pmd = new \PluginMetaData;
			$pmd->scan( $this->lp->tpl_dir );

			return $pmd->plugin;
		}

		/**
		 * Returns or prints template thumbnail
		 *
		 * @param string $template_id - thumbnail will be included from template with this id
		 * @param string $echo        - Default - false. If set to true thumbnail will be printed immediately, otherwise it will be return as a string
		 *
		 * @return string $thumbnail - only if $echo is set to false
		 */
		public function get_template_thumbnail( $template_id, $echo = false ) {
			CHCH_LPF_Templates::get_thumbnail( $template_id, $this->lp->tpl_dir, $this->lp->tpl_url, $echo );
		}

		/**
		 * Return tabs html
		 *
		 * @use get_default_tabs();
		 *
		 * @return string
		 */
		public function get_tabs() {

			$tabs = $this->get_default_tabs();

			return $tabs;
		}

		/**
		 * Get default tabs and tabs register by user within chch-lp-tabs filter.
		 *
		 * @use build_tabs()
		 *
		 * @return string
		 */
		private function get_default_tabs() {
			$default_tabs = array(
				"templates" => array(
					'id'       => 'templates',
					'name'     => "Templates",
					'priority' => 10,
					'icon'     => "format-gallery",
				),
				"settings"  => array(
					'id'       => "settings",
					'name'     => "Settings",
					'priority' => 20,
					'icon'     => "admin-generic",
				),
			);

			$default_tabs = apply_filters( 'chch-lpf-tabs', $default_tabs );
			$default_tabs = apply_filters( 'chch-lpf-tabs-' . $this->lp->id, $default_tabs );

			$lp_tabs = $this->build_tabs( $default_tabs );

			return $lp_tabs;
		}

		/**
		 * Build html from tabs array.
		 *
		 * @param array $tabs
		 *
		 * @return string
		 */
		private function build_tabs( array $tabs ) {
			usort( $tabs, array( $this, "sort_tabs" ) );

			$tabs_html = '';
			$first_tab = true;
			foreach ( $tabs as $tab ) {
				$active_tab = "";
				if ( $first_tab ) {
					$active_tab = "nav-tab-active";
					$first_tab  = false;
				}

				$tabs_html .= sprintf( "<a class=\"nav-tab %s\" href=\"#\" data-target=\"chch-lpf-tab-%s\"><span class=\"dashicons dashicons-%s\"></span>%s</a>", $active_tab, $tab['id'], $tab['icon'], $tab['name'] );
			}

			return $tabs_html;
		}

		/**
		 * Sort tabs by priority - ASC
		 *
		 * @param $a
		 * @param $b
		 *
		 * @return int
		 */
		private function sort_tabs( $a, $b ) {
			if ( ! isset( $a['priority'] ) && ! isset( $b['priority'] ) ) {
				return 0;
			} else {
				return ( $a['priority'] < $b['priority'] ) ? - 1 : 1;
			}
		}

	}
}
