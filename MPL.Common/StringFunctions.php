<?php
declare(strict_types=1);

namespace MPL\Common
{
  class StringFunctions
  {
    // Constants
    private const STRING_REPLACE_LINEBREAKS = "/\r\n|\r|\n/";
    
    // Public functions
    public static function RemoveLinebreaks(string $source, string $replacement = ''): string {
      return preg_replace(self::STRING_REPLACE_LINEBREAKS, $replacement, $source);
    }
  }
}
?>