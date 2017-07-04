<?php

class Timer {
	protected $timeout;	// how long between plugin executions
	private $timer;		// keeps track of how long it has been since last execution
	private $lastCheck;	// keeps track of the last time updateTimer() ran
	protected $irc;		// reference to the main IRC object so you can do IRC things from the execute method

	function __construct(&$irc){
		$this->lastCheck = time();
		$this->timer = 0;
		$this->irc = $irc;
		$this->initialize();
	}

	public function updateTimer(){
		$this->timer += ( time() - $this->lastCheck );
		if ( $this->timer >= $this->timeout ){
			$this->execute();
			$this->timer = 0;
		}
		$this->lastCheck = time();
	}

	protected function initialize(){
		// put code you want executed after __construct here
		// in case your plugin needs to initialize some stuff
	}

	protected function execute(){
		// put the code you want to execute when the
		// timeout is reached in the execute function
	}
}

?>