<?php

/**
 * BP-MEDIA NETWORK UTILITY FUNCTIONS
 * Utility functions that do commonly used minor tasks
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

class BPM_Network_Utils {

	private function __construct() {}

	/**
	 * Check a media url and follow redirects
	 *
	 * @param string $url url to check
	 * @return string|WP_Error final url or error
	 */
	
	public static function check_url( $url ){
	    
		if( empty($url) )
			return new WP_Error('bp_media:url_handle:no_url',sprintf(__("Url handler called without url.","bp-media"),$url) );

		$url = trim($url);
		$url_components = parse_url($url);

		// We need at least a host and a path, or a query
		if( empty($url_components['host']) || ( empty($url_components['path']) && empty($url_components['query']) ) )
			return new WP_Error('bp_media:url_handle:incomplete_url',sprintf(__("Stop processing url %s : doesn't seem a complete media url.","bp-media"),$url) );

		// If the url is given without scheme, http is assumed
		if( empty($url_components['scheme'])  ) {
			$url = 'http://'.$url;
			$url_components['scheme'] = 'http';
		}

		$test_url = $url;

		// Check if the scheme is supported and if the url returns a valid response
		switch( $url_components['scheme'] ) {
			case 'https':
				$test_url = BPM_Network_Utils::follow_redirects( $test_url );

				if ( !is_wp_error($test_url) && 200 == wp_remote_retrieve_response_code( BPM_Network_Utils::remote_head( $test_url ) ) ) {
					$url = $test_url;
					break;
				}else // If an https url is not valid, retry without SSL
					$test_url = substr_replace($url, 'http', 0, 5);

			case 'http':
				$test_url = BPM_Network_Utils::follow_redirects( $test_url );
				if( is_wp_error($test_url) )
					return $test_url;

				$head_request = BPM_Network_Utils::remote_head( $test_url );

				if( is_wp_error($head_request) )
					return new WP_Error( 'bp_media:http:request_error', sprintf( __('Error in the request of url %s (original %s ): %s - %s',"bp-media"),$test_url , $url, $head_response->get_error_code(), $head_response->get_error_message() ), $head_response->get_error_data() );
				if( 200 != wp_remote_retrieve_response_code( $head_request ) )

					return new WP_Error('bp_media:http:response_code_not_200', sprintf( __('Unsuccessful request of url %s (original %s ): response code %d - %s',"bp-media"),$test_url , $url, wp_remote_retrieve_response_code( $head_request ), wp_remote_retrieve_response_message($head_request) ) );
				$url = $test_url;

				break;

			default:
				return new WP_Error('bp_media:url_handle:invalid_url_schema',sprintf(__('Stop processing url %s : the scheme %s is not supported.',"bp-media"),$url_components['scheme']));
		}

		return $url;
	}

	/**
	 * Recursive function to follow redirects
	 *
	 * @param string $url the url to follow
	 * @param int $max_redirects
	 * @param array $followed_urls used in recursion, leave empty
	 * @return string $url final url
	 */
	public static function follow_redirects($url, $max_redirects = 5, $followed_urls = array() ) {
	    
		$head_response = BPM_Network_Utils::remote_head( $url );
		if( is_wp_error($head_response) )
			return new WP_Error( 'bp_media:http:request_error', sprintf( __('Error in the request of url %s : %s - %s',"bp-media"), $url, $head_response->get_error_code(), $head_response->get_error_message() ), $head_response->get_error_data() );

		$location = wp_remote_retrieve_header( $head_response, 'location' );

		$followed_urls[] = $url;

		if( !$location )
			return $url;
		elseif ( $max_redirects-- > 0 )
			return bp_media_follow_redirects( $location, $max_redirects, $followed_urls );
		else
			return new WP_Error('bp_media:http:too_many_redirects', sprintf( __('Too many redirects, followed urls: %s',"bp-media"), join(' -> ', $followed_urls) ) );
	}

	/**
	 * Same as wp_remote_head but cached (not persistent)
	 *
	 * Note: error responses are not cached
	 */
	public static function remote_head ( $url, $args = array() ) {

		static $cache = array();

		$cache_key = $url;
		if( $args )
			$cache_key .= ' args: ' . serialize($args);

		if( !isset( $cache[$cache_key] ) )
			if ( is_wp_error( $head = wp_remote_head( $url, $args ) ) )
				return $head;
			else
				$cache[$cache_key] = $head;

		return $cache[$cache_key];
	}

	/**
	 * Download a remote file
	 *
	 * @param string $url url to download
	 * @param string $file_path path of the saved file
	 * @param int $timeout timeout in sec
	 * @return string|WP_Error downloaded file path or error
	 */
	public static function download_file( $url, $file_path, $timeout ) {
	    
		$handle = BPM_sUtil::new_file_handle($file_path);
		if( is_wp_error( $handle ) )
			return $handle;

		$response = wp_remote_get( $url, array('timeout' => $timeout) );

		if ( is_wp_error( $response ) ) {
			fclose( $handle );
			unlink( $file_path );
			return new WP_Error( 'bp_media:http:request_error', sprintf( __('Error in the request of url %s : %s - %s',"bp-media"), $url, $response->get_error_code(), $response->get_error_message() ), $response->get_error_data() );
		}

		if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
			fclose( $handle );
			unlink( $file_path );
			return new WP_Error('bp_media:http:response_code_not_200', sprintf( __('Unsuccessful request of url %s : response code %d - %s',"bp-media"), $url, wp_remote_retrieve_response_code( $response ), wp_remote_retrieve_response_message( $response ) ) );
		}

		fwrite( $handle, wp_remote_retrieve_body($response) );
		fclose( $handle );
		clearstatcache();

		return $file_path;
	}

} // End of class BPM_Network_Utils

?>