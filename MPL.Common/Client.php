<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Client
  {
    // Declarations
    private static $ip;

    // Public functions
  	public static function GetClientIP(): string {
  	  if (!self::$ip) {
    		$ip = 'Unknown';

    		if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];		
    		} else if (isset($_SERVER['REMOTE_ADDR'])) {
    			$ip = $_SERVER['REMOTE_ADDR'];
    		}
    		self::$ip = $ip;
    	}

    	return self::$ip;
  	}
  }
}
?>