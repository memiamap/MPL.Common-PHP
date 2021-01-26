<?php
declare(strict_types=1);

namespace MPL\Common
{
  class StringFunctions
  {
    // Constants
    private const STRING_REPLACE_LINEBREAKS = "/\r\n|\r|\n/";
    private const STRING_REPLACE_NONALPHANUMERIC = "/[^A-Za-z0-9]/";

    // Public functions
    public static function EndsWith(string $haystack, string $needle): bool {
      return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    public static function Prefix(string $string, string $prefix): string {
      return self::StartsWith($string, $prefix) ? $string : $prefix . $string;
    }

    public static function Postfix(string $string, string $postfix): string {
      return self::EndsWith($string, $postfix) ? $string : $string . $postfix;
    }

    public static function RemoveLinebreaks(string $source, string $replacement = ''): string {
      return preg_replace(self::STRING_REPLACE_LINEBREAKS, $replacement, $source);
    }
    
    public static function StartsWith(string $haystack, string $needle): bool {
      return $haystack[0] === $needle[0] ? strncmp($haystack, $needle, strlen($needle)) === 0 : false;
    }

    public static function StripNonAlphanumeric(string $string): string {
      return preg_replace(self::STRING_REPLACE_NONALPHANUMERIC, '', $string);
    }

    public static function SurroundWith(string $string, string $surroundString): string {
      return self::Postfix(self::Prefix($string, $surroundString), $surroundString);
    }
    
    public static function TrimTo(string $string, int $length): string {
      return strlen($string) > $length ? substr($string, 0, $length) : $string;
    }
  }
}
?>