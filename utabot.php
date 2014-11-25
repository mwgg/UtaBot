<?php
set_time_limit(0);
date_default_timezone_set("UTC");

require_once("IRC.php");
require_once("Actions.php");
require_once("Misc.php");

require_once("class/github.com/mwgg/GreatCircle/GreatCircle.php");

foreach (glob( dirname(__FILE__) . "/plugins/*.php" ) as $filename) { require_once($filename); }

$halp  = "\nUsage: php bot.php [options]\n\n";
$halp .= "-s <server>      IRC server name or IP address\n";
$halp .= "-p <port>        [Optional] Port to connect to, defaults to 6667\n";
$halp .= "-n <nickname>    Nickname to use\n";
$halp .= "-r <realname>    [Optional] Real name to use. If not specified, nickname value will be used\n";
$halp .= "-c '<channels>'  IRC channels enclosed in double or single quotes, separated by spaces\n\n";
$halp .= "-a '<password>'  Administration password (required for bot related features like changing nicknames)\n\n";

$shortopts  = "";
$shortopts .= "h::"; // help
$shortopts .= "s:"; // server
$shortopts .= "p:"; // port
$shortopts .= "n:"; // nickname
$shortopts .= "r:"; // real name
$shortopts .= "c:"; // channels
$shortopts .= "a:"; // channels

$longopts = array("help::");

$options = getopt($shortopts, $longopts);

if ( isset($options["h"]) || isset($options["help"]) ) { die($halp); }

echo "[Optional] Provide optional identification for server or nick\n(i.e. PASS password; NICKSERV identify password): ";
if (PHP_OS == 'WINNT') {
    $options["pwd"] = stream_get_line(STDIN, 1024, PHP_EOL);
} else {
    $options["pwd"] = trim(readline());
}

$channels = array();

if ( isset($options["c"]) ) {
    while ( strstr($options["c"], "  ") ) { $options["c"] = str_replace("  ", " ", $options["c"]); }
    $channels = explode(" ", $options["c"]);
}

if ( isset($options["a"]) ) { $adminPass = $options["a"]; } else { $adminPass = NULL; }

if ( !isset($options["s"]) ) { die("Please specify IRC server.\n" . $halp); } else { $server = $options["s"]; }
if ( !isset($options["n"]) ) { die("Please specify nickname.\n" . $halp); } else { $nickname = $options["n"]; }
if ( !isset($options["c"]) || !count( $channels ) > 0 || !strlen($channels[0]) > 0 ) { die("Please IRC channels separated by spaces enclosed in quotes (i.e. '#chan1 #chan2').\n" . $halp); } else { $nickname = $options["n"]; }
if ( !isset($options["p"]) ) { $port = 6667; } else { $port = $options["p"]; }
if ( !isset($options["r"]) ) { $realname = $nickname; } else { $realname = $options["r"]; }

while(true) {
    
    $up = time();
    
    $irc = new IRC($server, $port, $nickname, $realname, $options["pwd"], $channels);
    $irc->connect();
    $irc->authorize();
    $irc->join();
    $irc->handleRecv();
    unset($irc);
}

?>
