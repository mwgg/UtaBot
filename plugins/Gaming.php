<?php
class Gaming extends Actions {
    
    /** 
     * Loads and displays number of online pilots and ATC positions on the VATSIM network
     */
    public static function vatsim($channel, $nickname, $args) {
        $url = "http://info.vroute.net/vatsim-data.txt";
        $vatsim = file_get_contents($url);
        
        $pilots = 0;
        $atc = 0;
        
        if ( $vatsim !== false ) {
            $vatsim = explode("\n", $vatsim);
            foreach ($vatsim as $line) {
                $vat = explode(":", $line);
                if ( isset($vat[3]) && $vat[3] == "PILOT" ) { $pilots++; }
                if ( isset($vat[0]) && isset($vat[3]) && $vat[3] == "ATC" && !strstr($vat[0], "ATIS") && !strstr($vat[0], "OBS") ) { $atc++; }
            }
        }
        return "VATSIM: " . $pilots . " pilots and " . $atc . " ATC positions online";
    }

}
?>