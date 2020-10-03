<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Conversion
  {
    // Public functions
    public static function ParseArray($data): array {
      if (!self::TryParseBoolean($data, $returnValue)) {
        throw new \Exception('The specified data is not a valid array');
      }
      
      return $returnValue;
    }

    public static function ParseBoolean($data): bool {
      if (!self::TryParseBoolean($data, $returnValue)) {
        throw new \Exception('The specified data is not a valid boolean');
      }
      
      return $returnValue;
    }

    public static function ParseFloat($data): float {
      if (!self::TryParseFloat($data, $returnValue)) {
        throw new \Exception('The specified data is not a valid float');
      }
      
      return $returnValue;
    }

    public static function ParseInteger($data): int {
      if (!self::TryParseInteger($data, $returnValue)) {
        throw new \Exception('The specified data is not a valid integer');
      }
      
      return $returnValue;
    }

    public static function ParseString($data): string {
      if (!self::TryParseString($data, $returnValue)) {
        throw new \Exception('The specified data is not a valid string');
      }
      
      return $returnValue;
    }

    public static function TryParseArray($data, ?array &$value, bool $failOnEmptyArray = true): bool {
      $returnValue = false;
      
      if (self::TryParseString($data, $stringValue)) {
        parse_str($data, $array);
        if (is_array($array)) {
          if (count($array) > 0 || !$failOnEmptyArray) {
            $value = $array;
            $returnValue = true;
          }
        }
      }
      
      return $returnValue;
    }

    public static function TryParseBoolean($data, ?bool &$value): bool {
      $returnValue = false;
      
      if (self::TryParseInteger($data, $intValue)) {
        $returnValue = true;
        
        if ($intValue === 0) {
          $value = false;
        } else {
          $value = true;
        }
      } else if (self::TryParseString($data, $strValue)) {
        switch (strtolower($strValue)) {
          case 'false':
          case 'n':
          case 'no':
            $returnValue = true;
            $value = false;
            break;
          
          case 'true':
          case 'y':
          case 'yes':
            $returnValue = true;
            $value = true;
            break;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseFloat($data, ?float &$value): bool {
      $returnValue = false;

      if (is_float($data) || is_integer($data)) {
        $returnValue = true;
      } else if (is_string($data)) {
        $data = trim($data);

        // Check for empty string
        if (strlen($data) > 0) {
          // Check for standard number stored as string
          if (ctype_digit($data)) {
            $returnValue = true;
          } else {
            $temp = $data;

            // Look for leading negative symbol
            if (substr($data, 0, 1) == '-') {
              $temp = substr($data, 1);
            }

            // Look for single decimal separator
            if (substr_count($temp, '.') == 1) {
              $temp = str_replace('.', '', $temp);
            }

            // Check whether the string is now a valid number
            if (ctype_digit($temp)) {
              $returnValue = true;
            }
          }
        }
      }

      if ($returnValue) {
        $value = (float)$data;
      }

      return $returnValue;
    }

    public static function TryParseInteger($data, ?int &$value): bool {
      $returnValue = false;
      
      if (is_integer($data)) {
        $returnValue = true;
      } else if (is_string($data)) {
        $data = trim($data);
        
        // Check for empty string
        if (strlen($data) > 0) {
          // Check for standard number stored as string
          if (ctype_digit($data)) {
            $returnValue = true;
          } else {
            // Look for leading negative symbol
            if (substr($data, 0, 1) == '-') {
              if (ctype_digit(substr($data, 1))) {
                $returnValue = true;
              }
            }
          }
        }
      }

      if ($returnValue) {
        $value = (integer)$data;
      }
      
      return $returnValue;
    }

    public static function TryParseString($data, ?string &$value, ?int $minimumSize = null, ?int $maximumSize = null): bool {
      $returnValue = false;

      if (is_scalar($data)) {
        $length = strlen((string)$data);
        $isValid = true;

        if ($minimumSize && $length < $minimumSize) {
          $isValid = false;
        }

        if ($isValid && $maximumSize && $length > $maximumSize) {
          $isValid = false;
        }

        if ($isValid) {
          $value = (string)$data;
          $returnValue = true;
        }
      }

      return $returnValue;      
    }
  }
}
?>