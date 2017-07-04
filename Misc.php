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

	public static function getUrl($url){

		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;

	}
    
}
?>
