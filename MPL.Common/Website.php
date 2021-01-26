<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\Conversion;

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
    public static function ParseGet(string $name, &$value): bool {
      $returnValue = false;
      
      if (!empty($_GET) && isset($_GET[$name])) {
        $value = $_GET[$name];
        $returnValue = true;
	    }
	  
	    return $returnValue;
    }

    public static function ParseGetToString(string $name, ?string &$value): bool {
      return self::ParseGet($name, $rawValue) &&
             Conversion::TryParseString($rawValue, $value);
    }

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
    
    public static function Redirect(string $redirectURL): void {
    	if (headers_sent()) {
    		echo "<script>window.location = '{$redirectURL}'</script>";
    		echo "<noscript><meta http-equiv='refresh' content='0;url={$redirectURL}' /></noscript>";
    	} else {
    		header('Location: ' . $redirectURL); 
    		exit;
    	}
    }

    public static function RedirectToHomepage(): void {
      self::Redirect(self::$BaseLocation);
    }
  
    public static function RedirectWithRelativeURL(string $relativeURL): void {
      self::Redirect(RelativeMapping::MapRelativeUrl($relativeURL));
    }
  }

  Website::__init();
}
?>