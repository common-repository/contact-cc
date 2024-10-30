<?php
/**
 * DESC
 *
 * @package   ChopChop\LivePreviewFree
 * @author    Chop-Chop.org <shop@chop-chop.org>
 * @license   GPL-2.0+
 * @link      https://shop.chop-chop.org
 * @copyright 2016
 *
 * @wordpress-plugin
 */

namespace ChopChop\LivePreviewFree;

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF' ) ) {

	/**
	 * Class CHCH_LPF
	 *
	 * @package ChopChop\LivePreviewFree
	 */
	class CHCH_LPF {

		/**
		 * Live Preview settings array
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $live_preview = array();

		/**
		 * Module slug
		 *
		 * @var string
		 */
		public $slug = 'lp_';

		/**
		 * Live Preview Default settings
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $lp_defaults = array(
			'id'                => '',
			'title'             => '',
			'tpl_dir'           => CHCH_LIVE_PREVIEW_FREE_DIR,
			'tpl_url'           => CHCH_LIVE_PREVIEW_FREE_URL,
			'target_post_types' => 'fake_post',
		);

		/**
		 * Live Preview Section Default settings
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $section_defaults = array(
			'name' => 'name',
			'id'   => 'id',
		);

		/**
		 * Holds hole fields structure
		 *
		 * @var   array
		 * @since 1.0.0
		 */
		private $fields = array();

		/**
		 * LP id
		 *
		 * @var string
		 */
		public $lp_id;

		/**
		 * CHCH_LP_Free constructor.
		 *
		 * @param array $settings - lp settings.
		 */
		public function __construct( array $settings ) {

			if ( empty( $settings['id'] ) ) {
				wp_die( esc_html__( 'Live Preview Instance must have id!', $this->slug ) );
			}

			$this->live_preview = wp_parse_args( $settings, $this->lp_defaults );
			$this->lp_id        = $settings['id'];

			CHCH_LPF_Objects::add( $this );

			do_action( "chch_lpf_init_{$this->lp_id}", $this );
		}

		/**
		 * Add new section to LP fields.
		 *
		 * @param array $section - section settings, includes id, title etc.
		 */
		public function add_field_section( array $section ) {
			if ( ! is_array( $section ) || ! isset( $section['id'] ) ) {
				return;
			}

			$new_section = wp_parse_args( $section, $this->section_defaults );

			if ( ! isset( $this->fields['fields_sections'][ $new_section['id'] ] ) ) {
				$this->fields['fields_sections'][ $new_section['id'] ] = $new_section;
			}
		}

		/**
		 * Add new field group to LP fields.
		 *
		 * @param string $section_id - group will be added to section with this id.
		 * @param array  $group      - group settings.
		 */
		public function add_fields_group( $section_id = '', array $group ) {
			if ( empty( $section_id ) || ! is_array( $group ) || ! isset( $group['id'] ) ) {
				return;
			}

			if ( ! isset( $this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group['id'] ] ) ) {
				$this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group['id'] ] = $group;
			}
		}

		/**
		 * Add single field to LP fields.
		 *
		 * @param string $section_id - field will saved in section with this id.
		 * @param string $group_id   - field will saved in group with this id.
		 * @param array  $field      - field settings.
		 */
		public function add_field( $section_id = '', $group_id = '', $field = array() ) {
			if ( empty( $section_id ) || empty( $group_id ) || ! is_array( $field ) ) {
				return;
			}

			if ( isset( $this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group_id ] ) ) {
				$this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group_id ]['fields'][] = $field;
			}
		}

		/**
		 * Add fields group to LP fields.
		 *
		 * @param string $section_id - fields will be added to section with this id.
		 * @param string $group_id   - fields will be added to group with this id.
		 * @param array  $fields     - fields group settings.
		 */
		public function add_fields( $section_id = '', $group_id = '', $fields ) {
			if ( empty( $section_id ) || empty( $group_id ) || ! is_array( $fields ) ) {
				return;
			}

			if ( isset( $this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group_id ] ) ) {
				$this->fields['fields_sections'][ $section_id ]['fields_groups'][ $group_id ]['fields'] = $fields;
			}
		}

		/**
		 * Return hole LP fields structure, sections->groups->fields
		 *
		 * @return array
		 */
		public function get_all_field_sections() {
			return $this->fields['fields_sections'];
		}

		/**
		 * Return section with given id.
		 *
		 * @param string $section_id - return section with this id.
		 *
		 * @return array
		 */
		public function get_field_section( $section_id = 'group_id' ) {

			$section = array();
			if ( isset( $this->fields['fields_sections'][ $section_id ] ) ) {
				$section = $this->fields['fields_sections'][ $section_id ];
			}

			return $section;
		}

		/**
		 * Return groups register in section with given id.
		 *
		 * @param string $section_id - return field groups from section with this id.
		 *
		 * @return array
		 */
		public function get_section_fields_group( $section_id ) {

			$fields = array();
			if ( isset( $this->fields['fields_sections'][ $section_id ] ) && isset( $this->fields['fields_sections'][ $section_id ]['fields_groups'] ) ) {
				$fields = $this->fields['fields_sections'][ $section_id ]['fields_groups'];
			}

			return $fields;
		}

		/**
		 * Return all field groups.
		 *
		 * @return array
		 */
		public function get_all_fields_group() {

			$all_sections     = $this->get_all_field_sections();
			$all_fields_group = array();
			foreach ( $all_sections as $section ) {

				foreach ( $section['fields_groups'] as $id => $group ) {
					if ( isset( $group['disabled'] ) ) {
						continue;
					}

					$all_fields_group[ $id ] = $group['fields'];
				}
			}

			return $all_fields_group;
		}

		/**
		 * Return section fields.
		 *
		 * @param string $section_id - return fields from section with this id.
		 *
		 * @return array
		 */
		public function get_section_fields( $section_id ) {

			$fields = array();
			if ( isset( $this->fields['fields_sections'][ $section_id ] ) && isset( $this->fields['fields_sections'][ $section_id ]['fields'] ) ) {
				$fields = $this->fields['fields_sections'][ $section_id ]['fields_groups']['fields'];
			}

			return $fields;
		}

		/**
		 * Magic getter for this object.
		 *
		 * @param string $param - name of lp config param.
		 *
		 * @return mixed
		 */
		public function __get( $param ) {
			if ( array_key_exists( $param, $this->live_preview ) ) {
				return $this->live_preview[ $param ];
			}

			return '';
		}
	}
}
