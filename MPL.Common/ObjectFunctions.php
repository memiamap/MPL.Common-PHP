<?php
declare(strict_types=1);

namespace MPL\Common
{
  class ObjectFunctions
  {
    // Public functions
    public static function ValidateProperties(object $object, string ...$properties): bool {
  		$returnValue = true;

      // Verify each property
      foreach ($properties as $property) {
        if (!property_exists($object, $property)) {
          $returnValue = false;
          break;
        }
      }

    	return $returnValue;
    }
  }
}
?>