<?php
declare(strict_types=1);

namespace MPL\Common\Reflection
{
  use MPL\Common\ErrorHandling;
  use MPL\Common\RelativeMapping;

  class TypeCreator
  {
    // Public functions
    public static function CreateTypeRelative(string $fileName, string $className): ?object {
      $isValid = true;
      $returnValue = null;

      try {
    	  // Load the assembly from the filename
    	  RelativeMapping::RelativeRequire($fileName);
      } catch (\Throwable $t) {
			  ErrorHandling::LogThrowable($t, 'Load assembly failed');
			  throw new \Exception("Unable to load relative file $fileName");
      }
      
      try {
        // Create an instance of the type
    	  $returnValue = new $className();
      } catch (\Throwable $t) {
			  ErrorHandling::LogThrowable($t, 'Create type failed');        
			  throw new \Exception("Unable to create instance of class $className");
      }
      
      return $returnValue;
    }
  }
}
?>