<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\Conversion;

  class ArrayFunctions
  {
    // Public functions
    public static function NonNullAndEqualLength(?array $array1, ?array $array2): bool {
      return !is_null($array1) && !is_null($array2) && count($array1) == count($array2);
    }

    public static function SearchObjectArray(array $array, string $searchProperty, string $searchValue, string $outputProperty = null, &$outputValue): bool {
      $returnValue = false;
      
      $index = array_search($searchValue, array_column($array, $searchProperty));
      if ($index !== false) {
        $returnValue = true;
        if ($outputProperty) {
          $outputValue = $array[$index]->{$outputProperty};
        } else {
          $outputValue = $array[$index];
        }
      }
      
      return $returnValue;
    }

    public static function TryParseElementAsBoolean(array $array, string $element, ?bool &$value): bool {
      $returnValue = false;
      
			if (isset($array[$element])) {
        if (Conversion::TryParseBoolean($array[$element], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseIndexAsBoolean(array $array, int $index, ?bool &$value): bool {
      $returnValue = false;
      
      if (count($array) >= $index) {
        if (Conversion::TryParseBoolean($array[$index], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseElementAsFloat(array $array, string $element, ?float &$value): bool {
      $returnValue = false;
      
			if (isset($array[$element])) {
        if (Conversion::TryParseFloat($array[$element], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseIndexAsFloat(array $array, int $index, ?float &$value): bool {
      $returnValue = false;
      
      if (count($array) >= $index) {
        if (Conversion::TryParseFloat($array[$index], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseElementAsInteger(array $array, string $element, ?int &$value): bool {
      $returnValue = false;
      
			if (isset($array[$element])) {
        if (Conversion::TryParseInteger($array[$element], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseIndexAsInteger(array $array, int $index, ?int &$value): bool {
      $returnValue = false;
      
      if (count($array) >= $index) {
        if (Conversion::TryParseInteger($array[$index], $outValue)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseElementAsString(array $array, string $element, ?string &$value, ?int $minimumSize = null, ?int $maximumSize = null): bool {
      $returnValue = false;

			if (isset($array[$element])) {
        if (Conversion::TryParseString($array[$element], $outValue, $minimumSize, $maximumSize)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }

    public static function TryParseIndexAsString(array $array, int $index, ?string &$value, ?int $minimumSize = null, ?int $maximumSize = null): bool {
      $returnValue = false;
      
      if (count($array) >= $index) {
        if (Conversion::TryParseString($array[$index], $outValue, $minimumSize, $maximumSize)) {
          $value = $outValue;
          $returnValue = true;
        }
      }
      
      return $returnValue;
    }
  }
}
?>