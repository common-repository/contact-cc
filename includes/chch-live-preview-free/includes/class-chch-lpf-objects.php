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

if ( ! class_exists( 'ChopChop\\LivePreviewFree\\CHCH_LPF_Objects' ) ) {

	/**
	 * LP_Objects
	 *
	 * @package   CHC LIVE PREVIEW
	 * @author
	 * @copyright 2015
	 * @version   $Id$
	 * @access    public
	 */
	class CHCH_LPF_Objects {

		/**
		 * Array of CHCH_LPF instances.
		 *
		 * @var array
		 */
		protected static $LP_instances = array();

		/**
		 * Add new/update CHCH_LPF instance to instances array.
		 *
		 * @param CHCH_LPF $lp_instance - CHCH_LPF instance.
		 */
		public static function add( CHCH_LPF $lp_instance ) {
			self::$LP_instances[ $lp_instance->lp_id ] = $lp_instance;
		}

		/**
		 * LPF_Objects::get_all()
		 *
		 * @return array
		 */
		public static function get_all() {
			return self::$LP_instances;
		}

		/**
		 * Return register object with given id.
		 *
		 * @param string $object_id - id of instance.
		 *
		 * @return mixed
		 */
		public static function get_lp_object( $object_id ) {
			if ( isset( self::$LP_instances[ $object_id ] ) ) {
				return self::$LP_instances[ $object_id ];
			} else {
				return false;
			}
		}

		/**
		 * LP_Objects::get_all_post_types()
		 *
		 * @return array
		 */
		public static function get_all_post_types() {
			$lp_post_types = array();

			foreach ( self::$LP_instances as $lp ) {

				$lp_post_types[ $lp->lp_id ] = $lp->target_post_types;
			}

			return $lp_post_types;
		}
	}
}
