<?php

/**
 * BP-MEDIA BACKUP LOADER FUNCTIONS
 * These functions load content from BP-Media backup containers
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

class BPM_backupLoader {


	private function  __construct() {}


	/**
	 * Imports a backup container file into the system
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to container file
	 * @param array $ctrl | Control parameters
	 *	=> VAL @param bool $allow_remote | True to allow container files to be loaded from a remote server
	 */

	public function importContainer($path, $ctrl=null, &$error=null) {

	}


	/**
	 * Unzips a container file into BP-Media's temp folder
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to archive file
	 */

	public function unzipContainer($path, &$error=null) {

	}


	/**
	 * Processes a user manifest file
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to manifest file
	 */

	public function processUserManifest($path, &$error=null) {

	}


	/**
	 * Processes an album manifest file
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param string $path | Path to manifest file
	 */

	public function processAlbumManifest($path, &$error=null) {

	}


	/**
	 * Imports an album
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param array $data | Album data
	 */

	public function importAlbum($data, &$error=null) {

	}


	/**
	 * Imports a media item
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param array $data | Media item data
	 */

	public function importItem($data, &$error=null) {

	}

	


} // End of class BPM_backupLoader

?>