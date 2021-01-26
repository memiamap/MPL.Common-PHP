<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Environment
  {
    // Decalartions
    private static $isConsole;
    private static $isInitialised = false;
    public static $NewLine;
    
    // Public functions
    public static function __init() {
      if (!self::$isInitialised) {
        // Determine runtime environment
        $sapiName = php_sapi_name();
        if (strlen($sapiName) >= 3 && strtolower(substr($sapiName, 0, 3)) == 'cli') {
          // Console environment
          self::$isConsole = true;
          self::$NewLine = '\n';
        } else {
          // Web environment
          self::$isConsole = false;
          self::$NewLine = '<br />';
        }

        self::$isInitialised = true;
      }
    }
    
    public static function NewLine(int $count = 1) : string {
      return str_repeat(self::$NewLine, $count);
    }
  }
  
  Environment::__init();
}
?>