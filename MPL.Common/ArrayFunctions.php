<?php
declare(strict_types=1);

namespace MPL\Common
{
  class ArrayFunctions
  {
    // Public functions
    public static function NonNullAndEqualLength(?array $array1, ?array $array2): bool {
      return !is_null($array1) && !is_null($array2) && count($array1) == count($array2);

    }
  }
}
?>