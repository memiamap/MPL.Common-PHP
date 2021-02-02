<?php
declare(strict_types=1);

namespace MPL\Common\Events
{
  use MPL\Common\Reflection\CallableFunctions;

  class EventWrapper
  {
    // Decalartions
    private $callable = null;
    private bool $hasCallable = false;
    private bool $hasReturnValue = false;
    private int $parameterCount = 0;

    // Constructors
    public function __construct(?callable $callable) {
      if ($callable) {
        $this->callable = $callable;
        $this->hasCallable = true;
        $this->hasReturnValue = CallableFunctions::HasReturnValue($callable);
        $this->parameterCount = CallableFunctions::GetParameterCount($callable);
      }
    }
    
    // Public functions
    public function Invoke(... $params) {
      $returnValue = null;
      
      // Check a callable was specified (not unbound event wrapper)
      if ($this->hasCallable) {
        $paramCount = $params ? count($params) : 0;

        // Check for zero-parameter call
        if ($this->parameterCount == 0) {
          // Check that no parameters were specified
          if ($paramCount == 0) {
            if ($this->hasReturnValue) {
              $returnValue = ($this->callable)();
            } else {
              ($this->callable)();
            }
          } else {
            throw new \Exception("The event supports no parameters but {$paramCount} parameters were specified");
          }
        } else {
          if ($paramCount == $this->parameterCount) {
            if ($this->hasReturnValue) {
              $returnValue = ($this->callable)(...$params);
            } else {
              ($this->callable)(...$params);
            }
          } else {
            throw new \Exception("The event supports {$this->parameterCount} parameters but {$paramCount} parameters were specified");
          }
        }
      }
      
      // Check whether to return a result
      if ($this->hasReturnValue) {
        return $returnValue;
      }
    }
    
    public static function UnboundEventWrapper(): EventWrapper {
      return new EventWrapper(null);
    }
  }
}
?>