<?php
declare(strict_types=1);

namespace MPL\Common\Configuration
{
  class AppSettings
  {
    // Declarations
    private static $Default = null;

    // Public functions
    public static function Default(): ConfigurationFile {
      if (!self::$Default) {
        self::$Default = ConfigurationFile::LoadFromFile('settings.config');
      }
      
      return self::$Default;
    }
  }
}
?>