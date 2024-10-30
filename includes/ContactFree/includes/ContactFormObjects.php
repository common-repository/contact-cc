<?php

namespace ChopChop\ContactFormFree;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ContactFormObjects {

	private static $instances = array();

	/**
	 * Add new FORM Object to instances array
	 *
	 * @param ContactForm $instance
	 *
	 */
	public static function add( ContactForm $instance ) {
		self::$instances[ $instance->id ] = $instance;
	}

	/**
	 * Get all registered instances.
	 *
	 * @return array
	 */
	public static function get_all() {
		return self::$instances;
	}

	/**
	 * Gets single object by id
	 *
	 * @param $object_id
	 *
	 * @return mixed - lp object if exists else false
	 */
	public static function get_form_object( $object_id ) {
		if ( isset( self::$instances[ $object_id ] ) ) {
			return self::$instances[ $object_id ];
		} else {
			return 'none';
		}
	}

	/**
	 * Updates already registered instance
	 *
	 * @param ContactForm $form_object
	 *
	 * @return mixed - object if exists else false
	 *
	 */
	public static function update_form_object(ContactForm $form_object ) {

		if ( isset( self::$instances[ $form_object->id] ) ) {
			self::$instances[ $form_object->id ] = $form_object;
		} else {
			return false;
		}
	}
}