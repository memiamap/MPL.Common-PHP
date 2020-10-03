<?php
declare(strict_types=1);

namespace MPL\Common
{
  class Random
  {
    // Public functions
    public static function HexString(int $length): string {
      $returnValue = null;
      
      if ($length > 0) {
        $length = ($length < 4) ? 4 : $length;
        $returnValue = bin2hex(random_bytes(($length - ($length % 2)) / 2));
      } else {
        throw new \Exception("The specified length of $length is invalid");
      }
      
      return $returnValue;
    }
  }
}
?>