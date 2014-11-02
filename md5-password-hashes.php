<?php

/*
Plugin Name: MD5 Password Hashes
Plugin URI: http://wordpress.org/extend/plugins/md5-password-hashes/
Description: Changes the password hashing in WordPress to use MD5
Author: Ryan Boren
Author URI: 
Version: 1.0.1

Version History:
1.0             : Initial Release
*/

if ( ! function_exists('wp_check_password') ):
function wp_check_password($password, $hash, $user_id = '') {
	// If the hash was updated to the new hash before this plugin
	// was installed, rehash as md5.
	if ( strlen($hash) > 32 ) {
		global $wp_hasher;
		if ( empty($wp_hasher) ) {
			require_once( ABSPATH . 'wp-includes/class-phpass.php');
			$wp_hasher = new PasswordHash(8, TRUE);
		}
		$check = $wp_hasher->CheckPassword($password, $hash);
		if ( $check && $user_id ) {
			// Rehash using new hash.
			wp_set_password($password, $user_id);
			$user = get_userdata($user_id);
			$hash = $user->user_pass;
		}

		return apply_filters('check_password', $check, $password, $hash, $user_id);
	}

	$check = ( $hash == md5($password) );

	return apply_filters('check_password', $check, $password, $hash, $user_id);
}
endif;

if ( !function_exists('wp_hash_password') ):
function wp_hash_password($password) {
	return md5($password);
}
endif;

?>
