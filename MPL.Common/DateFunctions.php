<?php
declare(strict_types=1);

namespace MPL\Common
{
  class DateFunctions
  {
    // Public functions
    public static function IsValidDate(?string $date): bool {
      return $date ? (bool)strtotime($date) : false;
    }
  }
}
?>