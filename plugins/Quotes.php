<?php
class Quotes extends Actions {

    /** 
     * Displays a random quote from The Big Lebowski
     */
    public static function lebowski($channel, $nickname, $args) {
        $filename = dirname(dirname(__FILE__)) . "/data/lebowski.txt";
        if ( !file_exists($filename) ) { return NULL; }
        $lines = file($filename);
        $num = mt_rand(0,count($lines)-1);
        return $lines[$num];
    }
    
    /** 
     * Alias for 'lebowski'
     */
    public static function l($channel, $nickname, $args) {
        return self::lebowski($channel, $nickname, $args);
    }
    
    /** 
     * Displays a random quote by George Carlin
     */
    public static function carlin($channel, $nickname, $args) {
        $filename = dirname(dirname(__FILE__)) . "/data/carlin.txt";
        if ( !file_exists($filename) ) { return NULL; }
        $lines = file($filename);
        $num = mt_rand(0,count($lines)-1);
        return $lines[$num];
    }
    
    /** 
     * Alias for 'carlin'
     */
    public static function c($channel, $nickname, $args) {
        return self::carlin($channel, $nickname, $args);
    }
    
}
?>
