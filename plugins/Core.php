<?php
class Core extends Actions {

	public static function help($channel, $nickname, $args) {
		$help = array();
		
		$help[] = "Available commands (in a channel or PM):";
		$help[] = "\x02!metar\x02 ICAO [ICAO ...]; \x02!taf\x02 ICAO [ICAO ...];";
		$help[] = "\x02!vatsim\x02; \x02!greatcircle\x02 (\x02!gc\x02) ICAO ICAO; \x02!airport\x02 ICAO;";
		$help[] = "\x02!carlin\x02 (\x02!c\x02), \x02!lebowski\x02 (\x02!l\x02);";
		$help[] = "\x02!say\x02 #channel message; \x02!uptime\x02;";
		
		return $help;
	}
    
    // if Action method returns an array with "chan" element, other array elements will be combined in a message to be sent to specified channel
    public static function say($channel, $nickname, $args) {
        $toChan = $args[0];
        unset($args[0]); $args = array_values(array_filter($args));
        return array(
            "chan" => $toChan,
            0 => implode(" ", $args)
        );
    }
    
    public static function server($channel, $nickname, $args) {
        global $adminPass;
        if ( $args[0] != $adminPass ) { return NULL; }
        unset($args[0]); $args = array_values(array_filter($args));
        $toServer = array();
        $toServer[] = implode(" ", $args);
        $toServer["server"] = "true";
        return $toServer;
    }

    public static function quit($channel, $nickname, $args) {
        global $adminPass;
        if ( $args[0] != $adminPass ) { return NULL; }
        $toServer[] = "QUIT";
        $toServer["server"] = "true";
        return $toServer;
    }
    
    // if Action method returns an array with "server" element, other elements will be sent to the server
    public static function join($channel, $nickname, $args) {
        global $adminPass;
        if ( $args[0] != $adminPass ) { return NULL; }
        unset($args[0]); $args = array_values(array_filter($args));
        $toServer = array();
        foreach ($args as $chan) {
            $toServer[] = "JOIN " . trim($chan);
        }
        
        $toServer["server"] = "true";
        return $toServer;
    }
    
    public static function part($channel, $nickname, $args) {
        global $adminPass;
        if ( $args[0] != $adminPass ) { return NULL; }
        unset($args[0]); $args = array_values(array_filter($args));
        $toServer = array();
        foreach ($args as $chan) {
            $toServer[] = "PART " . trim($chan);
        }
        
        $toServer["server"] = "true";
        return $toServer;
    }
    
    public static function nick($channel, $nickname, $args) {
        global $adminPass;
        global $irc;
        if ( $args[0] != $adminPass ) { return NULL; }
        unset($args[0]); $args = array_values(array_filter($args));
        $newnick = $args[0];
        $irc->nick = $newnick;
        return array(
            "server" => "true",
            0 => "NICK " . trim($newnick)
        );
    }
    
    public static function uptime($channel, $nickname, $args) {
        global $up;
        $diff = time() - $up;
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$diff");
        return $dtF->diff($dtT)->format('Bot uptime: %a days, %h hours, %i minutes and %s seconds');
    }

   
}
?>
