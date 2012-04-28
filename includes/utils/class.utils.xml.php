<?php

/**
 * BP-MEDIA XML FUNCTIONS
 * Handle loading, parsing, validating, and saving XML files
 *
 * @version 0.1.9
 * @since 0.1.9
 * @package BP-Media
 * @subpackage XML Tools
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

class BPM_xml {
    
	
	public function __construct() {}


	/**
	 * Converts an XML file into a hierarchical array
	 *
	 * @version 0.1.9
	 * @since 0.1.9
	 *
	 * @param string $contents | XML to parse
	 * @param bool $get_attributes | If true, the function will get the attributes as well as the tag values
	 * @param string $priority | 'tag' or 'attribute'
	 * @param array &$error | Array containing numeric and text error information
	 * @return bool/array $result | False on failure. Parsed XML array on success.
	 */

	public function parseFile($file, $get_attributes=1, $priority='tag', &$error=null) {

		$data = file_get_contents($file);

		if(!$data)
		{
			$error = array(
				'numeric'=>1,
				'text'=>"Couldn't open the file",
				'data'=>$data,
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}

		$result = $this->parseString($data, $get_attributes, $priority, $error);

		return $result;

	}


	/**
	 * Converts an XML string into a hierarchical array
	 * 
	 * @version 0.1.9
	 * @since 0.1.9
	 *
	 * @author Based on the "xml2array" parser
	 * @link http://www.bin-co.com/php/scripts/xml2array/
	 * @link http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
	 *
	 * @param string $contents | XML to parse
	 * @param bool $get_attributes | If true, the function will get the attributes as well as the tag values
	 * @param string $priority | 'tag' or 'attribute'
	 * @param array &$error | Array containing numeric and text error information
	 * @return bool/array $result | False on failure. Parsed XML array on success.
	 */

	function parseString($contents, $get_attributes=1, $priority='tag', &$error=null) {

		if(!$contents)
		{
			$error = array(
				'numeric'=>1,
				'text'=>"Called with empty XML string",
				'data'=>$contents,
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}

		if(!function_exists('xml_parser_create'))
		{
			$error = array(
				'numeric'=>2,
				'text'=>"Host system is missing the PHP xml_parser_create function ",
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}


		// Setup PHP's XML parser
		// =======================================================
		
		$parser = xml_parser_create('');
		xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, trim($contents), $xml_values);
		xml_parser_free($parser);

		if(!$xml_values)
		{
			$error = array(
				'numeric'=>3,
				'text'=>"Parser error",
				'file'=>__FILE__, 'line'=>__LINE__, 'method'=>__METHOD__,
				'child'=>null
			);
			return false;
		}

		$xml_array = array();
		$parents = array();
		$opened_tags = array();
		$arr = array();
		$current = &$xml_array;


		// Process tags
		// =======================================================

		$repeated_tag_index = array(); 

		foreach($xml_values as $data) {
		    
			unset($attributes, $value); // Important
			extract($data);

			$result = array();
			$attributes_data = array();

			// Handle values
			// =======================
			if( isset($value) ) {

				if($priority == 'tag'){
				    
					$result = $value;
				}
				else {	
					// Put the value in a assoc array if we are in the 'Attribute' mode
					$result['value'] = $value;
				}

			}

			// Handle attributes
			// =======================
			if(isset($attributes) and $get_attributes) {

				foreach( $attributes as $attr => $val ) {

				    if($priority == 'tag'){
					
					    $attributes_data[$attr] = $val;
				    }
				    else {
					    // Set all the attributes in a array called 'attr'
					    $result['attr'][$attr] = $val; 
				    }

				}

			}

			// CASE 1: Opening a two-token tag '<tag_name>' + '</tag_name>'
			// =========================================================================

			if($type == "open") {

			    $parent[$level-1] = &$current;

			    // Insert new tag
			    if( !is_array($current) || ( !in_array($tag, array_keys($current) ) ) ) {

				    $current[$tag] = $result;
				    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
				    $repeated_tag_index[$tag.'_'.$level] = 1;

				    $current = &$current[$tag];

			    }
			    // There was another element with the same tag name
			    else { 

				    // If there is a 0th element it's already an array
				    if(isset($current[$tag][0])) {

					    $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
					    $repeated_tag_index[$tag.'_'.$level]++;
				    }
				    // Otherwise, combine the tags into an array
				    else {

					    $current[$tag] = array($current[$tag],$result);
					    $repeated_tag_index[$tag.'_'.$level] = 2;

					    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

						    $current[$tag]['0_attr'] = $current[$tag.'_attr'];
						    unset($current[$tag.'_attr']);
					    }

				    }

				    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
				    $current = &$current[$tag][$last_item_index];

			    }

			}

			// CASE 2: Processing a single-token tag '<tag_name />'
			// =========================================================================

			elseif($type == "complete") { 

				// New Key
				if(!isset($current[$tag])) {

					$current[$tag] = $result;
					$repeated_tag_index[$tag.'_'.$level] = 1;
					if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

				}
				// Existing key. Put all elements inside a list(array)
				else {

					// Already an array. Push the new element into that array.
					if( isset($current[$tag][0]) && is_array($current[$tag]) ) {

						$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

						if($priority == 'tag' and $get_attributes and $attributes_data) {
						    $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						}
						$repeated_tag_index[$tag.'_'.$level]++;

					}

					// Not already an array. Expand into array.
					else {

						$current[$tag] = array( $current[$tag], $result );
						$repeated_tag_index[$tag.'_'.$level] = 1;

						if( ($priority == 'tag') && $get_attributes) {

						    if(isset($current[$tag.'_attr'])) {

							    // The attribute of the last(0th) tag must be moved as well
							    $current[$tag]['0_attr'] = $current[$tag.'_attr'];
							    unset($current[$tag.'_attr']);
						    }

						    if($attributes_data) {
							    $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
						    }

						}

						$repeated_tag_index[$tag.'_'.$level]++; // The 0 and 1 keys are already taken

					}

				}

			}

			// CASE 3: Closing a two-token tag '<tag_name>' + '</tag_name>'
			// =========================================================================
			
			elseif($type == 'close') {

				$current = &$parent[$level-1];
			}
			
			
		} // ENDOF: foreach($xml_values as $data) 

		return($xml_array);

	}
	
} // End of class BPM_xml

?>