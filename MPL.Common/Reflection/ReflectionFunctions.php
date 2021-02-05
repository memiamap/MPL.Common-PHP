<?php
declare(strict_types=1);

namespace MPL\Common\Reflection
{
  use MPL\Common\ErrorHandling;
  use MPL\Common\RelativeMapping;

  class ReflectionFunctions
  {
    // Private functions
    public static function isClassInheritedFrom(string $typeName, string $baseTypeName): bool {
      $returnValue = false;
      
      try {
        $class = new \ReflectionClass($typeName);
        while ($class) {
          if ($class->getName() === $baseTypeName) {
            $returnValue = true;
            break;
          } else {
            $class = $class->getParentClass();
          }
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t);
        throw new \Exception("Unable to determine whether '{$typeName}' inherits from '{$baseClassName}'");
      }

      return $returnValue;
    }

    // Public functions
    public static function GetShortClassName(?object $item): string {
      $returnValue = null;
      
      if ($item) {
        $rc = new \ReflectionClass($item);
        $returnValue = $rc->getShortName();
      }
      
      return $returnValue;
    }

    public static function IsTypeInheritedFrom(string $typeName, string $baseTypeName): bool {
      $returnValue = false;

      if (class_exists($typeName)) {
        if (class_exists($baseTypeName)) {
          $returnValue = self::isClassInheritedFrom($typeName, $baseTypeName);
        } else {
          throw new \Exception("The specified base type '{$baseTypeName}' is not defined");
        }
      } else {
        // Check for scalar type comparison (i.e. type == baseType)
        if (self::IsScalarType($typeName)) {
          if (self::IsScalarType($baseTypeName)) {
            if ($typeName == $baseTypeName) {
              $returnValue = true;
            }
          } else {
            throw new \Exception("The specified base type '{$baseTypeName}' is not defined");
          }
        } else {
          throw new \Exception("The specified type '{$typeName}' is not defined");
        }
      }
      
      return $returnValue;
    }
    
    public static function IsScalarType(string $typeName): bool {
      $returnValue = false;

      try {
        $var = null;
        settype($var, $typeName);
        $returnValue = true;
      } catch (\Throwable $t) {
      }
      
      return $returnValue;
    }
  }
}
?>