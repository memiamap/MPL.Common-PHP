<?php
declare(strict_types=1);

namespace MPL\Common\Reflection
{
  use MPL\Common\ErrorHandling;
  use MPL\Common\RelativeMapping;

  class CallableFunctions
  {
    // Public functions
    public static function GetParameterCount(callable $callable): int {
      $returnValue = 0;

      $rf = new \ReflectionFunction($callable);
      $params = $rf->getParameters();
      if (is_array($params)) {
        $returnValue = count($params);
      }

      return $returnValue;
    }

    public static function GetParameterType(callable $callable, int $parameterNumber): string {
      $returnValue = null;
      
      if (self::HasParameterCount($callable, $parameterNumber + 1)) {
        $rf = new \ReflectionFunction($callable);
        $params = $rf->getParameters();
        $returnValue = $params[$parameterNumber]->getType()->getName();
      } else {
        throw new \Exception("The specified callable does not have a parameter at position $parameterNumber");
      }
      
      return $returnValue;
    }
    
    public static function HasParameterCount(callable $callable, int $parameterCount, bool $exactCount = false): bool {
      $returnValue = false;

      $rf = new \ReflectionFunction($callable);
      $params = $rf->getParameters();
      if (is_array($params) &&
          ($exactCount && count($params) == $parameterCount) ||
          count($params) >= $parameterCount) {
        $returnValue = true;
      }
      
      return $returnValue;
    }
    
    public static function HasReturnValue(callable $callable): bool {
      $returnValue = false;

      $rf = new \ReflectionFunction($callable);
      $returnType = $rf->getReturnType();
      if ($returnType) {
        $returnValue = true;
      }
      
      return $returnValue;

    }
  }
}
?>