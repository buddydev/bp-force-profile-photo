<?php
/**
 * Plugin Name: BP Force Profile Photo
 * Version: 1.0
 * Plugin URI: http://BuddyDev.com/plugins/bp-force-profile-photo/
 * Author: Brajesh Singh
 * Author URI: http://BuddyDev.com
 * Description: Force a User to upload their profile photo(avatar) before they start using the site features.
 * License: GPL2 or above
 */
class BD_Force_User_Avatar_Helper {
	
    
    private  static $instance;
	
    private function __construct() {
		//record on new avatar upload
		add_action( 'xprofile_avatar_uploaded', array( $this, 'log_uploaded' ) );
		//on avatar delete, remove the log
		add_action( 'bp_core_delete_existing_avatar', array( $this, 'log_deleted' ) );

		add_action( 'bp_template_redirect', array( $this, 'check_or_redirect' ), 1 );
            
	}
		
   /**
    * Singleton Instance
    * 
    * @return BD_Force_User_Avatar_Helper
    */
    public static function get_instance() { 
        
        if( ! isset ( self::$instance ) )
            self::$instance = new self();
        
        return self::$instance;
    }
	
	/**
	 * Checks if a user has uploaded avatar and redirects to upload page if not
	 * 
	 * @return type
	 */
	public function check_or_redirect() {
		
		if( ! is_user_logged_in() || is_super_admin() )
			return;
		
		//if we are here, the user is logged in
		if( $this->has_uploaded_avatar( get_current_user_id() ) ) 
			return ;
		
		if( bp_is_my_profile() && bp_is_user_change_avatar() )
			return;
		
		//if we are here, user has not uploaded an avatar, let us redirect them to upload avatar page
		bp_core_redirect( bp_loggedin_user_domain() . buddypress()->profile->slug .'/change-avatar/');
		
		
	}
    
	/**
	 * On New Avatar Upload, add the usermeta to reflect that user has uploaded an avatar
	 * 
	 */
    public function log_uploaded() {
		
        bp_update_user_meta( get_current_user_id(), 'has_avatar', 1 );
    }
    
    /**
	 * On Delete Avatar, delete the user meta to reflecte the change
	 * 
	 * @param type $args
	 * @return type
	 */
    public function log_deleted( $args ) {
        
		if( $args['object'] != 'user' )
			return;
        //we are sure it was user avatar delete

        //remove the log from user meta
        bp_delete_user_meta( get_current_user_id(), 'has_avatar');
    }

	/**
	 * has this user uploaded an avatar?
	 * 
	 * @param type $user_id
	 * @return boolean
	 */
    public function has_uploaded_avatar( $user_id ) {
		
		$has_avatar =  bp_get_user_meta( $user_id, 'has_avatar', true );
		if( ! $has_avatar )
			$has_avatar = bp_get_user_has_avatar( $user_id );//fallback
		
		return $has_avatar;
	}
	
}

BD_Force_User_Avatar_Helper::get_instance();


