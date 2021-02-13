<?php
declare(strict_types=1);

namespace MPL\Common\Reflection
{
  use MPL\Common\ErrorHandling;
  use MPL\Common\RelativeMapping;

  class TypeCreator
  {
    // Public functions
    public static function CreateType(string $fileName, string $className): ?object {
      $isValid = true;
      $returnValue = null;

      try {
        if (file_exists($fileName)) {
      	  // Load the assembly from the filename
      	  require_once $fileName;
      	} else {
      	  throw new \Exception("The specified file '{$fileName}' does not exist");
      	}
      } catch (\Throwable $t) {
			  ErrorHandling::LogThrowable($t, 'Load assembly failed');
			  throw new \Exception("Unable to load relative file '{$fileName}'");
      }
      
      if (class_exists($className)) {
        try {
          // Create an instance of the type
      	  $returnValue = new $className();
        } catch (\Throwable $t) {
  			  ErrorHandling::LogThrowable($t, 'Create type failed');        
  			  throw new \Exception("Unable to create instance of class '{$className}'");
        }
      } else {
			  throw new \Exception("The class '{$className}' does not exist");
      }

      return $returnValue;
    }

    public static function CreateTypeRelative(string $fileName, string $className): ?object {
      $isValid = true;
      $returnValue = null;

      try {
    	  // Load the assembly from the filename
    	  RelativeMapping::RelativeRequire($fileName);
      } catch (\Throwable $t) {
			  ErrorHandling::LogThrowable($t, 'Load assembly failed');
			  throw new \Exception("Unable to load relative file '{$fileName}'");
      }
      
      if (class_exists($className)) {
        try {
          // Create an instance of the type
      	  $returnValue = new $className();
        } catch (\Throwable $t) {
  			  ErrorHandling::LogThrowable($t, 'Create type failed');        
  			  throw new \Exception("Unable to create instance of class '{$className}'");
        }
      } else {
			  throw new \Exception("The class '{$className}' does not exist");
      }

      return $returnValue;
    }

    public static function TryCreateType(string $fileName, string $className, ?object &$output): bool {
      $returnValue = false;
      
      try {
        $output = self::CreateType($fileName, $className);
        $returnValue = true;
      } catch (\Throwable $t) {
      }
      
      return $returnValue;
    }

    public static function TryCreateTypeRelative(string $fileName, string $className, ?object &$output): bool {
      $returnValue = false;
      
      try {
        $output = self::CreateTypeRelative($fileName, $className);
        $returnValue = true;
      } catch (\Throwable $t) {
        var_dump('XXX');
      }
      
      return $returnValue;
    }
  }
}
?>