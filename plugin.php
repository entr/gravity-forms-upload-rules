<?php
/*
Plugin Name: Gravity Forms Upload Rules
Plugin URI: http://zzlatev.com/gravity-forms-upload-limits/
Description: Adds extra upload rules to file/image Gravity Froms upload fields. You will be able to limit upload filesize and dimensions of uploaded images.
Version: 1.0
Author: Zlatko Zlatev
Author URI: http://zzlatev.com/
Textdomain: gforms_uprules
*/

add_action( 'plugins_loaded', 'gravityforms_uploadrules_load' );

function gravityforms_uploadrules_load() {
	if ( class_exists( 'GFForms' ) || class_exists( 'RGForms' ) ) {
		
		require_once( plugin_dir_path( __FILE__ ) . 'gforms-uploadrules.php' );
	}
}