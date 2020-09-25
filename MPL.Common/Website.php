<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Website
  {
    // Declarations
    public static $BaseLocation;

    // Constructors
    public static function __init() {
      // Scheme
    	$location = 'http';
    	if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on")
    		$location .= 's';
    	$location .= '://';
    	
    	// Server
    	$location .= $_SERVER["SERVER_NAME"];
    	
    	// Subfolder
    	$location .= RelativeMapping::GetSiteRelativeUrlSegment();
    	
    	self::$BaseLocation = $location;
    }

    // Public functions
    public static function ParsePhpInputToArray(?array &$output): bool {
      $output = null;
      $returnValue = false;

      try {
        $phpInput = file_get_contents("php://input");
        if ($phpInput) {
          $phpInput = trim($phpInput);
          if (strlen($phpInput) > 0) {
            parse_str($phpInput, $output);
          }
          $returnValue = true;
        }
        $returnValue = true;
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, 'Unable to parse PHP input into array');
        $output = null;
      }
      
      return $returnValue;
    }
  }

  Website::__init();
}
?>