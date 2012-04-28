<?php

/**
 * BP-MEDIA TEST DATA LOADER FUNCTIONS
 * These functions add and clear users and media items from the BP-Media install and the host BuddyPress
 * and WordPress installations. They are used to reset the installation to a known state for UI testing.
 *
 * @version 0.1.9
 * @since 0.1.9
 * @package BP-Media
 * @subpackage Util
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

class BPM_testData {


	private function  __construct() {}


	/**
	 * Clears ALL users from the site, except the site admin
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 */

	public function clearUsers(&$error=null) {

	}


	/**
	 * Clears ALL uploaded content from the site, except items owned by the site admin
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 */

	public function clearItems(&$error=null) {

	}


	/**
	 * Loads user data into the system from a manifest file
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to manifest file
	 */

	public function loadUsers($path, &$error=null) {

	}


	/**
	 * Loads media items into the system from a manifest file
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to manifest file
	 */

	public function loadItems($path, &$error=null) {

	}








} // End of class BPM_testData

?>