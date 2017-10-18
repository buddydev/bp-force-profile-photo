<?php

/**
 * Plugin Name: BP Force Profile Photo
 * Version: 1.0.7
 * Plugin URI: https://BuddyDev.com/plugins/bp-force-profile-photo/
 * Author: BuddyDev Team
 * Author URI: https://BuddyDev.com
 * Description: Force a User to upload their profile photo(avatar) before they start using the site features.
 * License: GPL2 or above
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 );
}

/**
 * Helper class
 */
class BD_Force_User_Avatar_Helper {

	/**
	 * Singleton instance.
	 *
	 * @var BD_Force_User_Avatar_Helper
	 */
	private static $instance = null;

	/**
	 * Constructor
	 */
	private function __construct() {
		// record on new avatar upload.
		add_action( 'xprofile_avatar_uploaded', array( $this, 'log_uploaded' ) );
		// on avatar delete, remove the log.
		add_action( 'bp_core_delete_existing_avatar', array( $this, 'log_deleted' ) );

		add_action( 'bp_template_redirect', array( $this, 'check_or_redirect' ), 1 );

		// load languages file.
		add_action( 'bp_init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Singleton Instance
	 *
	 * @return BD_Force_User_Avatar_Helper
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Load translation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'bp-force-profile-photo', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Checks if a user has uploaded avatar and redirects to upload page if not
	 */
	public function check_or_redirect() {

		if ( ! is_user_logged_in() || is_super_admin() ) {
			return;
		}

		$user_id = get_current_user_id();

		// should we skip check for the current user?
		if ( apply_filters( 'bp_force_profile_photo_skip', false ) || $this->skip_check( $user_id ) ) {
			return;
		}
		// if we are here, the user is logged in.
		if ( $this->has_uploaded_avatar( $user_id ) ) {
			return;
		}

		if ( bp_is_my_profile() && bp_is_user_change_avatar() ) {
			return;
		}

		bp_core_add_message( __( 'Please upload your profile photo to start using this site.', 'bp-force-profile-photo' ), 'error' );
		// if we are here, user has not uploaded an avatar, let us redirect them to upload avatar page.
		bp_core_redirect( bp_loggedin_user_domain() . buddypress()->profile->slug . '/change-avatar/' );


	}

	/**
	 * Checks if we should skip check for the given user.
	 *
	 * @param int $user_id user id.
	 *
	 * @return bool
	 */
	public function skip_check( $user_id ) {
		$meta_keys = array(
			'_fbid', // for kleo
			'deuid', // AccessPress Social Login Lite
			'fb_account_id', // for BuddyPress Facebook Connect Plus
			'oa_social_login_user_picture', // social login plugin
			'oa_social_login_user_thumbnail',// social login plugin
			'wsl_current_user_image', // WordPress social login plugin, may not work in some case
			'facebook_avatar_full',// wp-fb-autoconnect
			'facebook_uid',// for wp-fb-autoconnect
			'wsl_user_image',// for WordPress social login.
		);
		// use the below filter to remove/add any extra key.
		$meta_keys = apply_filters( 'bp_force_profile_photo_social_meta', $meta_keys );

		if ( empty( $meta_keys ) ) {
			return false;// we do not need to skip the test.
		}

		$meta_keys = array_map( 'esc_sql', $meta_keys );

		$list = '\'' . join( '\', \'', $meta_keys ) . '\'';

		$meta_list = '(' . $list . ')';

		global $wpdb;

		$has_meta = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key IN {$meta_list} and user_id = %d ", $user_id ) );

		if ( ! empty( $has_meta ) ) {
			return true;
		}

		return false;

	}

	/**
	 * On New Avatar Upload, add the user meta to reflect that user has uploaded an avatar
	 *
	 * @param int $user_id user whose avatar changed.
	 */
	public function log_uploaded( $user_id ) {
		bp_update_user_meta( $user_id, 'has_avatar', 1 );
	}

	/**
	 * On Delete Avatar, delete the user meta to reflect the change
	 *
	 * @param array $args see args array.
	 *
	 * @return type
	 */
	public function log_deleted( $args ) {

		if ( $args['object'] != 'user' ) {
			return;
		}

		$user_id = empty( $args['item_id'] ) ? 0 : absint( $args['item_id'] );

		if ( ! $user_id ) {
			if ( bp_is_user() && ( bp_is_my_profile() || is_super_admin() ) ) {
				$user_id = bp_displayed_user_id();
			} else {
				$user_id = bp_loggedin_user_id();
			}
		}

		// we are sure it was user avatar delete
		// remove the log from user meta.
		bp_delete_user_meta( $user_id, 'has_avatar' );
	}

	/**
	 * Has this user uploaded an avatar?
	 *
	 * @param int $user_id user id.
	 *
	 * @return boolean
	 */
	public function has_uploaded_avatar( $user_id ) {

		$has_avatar = bp_get_user_meta( $user_id, 'has_avatar', true );

		if ( ! $has_avatar ) {
			$has_avatar = bp_get_user_has_avatar( $user_id );// fallback.
		}

		return $has_avatar;
	}

}

BD_Force_User_Avatar_Helper::get_instance();
