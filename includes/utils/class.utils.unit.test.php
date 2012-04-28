<?php

/**
 * BP-MEDIA UNIT TEST FUNCTIONS
 * Utility functions that allow command-line unit tests to be run from within the plugin's admin interface
 *
 * @version 0.1.9
 * @since 0.1.9
 * @package BP-Media
 * @subpackage Unit Test
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

class BPM_uTest {


	private function  __construct() {}


	/**
	 * Runs the unit test script at the server's command prompt
	 *
	 * @param string $val | Value to search for
	 * @param array $array | Array to search
	 * @return bool | True if val exists. False if not.
	 */

	public static function run() {

		global $bpm;

		$php_path = $bpm->config->getKeyVal($tree="system", $branch="unitTest", $key="PHPPath", &$valid);
		$test_core_path = $bpm->config->getKeyVal($tree="system", $branch="unitTest", $key="testsuitePath", &$valid);
		$options = $bpm->config->getKeyVal($tree="system", $branch="unitTest", $key="options", &$valid);
		
		$fail = ini_get ('apc.enable_cli');
		
		if($fail){
			
			$result =  "CANNOT RUN UNIT TESTS ON THIS SERVER\n";
			$result .= "=====================================================\n\n";
			
			
			$error .= "Due to a serious defect in the version of PHP that's installed on your server, ";
			$error .= "BP-Media cannot run unit tests without crashing Apache. That would probably make your web host very upset.\n";	
			
			$result .= self::formatText(80, "", "", $error);
			
			$error = "To fix this problem, you need to edit your php.ini file, set 'apc.enable_cli = 0', save it, restart your web server, and run the tests again";	
			$result .= self::formatText(80, "", "", $error);			
			
		}
		else {
			
			$command = $php_path . " " . $test_core_path . "/test.php " . $options;
			$result = shell_exec($command);															
		}


		// Replace '<' and '>' with '[' and ']'. Otherwise the browser thinks
		// we're sending it XML, and screws-up the output
		
		$result = preg_replace('|<|', '[', $result);
		$result = preg_replace('|>|', ']', $result);
		
		$result = preg_replace('|\n|', '<br>', $result);
		
		return $result;
				
	}
	
	
	/**
         * Formats a text string for printing in the terminal window
         *
         * @version 0.1.9
         * @since 0.1.9
	 *
	 * @param int $term_width | Total width of the terminal window (typically 80 columns)
	 * @param string $left_pad | Character string to pad start of each line with
	 * @param string $right_pad | Character string to pad end of each line with
	 * @param string $text | Text string to process
	 *
         * @return string $result | Formatted text
         */

	public function formatText($term_width, $left_pad, $right_pad, $text) {
		
		// In most situations, the PHP wordwrap() function is sufficient for this 
		// job @link http://php.net/manual/en/function.wordwrap.php but it doesn't
		// indent text
		
		if( strlen($text) == 0 ){			
			return null;			
		}
		
		// The printable line width is the terminal width minus the width of the 
		// padding strings
		
		$max_line_width = $term_width - ( strlen($left_pad) + strlen($right_pad) );
				
		$raw_words = explode(" ", $text);
		$words = array();
		
		// Handle "words" that are longer than $max_line_width (this can happen
		// when printing file path strings)
		
		foreach($raw_words as $check_word){
			
			if( strlen($check_word) <= $max_line_width ){
				
				$words[] = $check_word;
			}
			else {
				
				$word_split = str_split($check_word, $max_line_width);
				$words = array_merge($words, $word_split);
				unset($word_split);
			}
			
		}
		unset($check_word);
		
		$total_words = count($words) - 1;
		$current_word = 0;		
		
		// Fill a line with words until the current length + the length of the next word
		// in the array would exceed the max line width, then drop down to the next line
		
		while( $current_word <= $total_words ){
			
			$current_width = 0;
			$current_line = "";
			
			while( ($current_width <= $max_line_width) && ($current_word <= $total_words) ){
				
				if( ( strlen($words[$current_word]) + $current_width) > $max_line_width ){
														
					break;
				}
				else{															
					$current_line .= $words[$current_word] . " ";
					$current_width += strlen($words[$current_word]) + 1;
					$current_word++;					
				}			
				
			}
				
			$result .= $left_pad . $current_line;
				
			// When jumping down to a new line, pad the end of the line with spaces 
			// so the $right_pad characters on each line align with each other
			
			$align_spaces = $max_line_width - $current_width;

			for($i=0; $i<=$align_spaces; $i++){

				$result .= " ";
			}
					
			$result .= $right_pad . "\n";
						
		}		
		
		return $result;

	}	




} // End of class BPM_uTest

?>