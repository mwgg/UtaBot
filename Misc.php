<?php
class Misc {
    
    public static function analyze($message) {
    	$result = array();

        // fetch url page titles
        $matches = array();
        $regex = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
        preg_match_all($regex, $message, $matches);

		foreach ($matches[0] as $url) {
			$title = array();
		    $str = file_get_contents($url);
		    if(strlen($str)>0){
		        preg_match("/<title>(.*)<\/title>/i",$str,$title);
		        if ( strlen($title[1]) > 0 ) {
		            $result[] = "^ " . $title[1];
		        }
		    }
    	}

        return $result;
    }
    
}
?>
