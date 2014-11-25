<?php
class IRC {
    
    public $nick;
    
    private $socket;
    private $actions;
    
    private $server;
    private $port;
    private $realname;
    private $authentication;
    private $channels;

    private $version = "UTAbot 0.6";
    
    function __construct($server, $port, $nick, $realname, $authentication, $channels) {
        foreach (get_defined_vars() as $arg=>$val) {
            $this->$arg = $val;
        }

    }
    
    public function connect() {
        $address = gethostbyname($this->server);

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            die("Failed to create socket: " . socket_strerror(socket_last_error()) . "\n");
        } else {
            echo "OK.\n";
        }
        
        echo "Attempting to connect to '$this->server' ('$address') on port '$this->port'...";
        $result = socket_connect($this->socket, $address, $this->port);
        if ($result === false) {
            die("Connection failed: ($result) " . socket_strerror(socket_last_error($this->socket)) . "\n");
        } else {
            echo "OK.\n";
        }
        
        $this->actions = new Actions();
        
    }
    
    public function authorize() {
        $this->send("NICK " . $this->nick);
        $this->send("USER " . $this->nick . " " . $this->nick . " " . $this->nick . " " .$this->realname);
        if ( strlen($this->authentication) > 0 ) {
            $this->send($this->authentication);
        }
    }
    
    public function join() {
        foreach($this->channels as $chan) {
            $this->send("JOIN " . $chan);
        }
    }
    
    public function handleRecv() {
        while ($recv = socket_read($this->socket, 2048)) {
            echo "<< " . $recv;
            
            $message = "";
            $channel = "";
            $parts = explode(" :", $recv);
            $source_parts = explode(" ", substr($parts[0], 1));
            $user = $source_parts[0];
            $user_parts = explode("!", $user);
            $nickname = $user_parts[0];
            
            if ( isset($source_parts[2]) ) { $channel = $source_parts[2]; }
            if ( $channel == $this->nick ) { $channel = $nickname; }
            
            if ( isset($parts[1]) ) { $message = $parts[1]; }
            
            if ( strstr($recv, " 433 ") ) { // Nickname is already in use. // add | and try again
                $this->nick .= "|";
                $this->send("NICK " . $this->nick);
            }
            
            if ( strstr($recv, " 451 ") ) { // You have not registered. // try joining again
                $this->join();
            }
            
            // respond to PINGs
            if ( substr($recv, 0, 4) == "PING" ) {
                $this->send("PONG" . substr($recv, 4));
            }
            
            if ( isset($parts[1]) && substr($channel, 0, 1) == "#" ) {
                // if message came from a channel, see if there is anything we can do with the whole message
                $this->sendToChan( $channel, Misc::analyze($message) );
            }
            
            // first character is !, must be a command
            if ( substr($message, 0, 1) == "!" ) {
                if ( strstr($message, " ") ) { $params = explode(" ", $message); }
                else { $params = array($message); }
                $command = trim(strtolower(substr($params[0], 1)));
                
                if ( isset($this->actions->plugins[$command]) ) {
                    $args = explode(" ", trim($message));
                    unset($args[0]); $args = array_values(array_filter($args));
                    
                    $className = $this->actions->plugins[$command];
                    $result = $className::$command($channel, $nickname, $args);
                    $this->sendToChan( $channel, $result );
                }
            }
            
        }
        socket_close($this->socket);
        echo "Attempting to reconnect in 30 seconds...\n";
        sleep(30);
    }
    
    private function send($msg) {
        if ( !is_array($msg) ) {
            $msg = array($msg);
        }
        
        foreach ($msg as $line) {
        	if ( strlen($line) > 0 ) {
            	$line = trim($line) . "\n";
            	socket_write($this->socket, $line, strlen($line));
            	echo ">> " . $line;
        	}
        }

    }
    
    // accepts string and array of strings for $msg
    private function sendToChan($channel, $msg) {
        if ( !is_array($msg) ) {
            $msg = array($msg);
        }
        
        if ( isset($msg["chan"]) ) { $channel = $msg["chan"]; unset($msg["chan"]); }
        if ( isset($msg["server"]) ) { unset($msg["server"]); $this->send($msg); return NULL; }
        
        foreach ($msg as $line) {
        	if ( strlen($line) > 0 ) {
            	$line = "PRIVMSG " . $channel . " :" . trim($line) . "\n";
            	socket_write($this->socket, $line, strlen($line));
            	echo ">> " . $line;
        	}
        }
    }
    
}

?>