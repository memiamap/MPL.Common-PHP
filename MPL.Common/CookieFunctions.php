<?php
declare(strict_types=1);

namespace MPL\Common
{
  class CookieFunctions
  {
    // Constants
    public const SAMESITE_LAX = 2;
    public const SAMESITE_NONE = 1;
    public const SAMESITE_STRICT = 3;

    // Declarations
    private static $sameSitePolicy = self::SAMESITE_NONE;
    
    // Private functions
    private static function getSameSitePolicyName(?int $sameSitePolicy) {
      if (!$sameSitePolicy) $sameSitePolicy = self::$sameSitePolicy;

      switch ($sameSitePolicy) {
        case self::SAMESITE_LAX:
          $returnValue = 'Lax';
          break;
        
        case self::SAMESITE_STRICT:
          $returnValue = 'Strict';
          break;
        
        default:
          $returnValue = 'None';
          break;
      }
      
      return $returnValue;
    }
    
    private static function makeCookieOptions(int $expiryTimestamp, string $path, ?int $sameSitePolicy): array {
      return array(
                  'expires'   => $expiryTimestamp,
                  'path'      => $path,
                  'samesite'  => self::getSameSitePolicyName($sameSitePolicy)
                  );
    }

    // Public functions
    public static function CreateCookie(string $name, string $value, int $lengthSeconds, string $path = '/', ?int $sameSitePolicy = null): void {
    	setcookie($name, $value, self::makeCookieOptions(time() + $lengthSeconds, $path, $sameSitePolicy));
    }

    public static function CreateCookieFromExpiryDays(string $name, string $value, int $lengthDays, string $path = '/', ?int $sameSitePolicy = null): void {
      self::CreateCookie($name, $value, $lengthDays * 86400, $path, $sameSitePolicy);
    }

    public static function ExpireCookie(string $name) {
      self::CreateCookieFromExpiryDays($name, '', -1);
    }

    public static function GetCookieValue(string $name, ?string &$value): bool {
    	$returnValue = false;

    	if (isset($_COOKIE[$name])) {
    		$value = $_COOKIE[$name];
    		$returnValue = true;
    	}

    	return $returnValue;
    }
    
    public static function SetDefaultSameSitePolicy(int $policy): void {
      if ($policy >= 1 && $policy <= 3) {
        self::$sameSitePolicy = $policy;
      } else {
        throw new \Exception("Invalid cookie SameSite policy $policy");
      }
    }
  }
}
?>