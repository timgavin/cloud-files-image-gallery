<?php
		
	// create a random hash
	function generate_hash($length = 8, $chars = 'BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz1234567890') {
		
		// we've removed the vowels from the $chars var to avoid dirty words
		
		// length of character list
		$chars_length = (strlen($chars) - 1);
		
		// start our string
		$string = $chars{rand(0, $chars_length)};
		
		// generate random string 
		for($i = 1; $i < $length; $i = strlen($string)) {
			
			// grab a random character from our list
			$r = $chars{rand(0, $chars_length)};
			
			// make sure the same two characters don't appear next to each other
			if($r != $string{$i - 1}) $string .= $r;
		}
		return $string;
	}
	
	
	// slug a string
	function slug($text) { 
		$text = str_replace("'s",'s',$text);
		$text = str_replace("'",'',$text);
		$text = str_replace("&#38;",'and',$text);
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
		$text = trim($text, '-');
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = strtolower($text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		if(empty($text)) {
			return 'n-a';
		}
		return $text;
	}