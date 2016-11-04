<?php

class Timers {
	private $plugins;

	function __construct(&$irc){
	        $this->plugins = array();
			foreach (get_declared_classes() as $class) {
			    if (is_subclass_of($class, "Timer")) {
			            $this->plugins[] = new $class($irc);
			    }
			}
	}

	function updateTimers(){
		foreach ( $this->plugins as $k => $class ){
			$class->updateTimer();
		}
	}
}

?>