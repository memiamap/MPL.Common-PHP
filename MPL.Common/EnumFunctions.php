<?php
declare(strict_types=1);

namespace MPL\Common
{
  class EnumFunctions
  {
    // Public functions
    public static function FlagMatchAll(int $value, int ...$flags): bool {
      $returnValue = true;
  		
		  // Go through the types to find a match
		  foreach ($flags as $flag) {
		    if (($value & $flag) !== $flag) {
		      $returnValue = false;
		      break;
		    }
		  }

		  return $returnValue;
    }

    public static function FlagMatchAny(int $value, int ...$flags): bool {
      $returnValue = false;
  		
		  // Go through the types to find a match
		  foreach ($flags as $flag) {
		    if (($value & $flag) === $flag) {
		      $returnValue = true;
		      break;
		    }
		  }
		  
		  return $returnValue;
    }
  }
}
?>