<?php
class Wx extends Actions {
    
    /** 
     * Displays aviation weather reports (METARs)
     * Users shall supply 4-letter airport ICAO codes separated by spaces
     */
    public static function metar($channel, $nickname, $args) {

        if ( count($args) > 0 ) {
            
            $msg = array();
            
            foreach($args as $icao) {
                $url = "http://tgftp.nws.noaa.gov/data/observations/metar/stations/".strtoupper($icao).".TXT";
                $metar = file_get_contents($url);
                if ( $metar !== false ) {
                    $metar = explode("\n", $metar);
                    $msg[] = $metar[1];
                }
                else {
                    $msg[] = "No METAR for " . $icao;
                }
            }
            return $msg;
            
        }
        else {
            return "Please specify at least one ICAO code";
        }
    }
    
    /** 
     * Displays aviation weather forecasts (TAFs)
     * Users shall supply 4-letter airport ICAO codes separated by spaces
     */
    public static function taf($channel, $nickname, $args) {
        
        if ( count($args) > 0 ) {
            
            $msg = array();
            
            foreach($args as $icao) {
                $url = "ftp://tgftp.nws.noaa.gov/data/forecasts/taf/stations/".strtoupper($icao).".TXT";
                $taf = file_get_contents($url);
                if ( $taf !== false ) {
                    $taf = explode("\n", $taf);
                    unset($taf[0]);
                    //array_values($taf);
                    foreach ($taf as $line) {
                        $msg[] = $line;
                    }
                }
                else {
                    $msg[] = "No TAF for " . $icao;
                }
            }
            return $msg;
        }
        else {
            return "Please specify ICAO code";
        }
    }

}
?>
