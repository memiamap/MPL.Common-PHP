<?php
declare(strict_types=1);

namespace MPL\Common
{
  use MPL\Common\Events\EventWrapper;
  use MPL\Common\Reflection\{CallableFunctions, ReflectionFunctions, TypeCreator};

  class ControlRequestHandler
  {
    private ?string $baseLocation;
    private ?string $namespace;
    private EventWrapper $onLoadControlHandler;

    // Constructors
    public function __construct(string $baseLocation, ?string $namespace = null) {
      $this->baseLocation = '~' . ltrim(rtrim($baseLocation, '\\/'), '~') . '/';
      $this->namespace = StringFunctions::SurroundWith($namespace, '\\');

      // Default event handlers
      $this->onLoadControlHandler = EventWrapper::ReturnBooleanDefaultEventWrapper(true, null);
    }
    
    // Private functions
    private function tryLoadControl(string $name, string $target, ?ControlBase &$control): bool {
      $returnValue = false;

      // Defaults
      $control = null;

      try {
        // Try to create an instance of the control
        require $target;
        $targetType = "{$this->namespace}{$name}";
        $createdType = TypeCreator::CreateType($target, $targetType);
        if ($createdType) {
          // Verify the page is a valid type
          if ($createdType instanceof ControlBase) {
            // Call the control loaded event for the control
            if ($this->onLoadControlHandler->Invoke($createdType)) {
              $control = $createdType;
              $returnValue = true;
            } else {
              // Control could not be loaded
              ErrorHandling::LogMessage("Could not load control '{$name}' due to failure from onLoadControl");
            }
          } else {
            throw new \Exception("The class '{$targetType}' is not a type of ControlBase and cannot be executed");
          }
        } else {
          throw new \Exception("The class '{$targetType}' does not exist in target file '{$target}'");
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, "Unable to load control '{$name}'");
        throw new \Exception("Unable to load control '{$name}'", 0, $t);
      }

      return $returnValue;
    }
    
    private function verifyCallableEventHandler(?callable $callable, bool $throwExceptionOnInvalid = true): EventWrapper {
      $returnValue = null;

      if ($callable) {
        if (CallableFunctions::HasParameterCount($callable, 1) &&
            ReflectionFunctions::IsTypeInheritedFrom(CallableFunctions::GetParameterType($callable, 0), ControlBase::class)) {
          $returnValue = new EventWrapper($callable);
        } else if ($throwExceptionOnInvalid) {
          throw new \Exception("The specified callback is invalid");
        }
      } else {
        $returnValue = EventWrapper::UnboundEventWrapper();
      }

      return $returnValue;
    }

    // Public functions
    public function GetControlOutput(string $name, RequestPage $requestPage): string {
      $returnValue = null;

      try {
        $control = $this->LoadControl($name, $requestPage);
        if ($control) {
          $returnValue = $control->GetOutput();
        } else {
          throw new \Exception("The control '{$name}' could not be loaded");
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, "Unable to get output for requested control");
        throw new \Exception("Unable to get output for requested control", 0, $t);
      }

      return $returnValue;
    }
    
    public function LoadControl(string $name, RequestPage $requestPage): ?ControlBase {
      $returnValue = null;

      try {
        // Check the control file exists
        $controlPath = RelativeMapping::MapRelativePath("{$this->baseLocation}{$name}.php");
        if (file_exists($controlPath)) {
          // Try to load the control
          if ($this->tryLoadControl($name, $controlPath, $loadedControl)) {
            $loadedControl->SetParent($requestPage);
            $returnValue = $loadedControl;
          } else {
            throw new \Exception("The specified control {$name} could not be created");
          }
        } else {
          // Control does not exist
          throw new \Exception("The specified control {$name} does not exist");
        }
      } catch (\Throwable $t) {
        ErrorHandling::LogThrowable($t, "Unable to load requested control");
        throw new \Exception("Unable to load requested control", 0, $t);
      }

      return $returnValue;
    }

    public function SetOnLoadControl(?callable $callable): void {
      $this->onLoadControlHandler = $this->verifyCallableEventHandler($callable);
    }
  }
}
?>