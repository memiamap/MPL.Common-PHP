<?php
declare(strict_types=1);

namespace MPL\Common\Reflection
{
  use MPL\Common\ErrorHandling;
  use MPL\Common\RelativeMapping;

  class ReflectionFunctions
  {
    // Public functions
    public static function IsClassInheritedFrom(string $className, string $baseClassName): bool {
      $returnValue = false;

      if (class_exists($className)) {
        if (class_exists($baseClassName)) {
          try {
            $class = new \ReflectionClass($className);
            while ($class) {
              if ($class->getName() === $baseClassName) {
                $returnValue = true;
                break;
              } else {
                $class = $class->getParentClass();
              }
            }
          } catch (\Throwable $t) {
            ErrorHandling::LogThrowable($t);
            throw new \Exception("Unable to determine whether '{$className}' inherits from '{$baseClassName}'");
          }
        } else {
          throw new \Exception("The specified base class '{$baseClassName}' is not defined");
        }
      } else {
        throw new \Exception("The specified class '{$className}' is not defined");
      }
      
      return $returnValue;
    }
  }
}
?>