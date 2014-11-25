<?php
class Actions {
    
    public $plugins;
    
    function __construct() {
        
        $this->plugins = array();
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, "Actions")) {
                foreach ( get_class_methods($class) as $sub ) {
                    $this->plugins[$sub] = $class;
                }
            }
        }
        unset($this->plugins["__construct"]);
    }

}

?>