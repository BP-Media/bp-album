<?php

/**
 * BP-MEDIA SYSTEM UTILITY FUNCTIONS
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

class BPM_sUtil {


	private function  __construct() {}


	/**
	 * Lofts a flat array of nodes into a rooted directed tree in O(n) time
	 * with only O(n) extra memory. This is also known as the "in-place quick
	 * union" algorithm.
	 *
	 * @link http://en.wikipedia.org/wiki/Tree_(graph_theory)
	 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
	 * @link http://en.wikipedia.org/wiki/Quicksort (in-place version)
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param array $nodes | Flat array of nodes
	 * @return array $result | Hierarchical array of nodes
	 */

	public function loftHierarchy($nodes) {

		$tree = array();

		foreach( $nodes as $node_id => $data){

			// Note: we can operate directly on the passed parameter, because unless
			// explicitly told not to by using the "&$" sigil, PHP passes copies
			// of variables into a function.

			$nodes[$node_id]["node_id"] = $node_id;	    // Insert the node_id into each node to make the data
								    // structure easier to use. Note the unit tests are very
								    // picky about the order this gets done in because it
								    // affects its position in the output array.
			if( empty($data["parent"]) ){

				$tree["children"][$node_id] =& $nodes[$node_id];
			}
			else {
				$nodes[$data["parent"]]["children"][$node_id] =& $nodes[$node_id];
			}
		}

		return $tree;
	}

	
	/**
	 * Finds the longest intersect between a walk and a tree.
	 *
	 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
	 * @link http://en.wikipedia.org/wiki/Breadth-first_search
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param array $walk | Walk array
	 * @param array $tree | Tree array
	 * @return array $result | Walk key and matching node id
	 */

	public function walkIntersectTree($walk, $tree, &$error=null) {


		if( !is_array($walk) ){

			$error = array(
				'numeric'=>1,
				'text'=>"Walk is not a valid array",
				'data'=>array( "walk"=>$walk, "tree"=>$tree),
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}

		if( !is_array($tree) ){

			$error = array(
				'numeric'=>2,
				'text'=>"Tree is not a valid array",
				'data'=>array( "walk"=>$walk, "tree"=>$tree),
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}



		// Loop through each child node, searching for the
		// child node with the longest walk
		// ================================================

		$min_offset = null;
		$min_node = null;

		foreach( $tree["children"] as $node_id => $data){

			if($data["slug"] == $walk[0]){

				$reduced_walk = array_slice($walk, 1);
				$intersect = self::walkIntersectTree_iterator($reduced_walk, $data);

				if( ($min_offset === null) || ($intersect["walk_offset"] < $min_offset) ){

					$min_offset = $intersect["walk_offset"];
					$min_node = $intersect["node_id"];
				}
			}

		}

		// Return the child node with the longest walk, or if
		// there was no matching child node, return this node
		// ================================================

		if($min_offset === null){

			$result = array(
					    "endpoint_id"=>null,
					    "endpoint_name"=>null,
					    "walk_key"=>null,
					    "transect"=>array()
			);
		}
		else {

			// Convert offset to array key number so functions further down
			// the chain can use array_slice() to find the tokens after the
			// endpoint that correspond to actions/arguements (if they exist)

			$walk_key = (count($walk) - $min_offset) - 1;

			$result = array(
					    "endpoint_id" => $min_node,
					    "endpoint_name"=>$walk[$walk_key],
					    "walk_key" => $walk_key,
					    "transect"=>array_slice($walk, ($walk_key +1) )
			);
		}

		return $result;

	}


	/**
	 * Finds the longest intersect between the walk and the tree.
	 *
	 * @link http://en.wikipedia.org/wiki/Glossary_of_graph_theory#Walks
	 * @link http://en.wikipedia.org/wiki/Breadth-first_search
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 * @param array $walk | Walk array
	 * @param array $tree | Tree array
	 * @return array $result | Walk offset and matching node id
	 */

	public function walkIntersectTree_iterator($walk, $tree) {


		// Calculate offsets
		// ================================================

		$walk_offset = count($walk);

		if( is_array($tree["children"]) ){

			$children_count = count($tree["children"]);
		}
		else {
			$children_count = 0;
		}

		// If either termination condition is met, return
		// ================================================

		if( ($walk_offset == 0) || ($children_count == 0) ){

			$result = array(    "node_id"=>$tree["node_id"],
					    "walk_offset"=>$walk_offset
			);

			return $result;
		}

		// Loop through each child node, searching for the
		// child node with the longest walk
		// ================================================

		$min_offset = null;
		$min_node = null;

		foreach( $tree["children"] as $node_id => $data){

			if($data["slug"] == $walk[0]){

				$reduced_walk = array_slice($walk, 1);
				$intersect = self::walkIntersectTree_iterator($reduced_walk, $data);

				if( ($min_offset === null) || ($intersect["walk_offset"] < $min_offset) ){

					$min_offset = $intersect["walk_offset"];
					$min_node = $intersect["node_id"];
				}
			}

		}

		// Return the child node with the longest walk, or if
		// there was no matching child node, return this node
		// ================================================

		if($min_offset === null){

			$result = array(
					    "node_id"=>$tree["node_id"],
					    "walk_offset"=>$walk_offset
			);
		}
		else {
			$result = array(
					    "node_id"=>$min_node,
					    "walk_offset"=>$min_offset
			);
		}

		return $result;

	}

	
	/**
	 * Recursively key-intersects two arrays, preserving the values in the $master array.
	 * For 1-level deep arrays, PHP's native array_intersect_key() is slightly faster.
	 *
	 * It's approximately 40 times faster to intersect arrays based on keys instead of values,
	 * because of the way PHP stores arrays and because keys guarantee cardinality.
	 *
	 * @param array $master | Master array
	 * @param array $slave | Slave array
	 */

	public static function keyIntersect($master, $slave) {

		$intersect = array_intersect_key($master, $slave);

		foreach($intersect as $key => $val){

			if( is_array($val) && is_array($slave[$key]) ){

				$intersect[$key] = self::keyIntersect($val, $slave[$key]);
			}

		}
		unset($key, $val);

		return $intersect;
		
	}


	/**
	 * Removes all empty walks to $depth from an associative array
	 *
	 * @param array $data | Source array
	 * @param int $depth | Depth to search walks to
	 * @return array | Pruned array
	 */

	public static function arrayPrune($data, $depth) {


		if( !is_array($data) ){

			return false;
		}
		elseif( $depth == 0 ){

			return $data;
		}

		$depth--;
		
		foreach($data as $key => $val){

			if( count($val) != 0){

				$recur_result = self::arrayPrune($val, $depth);

				if( ($depth == 0) || (count($recur_result) > 0) ){

					$result[$key] = $recur_result;
				}

			}

		}
		unset($key, $val, $recur_result);

		return $result;

	}


	/**
	 * Determines if a value exists in an array. Unlike array_search(), this function
	 * will not crash if the variable or the parent array is null, and it returns a boolean
	 * response, not an int offset, so a === test statement is not required.
	 *
	 * @param string $val | Value to search for
	 * @param array $array | Array to search
	 * @return bool | True if val exists. False if not.
	 */

	public static function valExists($val, $array) {

		if( is_array($array) ){

			$offset = array_search($val, $array);

			if($offset === false){
				return false;
			}
			else {
				return true;
			}
		}
		else {
			return false;
		}
	}


	/**
	 * Determines if a key exists in an array. Unlike array_key_exists(), this function
	 * will not crash if the variable or the parent array is null.
	 *
	 * @param string $key | Name of the key
	 * @param array $array | Array to search for key
	 * @return bool | True if key exists. False if not.
	 */

	public static function keyExists($key, $array) {

		if( is_array($array) ){

			return array_key_exists($key, $array);
		}
		else {
			return false;
		}
	}


	/**
	 * Determines if a key in an array exists and is true. Unlike array_key_exists(),
	 * this function will not crash if the variable or the parent array is null, and
	 * won't trigger a PHP warning when testing against nonexistent keys.
	 *
	 * @param string $key | Name of the key
	 * @param array $array | Array to search for key
	 * @return bool | True if key exists. False if not.
	 */

	public static function keyTrue($key, $array) {

		if( is_array($array) ){

			if( array_key_exists($key, $array) ){

				if($array[$key] == true){

					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}


	/**
	 * Returns the value of a key in an array if it exists, otherwise returns NULL. Unlike
	 * array_key_exists(), this function will not crash if the variable or the parent array
	 * is null, and won't trigger a PHP warning when fetching nonexistent keys.
	 *
	 * @param string $key | Name of the key
	 * @param array $array | Array to search for key
	 * @return bool | If key exists, value of key. Otherwise null.
	 */

	public static function keyVal($key, $array) {

		if( is_array($array) ){

			if( array_key_exists($key, $array) ){

				return $array[$key];
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}


	/**
	 * Converts a size stored in an ini var in bytes
	 *
	 * @param $varname ini var name
	 * @return int size in bytes
	 */

	public static function ini_get_bytes ( $varname ) {

		$val = ini_get( $varname );
		$result = self::formatted_size_to_bytes( $val );

		return $result;
	}


	/**
	 * Converts a formatted size to bytes
	 *
	 * @param $size_str formatted size (up to PB)
	 * @return int size in bytes
	 */

	public static function formatted_size_to_bytes( $size_str ){

		// Convert any string of the form "12345GB", "12345678 gb". "5678g", etc to "g"
		$suffix = preg_replace( '/.*?([a-z]).*/', '$1', strtolower($size_str) );

		// Convert the numeric characters and decimal point in the string to a float.
		$scalar = (float)$size_str;
		
		// Multiply scalar by prefix
		switch( $suffix ) {

			case 'p':   // Petabytes
				$result = $scalar * (1024 * 1024 * 1024 * 1024 * 1024);
				break;
			case 't':   // Terabytes
				$result = $scalar * (1024 * 1024 * 1024 * 1024);
				break;
			case 'g':   // Gigabytes
				$result = $scalar * (1024 * 1024 * 1024);
				break;
			case 'm':   // Megabytes
				$result = $scalar * (1024 * 1024);
				break;
			case 'k':   // Kilobytes
				$result = $scalar * 1024;
				break;
		}

		return $result;
	}


	/**
	 * Converts an int value in bytes to a human readable string
	 *
	 * @param int $bytes | size value in bytes
	 * @param int $precision | number of decimal places to round to
	 * @return string | composited text string
	 */
	public static function bytes_to_formatted_size($bytes, $precision = 2) {

		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');

		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);

		$bytes /= pow(1024, $pow);

		return round($bytes, $precision) . ' ' . $units[$pow];
	}


	/**
	 * Converts an int value in bytes to a SI-formatted human readable string.
	 * Example "22714" => "22.7K"
	 *
	 * @param float $raw | raw size value
	 * @param int $digits | number of digits to show in the result (must be >= 3)
	 * @return string | composited text string
	 */
	public static function siFormat($raw, $digits=3, $drop_decimal=false) {

		$raw = (float)$raw;

		if($raw < 1000){

			if($drop_decimal) {

				$result = round($raw);
			}
			else {
				$primary_digits = strlen( (string)round($raw) );
				$decimal_digits = $digits - $primary_digits;

				$result = number_format($raw, $decimal_digits, '.', '');
			}
		}
		else {

			// If the raw value is negative, invert it so that the
			// logarithm and power math below works properly

			if($raw < 0){
				$negative = true;
				$raw = -$raw;
			}

			$units = array('K', 'M', 'G', 'T');

			$pow = floor(($raw ? log($raw) : 0) / log(1000));
			$pow = min($pow, (count($units) - 1) );
			$raw /= pow(1000, $pow);

			$primary_digits = strlen( (string)round($raw) );
			$decimal_digits = $digits - $primary_digits;

			$result = number_format($raw, $decimal_digits, '.', '');

			if($negative){
				$result = -$result;
			}

			$result .= $units[$pow - 1];

		}

		return (string)$result;
		
	}


	/**
	 * Truncates a string to specific length, automatically handling strings that are formatted
	 * with unicode (two byte) encoding
	 *
	 * @param string $string | text string to trim
	 * @param int $length | number of characters to truncate string to
	 * @param bool $unicode | true: process as two-byte characters, false: process as single-byte characters, null: auto-detect
	 * @return string | truncated text string
	 */

	public static function i18n_trunc($string, $length, $unicode=null) {

		// The purpose of this function is to centralize all our string truncates in one place
		// so it's easy to change our algorithms.

		if($unicode === null){

			if ( mb_check_encoding($s,"UTF-8") == true  )
			{
				$unicode = true;
			}
			else {
				$unicode = false;
			}
		}

		if($unicode){

		    return mb_substr($string, 0, $length);
		}
		else {

		    return substr($string, 0, $length );
		}
	}


	/**
	 * Truncates a string to specific length, automatically handling strings that are formatted
	 * with unicode (two byte) encoding
	 *
	 * @param string $string | text string to trim
	 * @param int $length | number of characters to truncate string to
	 * @param bool $unicode | true: process as two-byte characters, false: process as single-byte characters, null: auto-detect
	 * @return string | truncated text string
	 */

	public static function i18n_elipsis($string, $length, $unicode=null) {

		// The purpose of this function is to centralize all our string truncates in one place
		// so it's easy to change our algorithms.

		if($unicode === null){

			if ( mb_check_encoding($s,"UTF-8") == true  )
			{
				$unicode = true;
			}
			else {
				$unicode = false;
			}
		}

		if($unicode){

			$result = mb_substr($string, 0, $length);

			if($string != $result){
				$result = trim($result); // Is this valid for mb_ strings?
				$result .= '&#8230;';
			}

			return $result;
		}
		else {

			$result = substr($string, 0, $length);

			if($string != $result){
				$result = trim($result);
				$result .= '&#8230;';
			}

			return $result;
		}
	}


	/**
	 * Get a random file path
	 *
	 * @param string $dir
	 * @param string $extension
	 * @return string random and unique file path
	 */

	public static function random_filepath( $dir, $extension='' ) {
	    
		$dir = untrailingslashit($dir);
		$rand_string = self::random_string();

		if ( glob( "$dir/$rand_string*" ) ) // The odds of a collision are ridiculous, but still worth checking
			return self::random_filepath($dir, $extension);
		elseif($extension)
			return "$dir/$rand_string.$extension";
		else
			return "$dir/$rand_string";
	}


	/**
	 * Random string
	 *
	 * @param int $length string length
	 * @return random string
	 */

	public static function random_string( $length = 16 ){
	    
		$rand_string ='';
		$aZ09 = array_merge(range('A', 'Z'), range('a', 'z'), range(0, 9));

		for($c=0; $c < $length; $c++)
			$rand_string .= $aZ09[mt_rand(0,count($aZ09)-1)];

		return $rand_string;
	}


	/**
	 * Write data to file
	 *
	 * @param string $data data to write
	 * @param string $file_path
	 * @return string|WP_Error file path or error
	 */

	public static function write_file( $data, $file_path ){

		$handle = self::new_file_handle($file_path);
		
		if( is_wp_error( $handle ) )
			return $handle;

		fwrite( $handle, $data );
		fclose( $handle );
		
		clearstatcache();

		return $file_path;
	}


	/**
	 * Open a new file handle
	 *
	 * @param string $file_path
	 * @return resource|WP_Error handle or error
	 */

	public static function &new_file_handle($file_path){

		if ( ! wp_mkdir_p( dirname( $file_path ) ) )
			return new WP_Error( 'bp_media:filesystem:cannot_create_dir', sprintf( __('Cannot create the directory %s.',"bp-media"), dirname( $file_path ) ) );

		$handle = @fopen($file_path, 'wb');

		if ( ! $handle )
			return new WP_Error( 'bp_media:filesystem:cannot_create_file', sprintf( __('Cannot create the file %s.',"bp-media"), $file_path ) );

		return $handle;
	}
	
	/**
	 * Takes an int or a float and turns it into a string with commas
	 * inserted between groups of three numbers on the left side of the decimal point.
	 *
	 * @param int/float  $num | Single number to convert to string with formating
	 * @return string $formatted | Returns $num formatted as string
	 */

	 public static function format_num($num)
	 {
		// Checks if number then uses format number to output the number with a check if it has a decimal
		if(is_numeric($num))
		{
			return number_format($num, (false == strpos($num, '.') ? 0 : strlen($num)- (strpos($num, '.')+1))  );
		}
	 }
	 
	/**
	 * Takes a POST value as received from PHP and applies whatever processing is necessary to 
	 * turn it back into what the user actually entered into the form.
	 *
	 * @param string $raw | Raw value as received from PHP
	 * @return string $result | Processed string
	 */

	 public static function formVal($raw)
	 {
		// PHP likes to add backslashes to escape backslash "\" characters entered into forms. But, this
		// can be disabled by setting "magic_quotes_gpc" false in the php.ini file. However, PHP has deprecated
		// this setting, and the function used to check for it, get_magic_quotes_gpc(), doesn't return the correct
		// value on my Windows-based dev system (PHP adds backslashes but get_magic_quotes_gpc() returns false)

		// This function makes sure all of our form un-escaping tasks happen in one place so it's easy to change
		// when PHP decides what they're doing. NOTE: this function cannot be unit-tested with our current automated
		// test system because it requires sending POST commands to the server.

		if( !is_array($raw) ){

			$result = stripslashes($raw);
		}
		else {

			// If the form value is an array, recursively traverse any sub-arrays
			// and strip slashes

			foreach($raw as $key => $val ){

				$result[$key] = self::formVal($val);
			}

		}
			
		return $result;
			     
	 }

} // End of class BPM_sUtil

?>