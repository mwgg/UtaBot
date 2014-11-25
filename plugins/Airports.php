<?php
Class Airports extends Actions {

    /** 
     * Displays great circle distance between two airports in NM & KM
     * Users shall supply two 4-letter airport ICAO codes separated by spaces
     */
    public static function gc($channel, $nickname, $args) {
        if ( count($args) == 2 ) {
            
            $filename = dirname(dirname(__FILE__)) . "/data/github.com/mwgg/Airports/airports.json";
            if ( !file_exists($filename) ) { return NULL; }
            $a_file = file_get_contents($filename);
            $airports = json_decode($a_file);
            
            $dep = strtoupper($args[0]);
            $arr = strtoupper($args[1]);
    		
    		if ( isset($airports->$dep) && isset($airports->$arr) ) {
    		    $nm = GreatCircle::distance($airports->$dep->lat, $airports->$dep->lon, $airports->$arr->lat, $airports->$arr->lon, NM);
    		}
    		else {
    		    return "Please check if entered ICAO codes are valid";
    		}
    		
    		return "Great circle distance " . $dep . "-" . $arr . ": " . round($nm, 1) . " nm, " . round($nm*1.852, 1) . " km";
        }
        else {
            return "Please specify exactly two ICAO codes separated by space";
        }
    }
    
    /** 
     * Alias for 'gc'
     */
    public static function greatcircle($channel, $nickname, $args) {
        return self::gc($channel, $nickname, $args);
    }
    
    /** 
     * Requires github.com/mwgg/Airports
     * Displays airport information, such as name, city, country, coordinates, elevation, local time, sunrise/sunset
     * Users shall supply one 4-letter airport ICAO code
     */
    public static function airport($channel, $nickname, $args) {
        if ( count($args) > 0 ) {
            $filename = dirname(dirname(__FILE__)) . "/data/github.com/mwgg/Airports/airports.json";
            if ( !file_exists($filename) ) { return NULL; }
            $a_file = file_get_contents($filename);
            $airports = json_decode($a_file);
            $icao = strtoupper($args[0]);
            
            if (isset($airports->$icao)) {
                
                $lt = self::utc2lt(time(), $airports->$icao->icao, $airports->$icao->tz);
                
                $sunrise = date_sunrise($lt["ts"], SUNFUNCS_RET_TIMESTAMP, $airports->$icao->lat, $airports->$icao->lon);
                $sunrise_lt = self::utc2lt($sunrise, $icao, $airports->$icao->tz);
                $sunset = date_sunset($lt["ts"], SUNFUNCS_RET_TIMESTAMP, $airports->$icao->lat, $airports->$icao->lon);
                $sunset_lt = self::utc2lt($sunset, $icao, $airports->$icao->tz);
                
                $msg = array();
                $msg[] = implode(", ", array($airports->$icao->name, $airports->$icao->city, $airports->$icao->country) );
                $msg[] = "Lat: " . $airports->$icao->lat . ", Lon: " . $airports->$icao->lon . ", Elevation " . $airports->$icao->elevation . " ft, " . round($airports->$icao->elev/3.2808, 1) . " m";
                $msg[] = "Local time: " . strtoupper(date("dMy H:i:s", $lt["ts"])) . " " . $lt["tz"];
                $msg[] = "Sunrise: " . strtoupper(date("H:i", $sunrise_lt["ts"])) . " " . $sunrise_lt["tz"] . ", " . strtoupper(date("H:i", $sunrise)) . " UTC";
                $msg[] = "Sunset: " . strtoupper(date("H:i", $sunset_lt["ts"])) . " " . $sunset_lt["tz"] . ", " . strtoupper(date("H:i", $sunset)) . " UTC";
                
                return $msg;
            }
            else {
                return "No records for " . strtoupper($args[1]);
            }
            
        }
        else {
            return "Please specify ICAO code";
        }
    }
    
    // helper methods //
    
    private static function utc2lt($time, $icao, $timezone) {
    	
        $c_date = date("dMy", $time);
        $c_time = date("H:i:s", $time);

        $tz   = new DateTimeZone($timezone);
        $date = new DateTime($c_date . " " . $c_time . " UTC");
        $date->setTimeZone($tz);
        $times = $date->getTimestamp();
        $newdate = $date->format('dMy H:i:s');
        $timezone = $date->format('T');

        $local_ts = strtotime($newdate);
        
        return array(
        	"ts" => $local_ts,
        	"tz" => $timezone
    	);
    }

}
?>
