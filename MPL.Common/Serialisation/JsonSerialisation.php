<?php
declare(strict_types=1);

namespace MPL\Common\Serialisation
{
  use MPL\Common\ErrorHandling;

  class JsonSerialisation
  {
    // Public functions
    public static function TryDeserialise(string $string, ?object &$out): bool {
    	$returnValue = false;

    	try {
    	  // Deserialise the string
      	$result = json_decode($string, false);
      	$errorCode = json_last_error();

      	// Check for success
      	if ($errorCode == JSON_ERROR_NONE) {
      	  // Verify an object was returned
      	  if (is_object($result)) {
        	  $out = $result;
        	  $returnValue = true;
        	} else {
        	  ErrorHandling::LogMessage('Result of JSON deserialisation was not an object');
        	}
      	} else {
      	  $errorMessage = json_last_error_msg();
      	  if (!$errorMessage) $errorMessage = 'Unknown error';
      	  ErrorHandling::LogMessage("Result of JSON deserialisation produced error $errorCode: $errorMessage");
      	}
    	} catch (\Throwable $t) {
    	  ErrorHandling::LogThrowable($t);
      }

    	return $returnValue;
    }
  }
}
?>